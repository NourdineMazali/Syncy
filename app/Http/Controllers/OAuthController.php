<?php
/**
 * Created by PhpStorm.
 * User: nourdine
 * Date: 22/6/17
 * Time: 11:38 PM
 */

namespace App\Http\Controllers;

use App\Credential;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use App;
use App\IO;
use App\Pin;
use App\User;
use App\Page;

/* 3rd parties */
use DirkGroenen\Pinterest\Pinterest;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use InstagramAPI\Instagram;

class OAuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $pinterest;

    /**
     * Determines the privacy settings of the post. If not supplied, this defaults to the privacy level granted to the app in the Login Dialog. This field cannot be used to set a more open privacy setting than the one granted.
     *
     * @var array
     */
    public $privacy = [
        'value' => 'enum{\'SELF\'}', /**enum{'EVERYONE', 'ALL_FRIENDS', 'FRIENDS_OF_FRIENDS', 'CUSTOM', 'SELF'}**/
        'allow' => '',
        'deny' => ''
    ];

    public function switchFBP($id, Request $request)
    {

        if (is_null($id)) {
            return Redirect::to("/dashboard")->send();
        }

        IO::set('active_page', $id, false,
            function () use ($id) {
                Credential::set(['active_page' => $id]);
            }
        );

        #Update Homepage Cache
        CacheController::refreshDashboardView();

        return Redirect::to("/dashboard")
            ->with('message', '<div class="alert alert-success" role="alert">The target Facebook page has been changed successfully!</div>')
            ->send();
    }

    public function switchBoard($id, Request $request)
    {

        if (is_null($id)) {
            return Redirect::to("/dashboard")->send();
        }


        IO::set('active_board', $id, false,
            function () use ($id) {
                Credential::set(['active_board' => $id]);
            }
        );

        #Synchronize the latest pins
        self::synchronize($request);

        return Redirect::to("/dashboard")
            ->with('message', '<div class="alert alert-success" role="alert">The latest pins has been imported successfully!</div>')
            ->send();

    }

    /**
     * @param Request $request
     * @return Pinterest
     */
    public static function connectToPinterest(Request $request)
    {
        $pinterest = new Pinterest(env('PINTEREST_CLIENT_ID'), env('PINTEREST_CLIENT_SECRET'));
        $token = IO::get('pinterest_user_access_token_', new Credential(), 'get', ['pinterest_user_access_token_']);
        $pinterest->auth->setOAuthToken($token);
        return $pinterest;
    }

    public static function getAuthUrlPinterest() {
        $pinterest = new Pinterest(env('PINTEREST_CLIENT_ID'), env('PINTEREST_CLIENT_SECRET'));
        return $pinterest->auth->getLoginUrl(url('/pinterest/authenticate'), array('read_public', 'write_public'));
    }

    public static function synchronize(Request $request, $board_id = null, $is_ajax = false)
    {
        //Destroy all pins => internal use Only
        if ($request->forced) Pin::destroyMyPins();

//        try {
            $pinterest = self::connectToPinterest($request);
            $board_id = is_null($board_id) ?
                IO::get('active_board', new Credential, "get", ['active_board']) : $board_id;
            $pins = self::getMeBoardPins($board_id,
                ['fields' => 'link,board,note,color,counts,media,attribution,image[original,large,small],metadata'],$pinterest );
//        } catch (\Exception $exception) {
//            return response()->json(['error' => $exception->getMessage()], JsonResponse::HTTP_FORBIDDEN);
//        }
        $db_pins = Pin::myPinsByKey();

        foreach ($pins as $_pin) {
            if (!isset($db_pins[$_pin->id])) {
                // Saving New pin in DB
                $db_pin = new Pin();
                $db_pin->pin_id = $_pin->id;
                $db_pin->caption = $_pin->note;
                $db_pin->user_id = Auth::id();
                $db_pin->board_id = $board_id;
                $db_pin->image_src = json_encode($_pin->image);
                $db_pin->thumbnail = $_pin->image['large']['url'];
                $db_pin->save();
            }
        }

        #Save to Redis
        Redis::set('pins:'. $board_id .':'. Auth::id(), \GuzzleHttp\json_encode(Pin::myPins()));

        #Update Homepage Cache
        CacheController::refreshDashboardView();

        if($is_ajax) {
            return true;
        }

        return response()->json(['message' => 'The dashboard has been synchronized successfully!']);
    }

    public function handlePinterestCall(Request $request)
    {
        $this->pinterest = new Pinterest(env('PINTEREST_CLIENT_ID'), env('PINTEREST_CLIENT_SECRET'));

        $token = IO::get('pinterest_user_access_token_', new Credential(), 'get', ['pinterest_user_access_token_']);

        //Connect to Pinterest, Instagram and Facebook
        if ($request->get('code')) {

            $token = $this->pinterest->auth->getOAuthToken($request->get('code'));

            $token = (string) $token->access_token;
            //WTFFFFFFF
            IO::set('pinterest_user_access_token_', $token, false, function () use ($token) {
                Credential::set(['pinterest_user_access_token_' => $token]);
            });

            $this->pinterest->auth->setOAuthToken($token);

            return User::authenticate($this->pinterest->users->me(['fields' => 'image, counts, id, username, first_name, last_name']), $token);

        }
        elseif (!is_null($token)) {

            $this->pinterest->auth->setOAuthToken($token);

            Auth::loginUsingId($token);

            return Redirect::to('/dashboard')->send();

        }

        //redirect
        return Redirect::to('/dashboard')->send();
    }

    public function handleFacebookCallback(LaravelFacebookSdk $fb)
    {
        // Obtain an access token.
        try {
            $token = $fb->getAccessTokenFromRedirect();

        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        if (!$token) {
            // Get the redirect helper
            $helper = $fb->getRedirectLoginHelper();

            if (!$helper->getError()) {
                abort(403, 'Unauthorized action.');
            }

            // User denied the request
            dd(
                $helper->getError(),
                $helper->getErrorCode(),
                $helper->getErrorReason(),
                $helper->getErrorDescription()
            );
        }

        if (!$token->isLongLived()) {
            // OAuth 2.0 client handler
            $oauth_client = $fb->getOAuth2Client();

            // Extend the access token.
            try {
                $token = $oauth_client->getLongLivedAccessToken($token);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                dd($e->getMessage());
            }
        }

        $fb->setDefaultAccessToken($token);

        IO::set('user_access_token', $token, false, function () use ($token) {
            Credential::set(['user_access_token' => $token]);

        });

        // Get basic info on the user from Facebook.
        try {
            $response = $fb->get('/me?fields=id,name,email');
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            dd($e->getMessage());
        }

        return \redirect('/done');
    }

    public static function connectToInstagram($email = null, $password = null)
    {
        $instagram = new Instagram(false, false);

        $instagram->setUser($email, $password);

        try {
            $instagram->login();
        } catch (\Exception $e) {
            throw $e;
        }

        return $instagram;
    }

    public function authorizeFacebook(Request $request)
    {
        $this->facebook = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $url = $this->facebook->getLoginUrl(['email', 'pages_show_list', 'manage_pages', 'publish_pages', 'publish_actions']);

        return "<a href='$url' >Connect with your Facebook account </a>";
    }

    public static function getPinterest()
    {

        $pinterest = new Pinterest(env('PINTEREST_CLIENT_ID'), env('PINTEREST_CLIENT_SECRET'));

        $pinterest->auth->setOAuthToken(Auth::user()->getRememberToken());

        return $pinterest;
    }

    public static function getFacebook($token = null, $token_required = true)
    {

        $token = is_null($token) ?
                IO::get('user_access_token', new Credential, 'get', ['user_access_token']) :
                $token;
        /* @var $facebook LaravelFacebookSdk */

        $facebook = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');

        if ($token_required) {
            $facebook->setDefaultAccessToken($token);
        }

        return $facebook;
    }

    public static function publishToFacebook($page_id, $image, $caption, $date = null, $user_id = null)
    {

        $facebook = self::getFacebook(Credential::get('user_access_token', $user_id));
        $page_access_token = Page::getPageToken($page_id, $user_id);

        try {
            /*  @var $facebook \Facebook\Facebook */
            $facebook->post("/{$page_id}/photos", [
                'url'                    => $image,
                'caption'                => $caption,
                'published'              => false,
                'scheduled_publish_time' => strtotime(!is_null($date) ? $date : Carbon::createFromFormat('d-m-Y H:i:s', 'tomorrow'))
            ], $page_access_token);

        } catch (\Facebook\Exceptions\FacebookResponseException $exception) {
            return response()->json( 'Something went wrong:' . $exception->getMessage());

        } catch (\Facebook\Exceptions\FacebookSDKException $exception) {
            return response()->json( 'Something went wrong:' . $exception->getMessage());
        }

    }

    public static function publishToAccount($image, $caption, $privacy = 'SELF', $user_id = null) {
        /*  @var $facebook \Facebook\Facebook */
        $access_token = Credential::get('user_access_token', $user_id);
        $facebook = self::getFacebook($access_token);
        try{
            $facebook->post("/me/photos", [
                'url'       => $image,
                'caption'   => $caption,
                'privacy'   => "{'value':'$privacy'}",
            ]);

        }catch (\Facebook\Exceptions\FacebookResponseException $exception) {
            throw $exception;
        }
    }

    public static function uploadtoInstagram($photoFilename, $captionText)
    {

        try {
            $credentials = IO::get('instagram_credentials', new Credential, 'get',[],true);

            if(is_null($credentials)) {
                throw new \Exception('Your Instagram account is not connected!');
            }
            $instagram = self::connectToInstagram($credentials->email, $credentials);

            $result = $instagram->uploadTimelinePhoto($photoFilename, ['caption' => $captionText]);

            $instagram->comment($result->media->id,$captionText);

        } catch (\Exception $e) {
            throw new $e;
        }
    }

    public function shareToInstagram(Request $request) {
        $input  = $request->request->all();
        try {
            $base64_string    = $input['img'];

            $data = explode( ',', $base64_string );

            $source = imagecreatefromstring(base64_decode($data[1]));
            $rotate = imagerotate($source, 0, 0); // if want to rotate the image

            $filename_path = md5(time().uniqid()).".jpg";
            $path = storage_path() . "/app/public/$filename_path";

            $imageSave = imagejpeg($rotate,$path,100);
            imagedestroy($source);

            if($imageSave) {
                self::uploadtoInstagram($path, $input['caption']);
            }
        }catch (\Exception $e) {
            return response()->json($e->getMessage());
        }

        return response()->json(true,200);

    }

    public static function getCroppedImage($image_src)
    {
        $name = basename($image_src);
        list($txt, $ext) = explode(".", $name);
        $name = $txt . time();
        $name = $name . "." . $ext;
        $path = storage_path() . "/app/public/$name";
        $upload = file_put_contents($path, file_get_contents($image_src));
        list($width, $height, $type, $attr) = getimagesize($path);

        $size = ($width >= $height) ? $height : $width;

        $file = [
            "name" => "$name",
            "type" => "image/" . $ext,
            "tmp_name" => $path,
            "error" => 0,
            "size" => $upload
        ];

        $crop = new Crop(
            "",
            '{"x":0,"y":0.059850374064837904,"height":' . $size . ',"width":' . $size . ',"rotate":0}',
            $file
        );
        return ['image' => $image_src, 'original_src' => $crop->getResult()];
    }

    /**
     * Get the authenticated user's pins
     *
     * @access public
     * @param array     $data
     * @param \DirkGroenen\Pinterest\Endpoints\Users  $pinterest
     * @throws PinterestExceptions
     * @return \DirkGroenen\Pinterest\Models\Collection
     */
    public static function getMeBoardPins($id,$data, \DirkGroenen\Pinterest\Pinterest $pinterest)
    {
        $response = $pinterest->request->get("boards/{$id}/pins", $data);
        return new \DirkGroenen\Pinterest\Models\Collection($pinterest, $response, "Pin");
    }

}