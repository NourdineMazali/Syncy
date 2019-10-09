<?php

namespace App\Jobs;

use DirkGroenen\Pinterest\Pinterest;
use Dotenv\Validator;
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
class PostToTimeline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $page_id;
    protected $pin_id;
    protected $user_id;
    protected $date;
    protected $caption;
    protected $img;
    protected $to_ig;
    protected $privacy;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $pin, $user_id)
    {

        $this->pin_id   = $pin['pin_id'];
        $this->user_id  = $user_id;
        $this->caption  = $pin['caption'];
        $encoded_img    = \GuzzleHttp\json_decode($pin['image_src'],true);
        $this->img      = $encoded_img['original']['url'];
        $this->privacy  = $pin['privacy'];
    }

    /**
     * Execute the job.
     *
     * @return bool
     */

    public function handle()
    {
        OAuthController::publishToAccount($this->img, $this->caption, $this->privacy, $this->user_id);
        return true;
    }
}
