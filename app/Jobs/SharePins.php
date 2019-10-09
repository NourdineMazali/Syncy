<?php

namespace App\Jobs;

use DirkGroenen\Pinterest\Pinterest;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App;
use App\Pin;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Log;

/**
 * Class SharePins
 * @package App\Jobs
 */
class SharePins implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page_id;
    protected $pin_id;
    protected $user_id;
    protected $date;
    protected $caption;
    protected $to_fb;
    protected $to_ig;
    protected $to_account;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page_id,$pin_id, $pin, $user_id)
    {

        $this->page_id  = $page_id;
        $this->pin_id   = $pin_id;
        $this->user_id  = $user_id;
        $this->date     = $pin['scheduled_at'];
        $this->caption  = $pin['caption'];
     }

    /**
     * Execute the job.
     *
     * @return bool
     */

    public function handle()
    {
        $db_pin = Pin::where('pin_id', $this->pin_id);
        $images  = \GuzzleHttp\json_decode($db_pin->first()->image_src);
        $caption = isset($this->caption) ? $this->caption : "";

        OAuthController::publishToFacebook($this->page_id, $images->original->url, $caption,
            $this->date, $this->user_id);
    }
}
