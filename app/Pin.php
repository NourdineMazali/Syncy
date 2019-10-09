<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class Pin extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pins';
    protected $primaryKey = 'id';

    public $timestamps = true;


    public static function myPins()
    {
        return self::where([
                    'user_id'   => Auth::id(),
                    'board_id'  => IO::get('active_board', new Credential, "get", ['active_board'])])
            ->orderBy('id', 'desc')->get();
    }

    public static function myPinsByKey()
    {
        return self::where('user_id', Auth::id())->get()->keyBy('pin_id');
    }

    public static function destroyMyPins()
    {
        return self::where('user_id', Auth::id())->delete();
    }

    public static function MyPinsCount() {
        return self::where([
            'user_id'   => Auth::id()])
            ->orderBy('id', 'desc')->count();
    }

}
