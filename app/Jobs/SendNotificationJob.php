<?php

namespace App\Jobs;

use App\Services\FCMService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fcmTokens; // This will hold all the tokens
    protected $title;
    protected $body;
    protected $route;

    /**
     * Create a new job instance.
     */
    public function __construct($fcmTokens, $title, $body, $route)
    {
        $this->fcmTokens = array_unique($fcmTokens); // Pass all tokens as an array
        $this->title = $title;
        $this->body = $body;
        $this->route = $route;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Iterate through each token and send the notification
        foreach ($this->fcmTokens as $token) {
            if ($token && !empty($token)) {
                FCMService::sendNotification($token, $this->title, $this->body, $this->route);
            }
        }
    }
}
