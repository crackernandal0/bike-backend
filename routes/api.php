<?php

use App\Http\Controllers\NotificationController;
use App\Http\Middleware\CheckApiKey;
use Illuminate\Support\Facades\Route;

// api v1 routes
Route::prefix('v1')->middleware(CheckApiKey::class)->group(function () {
    include_route_files('api/v1');
});

Route::get('test-server', function () {
    return response()->json(['message' => 'Server is running!'], 200);
});

Route::post('send-push-notification', [NotificationController::class, 'sendPushNotification']);