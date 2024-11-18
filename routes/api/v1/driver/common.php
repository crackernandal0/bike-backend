<?php

use App\Http\Controllers\Api\V1\Driver\Common\ChatSupportController;
use App\Http\Controllers\Api\V1\Driver\Common\ComplainController;
use App\Http\Controllers\Api\V1\Driver\Common\FaqController;
use App\Http\Controllers\Api\V1\Driver\Common\SosContactController;
use App\Http\Controllers\Api\V1\Driver\Common\UserController;
use App\Http\Controllers\Api\V1\Driver\Common\WalletController;
use App\Http\Middleware\DriverMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'driver/common'], function () {

    Route::middleware(DriverMiddleware::class)->group(function () {
        Route::controller(SosContactController::class)->group(function () {
            Route::get('sos-contacts', 'sosContacts');
            Route::post('add-sos-contact', 'addSosContact');
            Route::post('update-sos-contact', 'updateSosContact');
            Route::post('delete-sos-contact', 'deleteSosContact');
            Route::post('share-location-sos-contact', 'shareLocation');
        });

        Route::get('faqs', [FaqController::class, 'getFaqs']);
        Route::post('submit-complain', [ComplainController::class, 'submitComplain']);

        Route::controller(UserController::class)->group(function () {
            Route::post('update-language', 'updateLanguage');
            Route::post('request-account-deletion', 'requestAccountDeletion');
            Route::post('submit-contact-query', 'submitContactQuery');
            Route::post('update-profile', 'updateProfile');
            Route::post('update-location', 'updateLocation');
            Route::get('notifications', 'notifications');
        });
        Route::controller(WalletController::class)->group(function () {
            Route::get('driver-wallet-stats', 'fetchDriverWalletStats');
            Route::get('all-driver-transactions', 'fetchAllDriverTransactions');
            Route::get('withdraw-driver-full-balance', 'withdrawDriverFullBalance');
        });
    });

    Route::controller(ChatSupportController::class)->group(function () {
        Route::post('chat-faqs', 'getFAQs');
        Route::post('send-message', 'sendMessage');
        Route::post('active-chat-session', 'getLastActiveChatSession');
    })->middleware('auth:sanctum');
});
