<?php

namespace App;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'password', 'remember_token', 'img', 'pinterest_url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function authenticate(\DirkGroenen\Pinterest\Models\User $user, $token = null)
    {
        //check if user exists in DB
        $db_user = User::getProfile();
        //try Login the user
        if(is_null($db_user)) {
            //register the user
            $new_user = new self;
            $new_user->name = $user->first_name;
            $new_user->email = $user->id . "@pinterest.com";
            $new_user->password = $user->id;
            $new_user->remember_token = $token;
            $new_user->img = $user->image["60x60"]['url'];
            $new_user->save();
            //Authenticate
            Auth::loginUsingId($new_user->id);
            //Redirect
            return \redirect('/login');
        }else {
            try {
                $db_user->remember_token = $token;
                $db_user->img = $user->image["60x60"]['url'];
                $db_user->pinterest_url = 'https://www.pinterest.com/'.$user->first_name;
                $db_user->save();

                return \redirect('/pinterest/boards');
                if (Auth::loginUsingId($db_user->id)) {
                    //check if user configured
                    if(IO::get('active_page', new Credential, 'get', ['active_page'], false)) {
                        //redirect
                        return \redirect('/pinterest/boards');
                    }
                    //redirect
                    return \redirect('/pinterest/boards');
                }
            }catch (AuthenticationException $exception) {
                echo $exception->getMessage();
            }

        }
    }


    /**
     * @return User
     */
    public static function getProfile() {
        $user = self::where('email',Auth::user()->email)->first();
        return $user;
    }

}
