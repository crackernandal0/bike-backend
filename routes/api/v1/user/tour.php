<?php

use App\Http\Controllers\Api\V1\User\TripController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/tour', 'controller' => TripController::class, 'middleware' => 'auth:sanctum'], function () {
    Route::get('packages', 'getAllCategoriesAndPackages');
    Route::get('all-packages', 'getAllPackages');
    Route::post('search', 'searchTourPackages');
    Route::get('details/{id}', 'getTourPackageDetails');
    Route::post('book', 'bookTour');
    Route::post('book-custom-tour', 'bookCustomTour');
    Route::get('user-bookings', 'getUserBookings');
    Route::get('booking-details', 'getBookingDetails');
    Route::get('cancel-booking', 'cancelBooking');


    Route::post('tour-payment-using-wallet', 'tourPaymentUsingWallet');
    Route::post('initialize-tour-payment', 'initializeTourPayment');
    Route::post('tour-payment-callback', 'gatewayTourPaymentCallback');
});
