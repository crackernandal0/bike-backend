<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FCMService
{
    public static function sendNotification($token, $title, $body, $route = null)
    {
        $messaging = Firebase::messaging();

        $notification = Notification::create($title, $body);

        if ($route) {
            $data = [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // Needed for redirection in Flutter
                'route' => $route,
            ];

            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);
        } else {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification);
        }


        $messaging->send($message);
    }
}
