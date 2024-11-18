<?php

namespace App\Http\Controllers;
use App\Services\FCMService;


use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendPushNotification(Request $request)
    {
        $token = $request->input('token');
        $title = $request->input('title');
        $body = $request->input('body');
        $route = $request->input('route');

        FCMService::sendNotification($token, $title, $body, $route);

        return response()->json(['message' => 'Notification sent successfully']);
    }
}
