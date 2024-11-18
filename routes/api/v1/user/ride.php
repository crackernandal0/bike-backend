<?php

use App\Http\Controllers\Api\V1\User\Ride\RideController;
use App\Http\Controllers\Api\V1\User\Ride\VehicleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/ride'], function () {

    Route::controller(VehicleController::class)->group(function () {
        Route::get('vehicle-types', 'getVehicleTypes');
        Route::post('vehicle-subcategory-details', 'getVehicleSubcategoryDetails');
        Route::post('vehicle-subcategories', 'getSubCategories');
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('zone-subcategories', 'fetchZoneSubCategories');
        });
    });

    Route::controller(RideController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('check-zone', 'checkZone');
        Route::post('rent-details', 'fetchRentDetails');
        Route::post('check-promo', 'checkPromo');
        Route::post('book-ride', 'bookRide');
        Route::post('check-status/{ride_id}', 'checkStatus');
        Route::post('initiate-ride-payment', 'initiateRidePayment');
        Route::post('gateway-ride-payment', 'gatewayRidePayment');
        Route::post('check-ride-payment-status', 'checkRidePaymentStatus');
        Route::post('payment-using-wallet', 'ridePaymentUsingWallet');
        Route::post('add-ride-rating', 'addRideRating');

        Route::get('active-ride', 'getActiveRide');
        Route::post('cancel-ride', 'cancelRide');

        Route::get('upcoming-rides', 'getUpcomingRides');
        Route::get('completed-rides', 'getCompletedRides');
        Route::get('canceled-rides', 'getCanceledRides');
        Route::post('ride-details', 'getRideDetails');
    });
});
