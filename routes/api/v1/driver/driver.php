<?php

use App\Http\Controllers\Api\V1\Driver\ChauffeurController;
use App\Http\Controllers\Api\V1\Driver\DriverController;
use App\Http\Middleware\DriverMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'driver', 'controller' => DriverController::class, 'middleware' => DriverMiddleware::class], function () {
    Route::get('availability', 'getAvailability');
    Route::post('toggle-availability', 'toggleAvailability');
    Route::get('profile', 'profile');
    Route::post('request-instructor', 'requestInstructor');
});

Route::group(['prefix' => 'driver/chauffeurs', 'controller' => ChauffeurController::class, 'middleware' => DriverMiddleware::class], function () {
    Route::post('profile', 'createOrUpdateProfile');
    Route::get('profile', 'getProfile');
    Route::get('delete-profile', 'deleteProfile'); // Route to delete profile
});
