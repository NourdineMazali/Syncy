<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/pins', 'CacheController@getPins');
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/', 'Controller@welcome');
Route::get('/dashboard', 'Controller@dashboard');

//Unauthenticated user actions
Route::get('/login', 'Controller@welcome');
Route::post('/login', 'Auth\LoginController@authenticate');
Route::get('/register', 'Controller@welcome');
Route::post('/register', 'Auth\RegisterController@register');
Route::get('/contact', 'Controller@welcome');
Route::post('/contact', 'Controller@contact');

//Vue js routing
Route::get('/profile', 'Controller@dashboard');
Route::get('/pinterest/boards', 'Controller@dashboard');
Route::get('/facebook/pages', 'Controller@dashboard');
Route::get('/schedule', 'Controller@dashboard');
Route::get('/done', 'Controller@dashboard');
Route::get('/pin/{id}', 'Controller@dashboard');
Route::get('/timeline/share', 'Controller@dashboard');
Route::get('/share/{id}', 'Controller@dashboard');
Route::get('/instagram/share', 'Controller@dashboard');
Route::get('/instagram/register', 'Controller@dashboard');
Route::get('/instagram', 'Controller@dashboard');

// Endpoint that is redirected to after an authentication attempt
Route::get('/pinterest/authenticate', 'OAuthController@handlePinterestCall');
Route::get('/pinterest/connect', 'Controller@connectToPinterestStep');
Route::get('/pinterest/authorize', function () {
    $pinterest = new DirkGroenen\Pinterest\Pinterest(env('PINTEREST_CLIENT_ID'), env('PINTEREST_CLIENT_SECRET'));
    $url = $pinterest->auth->getLoginUrl(url('/pinterest/authenticate'), array('read_public', 'write_public'));
    return view('pinterest.connect', ['url' => $url]);
});
Route::get('/pinterest/boards/get', 'Controller@getPinterestBoards');
Route::post('/pinterest/save', 'Controller@savePinterestBoard');
Route::get('/pinterest/switch/{id}', 'OAuthController@switchBoard');

//Facebook routes and end points
Route::get('/facebook/configure', 'Controller@configureFacebookPages');
Route::post('/facebook/configure', 'Controller@simpleFacebookConfig');
Route::get('/facebook/authorize', 'Controller@loginToFacebook');
Route::get('/facebook', 'Controller@publishToFacebook');
Route::get('/facebook/login', function (SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb) {
    return redirect($fb->getLoginUrl(['email','pages_show_list','manage_pages','publish_pages','publish_actions']));
});
Route::get('/facebook/callback', 'OAuthController@handleFacebookCallback');
Route::get('/facebook/switch/{id}', 'OAuthController@switchFBP');

Route::get('/instagram/configure', 'Controller@configureInstagram');
Route::post('/instagram/store', 'Controller@storeInstagramCredentials');
Route::get('/process', 'Controller@process');
Route::get('/synchronize', 'OAuthController@synchronize');
Route::post('/share', 'Controller@postToPage');
Route::post('/postToTimeline', 'Controller@postToTimeline');
//AUTH

// API
Route::get('/pages/get', 'Controller@configureFacebookPages');
Route::get('/boards/get', 'Controller@getPinterestBoards');
Route::get('/fb', 'OAuthController@getFacebook');
Route::post('/timeline/post', 'Controller@postToTimeline');
Route::get('/user/details/get', 'Controller@getProfileDetails');
Route::get('/image', 'Controller@getImage');
Route::post('/instagram/share', 'OAuthController@shareToInstagram');
Route::get('/instagram/account', 'Controller@getInstagramAccount');
Route::post('/instagram/connect', 'Controller@saveInstagramAccountDetails');
Route::get('/instagram/disconnect', 'Controller@disconnectInstagram');
