<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App;
use App\IO;
use App\Pin;
use App\Credential;
use App\Page;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;

class CacheController extends BaseController
{


    /**
     * Refresh Dashboard
     */
    public static function refreshDashboardView()
    {

        //params
        // $instagram_account = IO::get('instagram', new Credential, 'get', ['instagram'], true);
        $params = [
//            'pins'          => IO::get('pins', new Pin, 'myPins', [], true),
//            'pages'         => IO::get('active_pages', new Page, 'myPages', [], true, true),
//            'active_page'   => IO::get('active_page', new Credential, 'get', ['active_page'], false),
//            'boards'        => IO::get('active_boards', new Credential, "get", ['active_boards'], true, true),
//            'active_board'  => IO::get('active_board', new Credential, "get", ['active_board']),
//            'instagram'     => is_null($instagram_account) ? 'disabled' : '',
//            'name'          => Auth::user()->username
        ];

        IO::setViewToCache('dashboard', $params);

        return IO::getViewFromCache('dashboard');
    }

    public static function getPins () {
        //pins
        $active_board = IO::get('active_board', new Credential, "get", ['active_board']);
        $pins = IO::get('pins:'.$active_board , new Pin, 'myPins', [], true);
        //Board name
        $active_board_name = IO::get('active_board_name', new Credential, "get", ['active_board_name']);

        return response()->json(['pins' => $pins, 'board_name' => $active_board_name] );
    }
}
