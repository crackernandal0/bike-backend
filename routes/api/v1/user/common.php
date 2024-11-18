<?php

use App\Http\Controllers\Api\V1\User\Common\ChatSupportController;
use App\Http\Controllers\Api\V1\User\Common\ComplainController;
use App\Http\Controllers\Api\V1\User\Common\FaqController;
use App\Http\Controllers\Api\V1\User\Common\FavouriteLocationController;
use App\Http\Controllers\Api\V1\User\Common\SosContactController;
use App\Http\Controllers\Api\V1\User\Common\UserController;
use App\Http\Controllers\Api\V1\User\Common\WalletController;
use App\Http\Controllers\PhonePeController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'user/common', 'middleware' => 'auth:sanctum'], function () {
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
        Route::get('user', 'user');
    });

    Route::controller(FavouriteLocationController::class)->group(function () {
        Route::get('favorite-locations', 'favoriteLocations');
        Route::get('favorite-locations-suggestions', 'favoriteLocationsSuggestions');
        Route::post('add-favorite-location', 'addFavoriteLocation');
        Route::post('update-favorite-location', 'updateFavoriteLocation');
        Route::post('delete-favorite-location', 'deleteFavoriteLocation');
    });

    Route::controller(WalletController::class)->group(function () {
        Route::post('wallet/add-balance', 'addBalance');
        Route::post('wallet/payment-callback', 'walletPaymentCallback');
        Route::post('wallet/withdraw', 'withdrawBalance');
        Route::get('wallet/transactions', 'fetchTransactions');
    });

    Route::controller(ChatSupportController::class)->group(function () {
        Route::get('chat-faqs', 'getFAQs');
        Route::post('send-message', 'sendMessage');
        Route::get('active-chat-session', 'getLastActiveChatSession');
    });

    Route::post('wallet/refund', [PhonePeController::class, 'refundPayment']);
});
