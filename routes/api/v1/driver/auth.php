<?php

use App\Http\Controllers\Api\V1\Driver\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'driver/auth', 'controller' => AuthController::class], function () {
    Route::post('send-sms-otp', 'sendSMSOtp');
    Route::post('verify-otp', 'verifyOtp');
    Route::post('register', 'register');
});
