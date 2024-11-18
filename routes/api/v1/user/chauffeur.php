<?php

use App\Http\Controllers\Api\V1\User\ChauffeurController;
use App\Http\Controllers\Api\V1\User\ChauffeurHireController;
use App\Http\Controllers\Api\V1\User\Ride\RideController;
use App\Http\Controllers\Api\V1\User\Ride\VehicleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/chauffeurs', 'middleware' => 'auth:sanctum'], function () {
    Route::controller(ChauffeurController::class)->group(function () {
        Route::get('/', 'getChauffeurs');
        Route::get('chauffeur-profile/{id}', 'getChauffeurProfile');
    });
    Route::controller(ChauffeurHireController::class)->group(function () {
        Route::post('book-chauffeur', 'bookChauffeur');
        Route::get('pending-chauffeur-hires', 'getPendingChauffeurHires');
        Route::get('active-chauffeur-hires', 'getActiveChauffeurHires');
        Route::get('other-status-chauffeur-hires', 'getOtherStatusChauffeurHires');
        Route::get('chauffeur-hires-details/{id}', 'ChauffeurHiresDetails');
        Route::get('update-status', 'updateStatus');
        Route::post('chauffeur-payment-using-wallet', 'chauffeurPaymentUsingWallet');
        Route::post('initialize-chauffeur-payment', 'initializeChauffeurPayment');
        Route::post('chauffeur-payment-callback', 'gatewayChauffeurPaymentCallback');
    });
});
