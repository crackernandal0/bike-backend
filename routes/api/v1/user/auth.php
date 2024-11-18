<?php

use App\Http\Controllers\Api\V1\User\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/auth', 'controller' => AuthController::class], function () {
    Route::post('send-sms-otp', 'sendSMSOtp');
    Route::post('verify-otp', 'verifyOtp');
    Route::post('register', 'register');
    Route::get('social-auth/{provider}', 'socialAuth');
    Route::post('verify-phone', 'verifyPhone');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

