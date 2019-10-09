<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Credential extends Model
{
    protected $table = 'credentials';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'name', 'value', 'user_id'];

    public $timestamps = true;


    public static function get($name, $user_id = null, $to_serialize = false)
    {
        $credential = self::where(
            ['name' => $name, 'user_id' => is_null($user_id) ? Auth::id() : $user_id])
            ->first(['value']);

        return !is_null($credential) ? (($to_serialize) ? @unserialize($credential->value) : $credential->value) : null;
    }

    public static function set(array $data, $user_id = null)
    {
        foreach ($data as $key => $value) {
            self::updateOrCreate(['name' => $key, 'user_id' => is_null($user_id) ?  Auth::id(): $user_id], ['name' => $key, 'value' => $value, 'user_id' => Auth::id()]);
        }

    }

}
