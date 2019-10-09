<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Credential;
use App\Page;
use App\IO;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //if not authenticated redirect to login
         if (!Auth::check()) {
            if (!in_array($request->getPathInfo(), ['/', '/login', '/register', '/contact'])) {
                return redirect('/');
            }
            return $next($request);
        }else {
             if (in_array($request->getPathInfo(), ['/', '/login', '/register'])) {
                 return redirect('/dashboard');
             }
                 //if all configured
             $facebook_configured = IO::get('active_page', new Credential, 'get', ['active_page'], false);
             //if user has not configured Pinterest
             $active_board = IO::get('active_board', new Credential, 'get', ['active_board'], true);

             if (!is_null($facebook_configured) && !is_null($active_board)) {
                 return $next($request);
             }
         }

        //User registered but not configured yet
        if ($request->getPathInfo() === '/pinterest/connect' && Auth::check()) {
            return $next($request);
        }

        //if user has not connected his Pinterest account to Syncy
        $pinterest_boards = IO::get('active_boards', new Credential, "get", ['active_boards'], true, true);

        if (is_null($pinterest_boards)) {
            if (in_array($request->getPathInfo(),
                ['/pinterest/boards', '/boards/get', '/pinterest/save', 'pinterest/connect'])) {
                return $next($request);
            }

            return redirect('/');
        }

        //User imported Pinterest boards can go into Pinterest related UI

        if (in_array($request->getPathInfo(), ['/pinterest/boards', '/boards/get' , '/pinterest/save' ])) {
            return $next($request);
        }


        //if user has not reached The Facebook config step yet
        $token = IO::get('user_access_token', new Credential, 'get', ['user_access_token']);

        if (is_null($token) &&  !in_array($request->getPathInfo(), ['/facebook/authorize']) ) {
            return redirect('/facebook/authorize');
        }

        //if user has no Facebook page configured
//        $pages = IO::get('pages', new Page, 'myPages', [], true, true);
//
//        if (is_null($facebook_configured) && (!empty($pages)
//                && !in_array($request->getPathInfo(),
//                    ['/facebook/pages', '/facebook/configure', '/boards/get']))
//        ) {
//            return redirect('/facebook/pages');
//        }


        //You Shall passe
        return $next($request);
    }
}
