<?php

use App\Http\Controllers\Api\V1\Driver\RideController;
use App\Http\Middleware\DriverMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'driver/ride', 'controller' => RideController::class, 'middleware' => DriverMiddleware::class], function () {
    Route::get('ride-request', 'getRideRequest');
    Route::post('respond-to-ride-request', 'respondToRideRequest');
    Route::get('active-ride', 'getActiveRide');
    Route::post('cancel-ride', 'cancelRide');
    Route::post('payment-by-cash', 'updatePaymentStatus');
    Route::post('initiate-ride-payment', 'initiateRidePayment');
    Route::post('check-ride-payment-status', 'checkRidePaymentStatus');


    Route::post('mark-as-arrived', 'markAsArrived');
    Route::post('start-ride', 'startRide');
    Route::post('complete-ride', 'completeRide');

    Route::get('upcoming-rides', 'getUpcomingRides');
    Route::get('completed-rides', 'getCompletedRides');
    Route::get('canceled-rides', 'getCanceledRides');
    Route::post('ride-details', 'getRideDetails');
});