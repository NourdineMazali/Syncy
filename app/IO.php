<?php
/**
 * Created by PhpStorm.
 * User: nourdine
 * Date: 18/6/17
 * Time: 10:53 PM
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\CacheController;

class IO extends Model
{


    public static function get($name, Model $model = null, $action = null, $params = [], $encoded = false, $to_array = false)
    {

        $value = Redis::get("$name:" . Auth::id());

        #Cache
        if (is_null($value)) {

            if (!empty($params)) {
                $value = $model::$action(...$params);
            } else {
                $value = $model::$action();
            }

            //if value is empty don't proceed, return null
            if (is_null($value)) return null;

            //json_decode value
            if ($model instanceof Credential) {
                Redis::set("$name:" . Auth::id(), $value);
            } else {
                Redis::set("$name:" . Auth::id(), $encoded ? \GuzzleHttp\json_encode($value) : $value);
            }

            if (is_array($value)) {
                return $value;
            }
        }

        return $encoded ? \GuzzleHttp\json_decode($value, $to_array) : $value;
    }
    public static function getValueFromObject($name, $object, $attribute) {

        $value = Redis::get("$name:" . Auth::id());

        #Cache
        if (is_null($value)) {

            $value = $object->$attribute;

        }

        return $value;
    }

    public static function getViewFromCache($view_name, $params = [], $reload = false)
    {

        if ($reload) return view($view_name, $params);

        $cached_view = Redis::get("$view_name:" . Auth::id());

        if (is_null($cached_view)) {

            switch ($view_name) {
                case 'dashboard':
                    CacheController::refreshDashboardView();
                    return Redis::get("$view_name:" . Auth::id());
                    break;
                default :
                    $view = view($view_name, $params);
                    Redis::set("$view_name:" . Auth::id(), $view);
                    return $view;
                    break;
            }

        }

        return $cached_view;
    }


    public static function setViewToCache($view_name, $params = [])
    {

        $cached_view = view($view_name, $params);

        Redis::set("$view_name:" . Auth::id(), $cached_view);

        return $cached_view;
    }

    public static function set($name, $data, $encoded = false, callable $function = null)
    {

        //save in db
        if (!is_null($function)) call_user_func($function);

        //save in Cache
        return Redis::set("$name:" . Auth::id(), $encoded ? \GuzzleHttp\json_encode($data) : $data);

    }

    public static function remove($name, Model $model, $field) {

        try {
            Redis::del("$name:" . Auth::id());
            $model::where($field, $name)
                    ->where('user_id', Auth::id())
                    ->delete();
        }
        catch (\Exception $exception) {

        }

    }

}