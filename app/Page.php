<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Page extends Model
{
    protected $table = 'pages';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'name', 'token', 'active', 'page_id'];
    public $timestamps = false;

    CONST ACTIVE = 1;
    CONST INACTIVE = 0;

    public static function set($page_id, $page_name, $page_access_token)
    {
        self::updateOrCreate(
            ['page_id' => $page_id, 'user_id' => Auth::id()],
            [
                'name' => $page_name,
                'token' => $page_access_token,
                'user_id' => Auth::id(),
                'active' => 0
            ]);
        return true;
    }

    public static function setAll(Array $pages)
    {
        foreach ($pages as $page) {
            self::updateOrCreate(['page_id' => $page['id'], 'user_id' => Auth::id()],
                [
                    'name' => $page['name'],
                    'token' => $page['access_token'],
                    'user_id' => Auth::id(),
                    'page_id' => $page['id'],
                    'active' => 0
                ]);
        }
        return true;
    }

    public static function activate($page_id)
    {
        return self::where(['user_id' => Auth::id(),'page_id' => $page_id ])->update(['active' => self::ACTIVE ]);
    }

    public static function deactivate($page_id)
    {
        return self::where(['user_id' => Auth::id()])
            ->whereIn('page_id' , $page_id)
            ->update(['active' => self::INACTIVE ]);
    }

    public static function getPageToken($page_id, $user_id = null)
    {
        $value = self::where(['page_id' => $page_id, 'user_id' => !is_null($user_id) ? $user_id : Auth::id()])->first(['token']);
        return !is_null($value) ? $value->token : null;
    }

    public static function setPageToken($page_id, $access_token)
    {
        return self::where(['page_id' => $page_id, 'user_id' => Auth::id()])->update(['token' => $access_token ]);
    }

    public static function myPages($active = true)
    {
        $params = ['user_id' => Auth::id()];

        if ($active) {
            $params['active'] = 1;
        }

        return self::where($params)
            ->select('id', 'page_id', 'name')
            ->get()
            ->keyBy('page_id')
            ->toArray();
    }
}

