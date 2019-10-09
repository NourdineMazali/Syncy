<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Login;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function authenticate(Login $request) {

        try {

            if(Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {
                $response = ['message' => 'success', 'status' => 200];
            }else {
                $response = ['message' => 'Your credentials are incorrect! try again.', 'status' => 400];
            }

            return response()->json($response['message'], $response['status']);
        }
        catch(AuthenticationException $exception) {
            return response()->json($exception->getMessage(), 400);
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return Redirect::to('/')->send();

    }
}
