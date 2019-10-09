<?php

namespace App\Http\Controllers;

use App\Credential;
use App\Http\Requests\Share;
use App\Http\Requests\StoreFacebookPages;
use App\Http\Requests\StorePinterestBoards;
use App\Http\Requests\Register;
use App\Page;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redirect;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use Illuminate\Http\Request;
use App\Jobs;
use Illuminate\Support\Facades\Auth;
use App\IO;
use App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\OAuthController as OAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Contact;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $pinterest;
    private $instagram;
    private $facebook;

    /**
     * Controller constructor.
     * @param $fb
     */

    public function __construct(LaravelFacebookSdk $fb)
    {

        //check users are connected
        $this->middleware('guest', ['except' =>
            [
                'connectToPinterest',
                'connectToFacebook',
                'handleFacebookCallback',
                'configureFacebookPages',
                'configureFacebook',
                'getPinterestBoard',
                'share',
                'savePinterestBoard',
                'getInstagramAccount',
                'saveInstagramAccountDetails'
            ]
        ]);

    }

    public function welcome()
    {
        $url = '';
        return view('welcome', ['url' => $url]);
    }

    public function connectToPinterestStep() {
        $url = OAuthController::getAuthUrlPinterest();
        return view('dashboard', ['url' => $url, 'name' => Auth::user()->username]);

    }

    public function dashboard($id = null, Request $request, Validator $validator)
    {
        //only if page id is different from existing one
        CacheController::refreshDashboardView();

        return IO::getViewFromCache('dashboard');
    }

    public function postToPage(Share $request)
    {
        $data = $request->request->all();

        //share to platforms on queue
        try {
            $job = new Jobs\SharePins($data['page_id'], $data['pin_id'], $data, Auth::id());
            dispatch($job);
        }catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 401);
        }
        return response()->json('success');

    }

    public function postToTimeline(Request $request) {

        $data = $request->request->all();

        //share to Timeline on queue
        try {
            $job = new Jobs\PostToTimeline($data, Auth::id());
            dispatch($job);
        }catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 401);
        }

        return response()->json('success');

    }

    public function configureFacebookPages(Request $request)
    {
        $pages_list = IO::get('pages', new Page(), 'myPages', [false], true);

        if (empty($pages_list)) {

            $facebook = OAuth::getFacebook();
            $pages_list = $facebook->get('/me/accounts')->getDecodedBody();

            #store in cache
            IO::set('pages', $pages_list['data'], true, function () use ($pages_list) {
                page::setAll($pages_list['data']);
            });

            return $this->configureFacebookPages($request);
        }
        $token = IO::get('user_access_token', new Credential, 'get', ['user_access_token']);
        $active_pages = IO::get('active_pages', new Page, 'myPages', [], true, true);
        $active_page  = IO::get('active_page', new Credential, 'get', ['active_page'], false);

        return response()->json(['pages' => $pages_list , 'active_pages' => $active_pages, 'activated' => $active_page, 'token' => $token]);
    }

    public function loginToFacebook () {
        $url = OAuth::getFacebook(null, false)
                    ->getLoginUrl(
                    [
                        'email',
                        'pages_show_list',
                        'manage_pages',
                        'publish_pages',
                        'publish_actions'
                    ]);
        return view('dashboard',['url' => $url, 'name' => Auth::user()->name]);
    }

    public function simpleFacebookConfig(StoreFacebookPages $request) {
        //new page
        $page = $request->request->get('page');

        #activate Page
        Io::set('active_page', $page, false, function () use ($page) {
            Credential::set(['active_page' => $page]);
        });
        return response()->json(['status' => 'success', 'message' => 'Facebook page has changed successfully!']);
    }

    public function configureFacebook(StoreFacebookPages $request, LaravelFacebookSdk $fb)
    {
        $page = $request->request->get('page');

        $pages = $request->get('pages');

        $saved_activated_pages = Page::myPages();

        //deactivate previously activated pages
        $to_deactivate = array_diff(array_keys($saved_activated_pages), array_keys($pages));
        if (!empty($to_deactivate)) {
            Page::deactivate($to_deactivate);
        }

        //activate new pages
        $to_activate = array_diff(array_keys($pages), array_keys($saved_activated_pages));

        foreach ($to_activate as $key => $id) {

            #activate page
            Page::activate($id);

            $page_access_token = IO::get($id . '_access_token', new Page(), 'getPageToken', [$id], false);
            if (is_null($page_access_token)) {

                #set user access token to get page access token
                $user_access_token = IO::get('user_access_token', new Credential, 'get', ['user_access_token']);
                $facebook = OAuth::getFacebook($user_access_token);
                $response = $facebook->get("/$id?fields=access_token");
                $response = $response->getDecodedBody();

                #set in Redis and DB
                Io::set($id . '_access_token', $response['access_token'], false, function () use ($id, $response) {
                    Page::setPageToken($id, $response['access_token']);
                });
            }
        }

        #update my activated pages on Redis
        Io::set('active_pages', Page::myPages(), true);

        #active Page
        Io::set('active_page', $request->get('active_page'), false, function () use ($request) {
            Credential::set(['active_page' => $request->get('active_page')]);
        });

        return \redirect('/dashboard')
            ->with('message', '<div class="alert alert-success" role="alert">Facebook settings has been configured successfully</div>');
    }

    public function getPinterestBoards(Request $request)
    {
        $boards = IO::get('boards', new Credential, "get", ['boards', Auth::id(), false], true);

        if (!$boards || $request->get('synchronize')) {

            //Pinterest API
            try {
                $boards = OAuth::getPinterest()
                    ->users
                    ->getMeBoards(['fields' => 'id,name,image[original,large,small]'])
                    ->toArray()['data'];
            }catch(\DirkGroenen\Pinterest\Exceptions\PinterestException $e) {
                return response()->json(['error' => $e->getMessage()],JsonResponse::HTTP_FORBIDDEN);
            }

            //all data about the boards
            IO::set('boards', $boards, true,
                function () use ($boards) {
                    Credential::set(['boards' => \GuzzleHttp\json_encode($boards)]);
                }
            );

            //for header only
            $active_boards = [];

            foreach ($boards as $board) {
                $active_boards[$board['id']] = $board['name'];
            }

            IO::set('active_boards', $active_boards, true,
                function () use ($active_boards) {
                    Credential::set(['active_boards' => \GuzzleHttp\json_encode($active_boards)]);
                }
            );

            return $this->getPinterestBoards($request);
        }

        $active_board = IO::get('active_board', new Credential(), 'get', ['active_board']);

        return response()->json(['boards' => $boards , 'active_pages' => $active_board]);
    }

    public function savePinterestBoard(StorePinterestBoards $request)
    {
        $input = $request->request->all();
        $active_board = $input['board'];

        //Save board_id
        IO::set('active_board', $active_board, false, function () use ($active_board) {
            Credential::set(['active_board' => $active_board]);
        });

        //Save board name
        $boards = IO::get('active_boards', new Credential, "get", ['active_boards'], true, true);

        $board_name = isset($boards[$active_board]) ? $boards[$active_board] : null;
        IO::set('active_board_name', $board_name, false, function () use ($board_name) {
            Credential::set(['active_board_name' => $board_name]);
        });

        $token = IO::get('user_access_token', new Credential, 'get', ['user_access_token']);

        //TODO: FIND A BETTER WAY TO HANDLE THIS
        OAuth::synchronize($request, null, true);

        return response()->json(['message' => 'Pinterest settings has been configured successfully', 'token' => $token ]);
    }

    public function configureInstagram(Request $request)
    {
        $instagram_Account = IO::get('instagram', new Credential, 'get', ['instagram'], true);
        $params = [
            'email' => !is_null($instagram_Account) ? $instagram_Account->email : null,
            'password' => !is_null($instagram_Account) ? $instagram_Account->password : null
        ];
        return view('instagram.configure')->with($params);
    }

    public function storeInstagramCredentials(Request $request)
    {
        try {
            //Test Connectivity
            OAuth::connectToInstagram($request->email, $request->password);

        } catch (\Exception $e) {

            return Redirect::back()
                ->with('message', '<div class="alert alert-danger" role="alert">Something went wrong:' . $e->getMessage() . '</div>')
                ->send();
        }
        try {

            $credentials = [
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];

            //Save
            IO::set('instagram', $credentials, true, function () use ($credentials) {
                Credential::set($credentials);
            });

            return \redirect('/dashboard')
                ->with('message', '<div class="alert alert-success" role="alert">The credentials have been Successfully tested and saved</div>');
        } catch (\Exception $e) {

            return Redirect::back()
                ->with('message', '<div class="alert alert-danger" role="alert">Something went wrong:' . $e->getMessage() . '</div>')
                ->send();
        }
    }

    public function getProfileDetails() {
        $user = App\User::getProfile();

        $pins_count = IO::get('pinsCount', new App\Pin(), 'MyPinsCount');
        $boards = IO::get('active_boards', new Credential, "get", ['active_boards'], true, true);

        return response()->json(['user' => $user, 'MyPinsCount' => $pins_count, 'boards' => $boards ]);
    }

    public function saveInstagramAccountDetails(Register $request) {
        $data = $request->all();

        try {
            //test connectivity
            $instagram = OAuth::connectToInstagram(trim($data['username']), trim($data['password']));
            //save user details
            $user_details = $instagram->getCurrentUser()->getUser();
            IO::set('instagram_user_details',$user_details, true,
                function () use ($user_details) {
                    Credential::set(['instagram_user_details' => \GuzzleHttp\json_encode($user_details)]);
                }
            );
        }catch(\Exception $exception) {
            return response()->json([$exception->getMessage().' '. $data['username'].' : '. $data['password']], 400);
        }

        $credentials = ['email' => $data['username'], 'password' => $data['password']];

        Io::set('instagram_credentials', $credentials, true,
            function () use ($credentials) {
                Credential::set(['instagram_credentials' => \GuzzleHttp\json_encode($credentials)]);
            }
        );

        return response()->json('success', 200);
    }

    public function getInstagramAccount( ) {
        $credentials = IO::get('instagram_credentials', new Credential, 'get',[],true);

        $user_details = IO::get('instagram_user_details', new Credential, 'get', [], true );
        if (is_null($user_details)) {
            return response()->json(null, 401);
        }

        return response()->json($user_details, 200);
    }

    public function disconnectInstagram() {
        try {
            IO::remove('instagram_user_details', new Credential(), 'name' );
            return response()->json(true, 200);
        }catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }
    }

    public function contact(\App\Http\Requests\Contact $request) {
        //Form data
        $data = $request->all();
        try {
            Mail::to('test@syncy.io')->send(new Contact($data));
        }
        catch (\Exception $exception) {
            return response()->json($exception->getMessage(), 400);
        }
        return response()->json(true, 200);

    }

    //todo: move to better place
    public function getImage(Request $request) {
        $url = $request->url;
        $fp = fopen($url, 'rb');
        header("Content-Type: image/png");
        fpassthru($fp);
        exit;
    }

}
