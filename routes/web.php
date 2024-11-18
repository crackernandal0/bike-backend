<?php

use App\Http\Controllers\Api\V1\user\Common\WalletController;
use App\Http\Controllers\AccountDeletionReqController;
use App\Http\Controllers\PhonePeController;
use App\Http\Controllers\PhonepeTestController;
use App\Livewire\Zone\ZoneVehciles;
use App\Http\Middleware\AdminMiddleware;
use App\Livewire\Chauffeur\ChauffeurBookings;
use App\Livewire\Chauffeur\Chauffeurs;
use App\Livewire\Dashboard;
use App\Livewire\Driver\ApprovedDrivers;
use App\Livewire\Driver\Driver;
use App\Livewire\Driver\PendingDrivers;
use App\Livewire\Login;
use App\Livewire\PushNotification;
use App\Livewire\Rides;
use App\Livewire\Users;
use App\Livewire\Vehicle\VehicleSubcategories;
use App\Livewire\Vehicle\VehicleTypes;
use App\Livewire\Zone\CreateZone;
use App\Livewire\Zone\ServiceLocations;
use App\Livewire\Zone\UpdateZone;
use App\Livewire\Zone\Zones;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;


Route::get('payment', [PhonePeController::class, 'payment']);

Route::any('phonepe-callback', [PhonePeController::class, 'callback'])->name('phonepe-ride-payment-callback');
Route::post('course-payment-callback', [PhonePeController::class, 'coursePaymentCallback'])->name('coursePaymentCallback');

Route::post('wallet/payment-callback', [PhonePeController::class, 'handleWalletPaymentCallback'])->name('wallet-payment-callback');

Route::post('wallet/payment-refund', [PhonePeController::class, 'rideRefundPayment'])->name('rideRefundPayment');

Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('google.login');

Route::get('google/callback', function () {
    $googleUser = Socialite::driver('google')->user();
    dd($googleUser);
});

// Route::view('notification','notification');
Route::view('terms-conditions', 'terms-conditions');
Route::view('privacy-policy', 'privacy-policy');
Route::view('refund-policy', 'refund-policy');

Route::get('account-deletion-request', [AccountDeletionReqController::class, 'accountDeletionRequest']);
Route::post('submit-account-deletion-request', [AccountDeletionReqController::class, 'submitAccountDeletionRequest'])->name('submit-account-deletion-request');


Route::get('/', Login::class)->name('login');
Route::get('logout', function () {
    auth('admin')->logout();
    return redirect()->route('login')->with('success', 'Logout successfull!');
})->name('logout');

Route::middleware(AdminMiddleware::class)->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('users', Users::class)->name('users');
    Route::get('rides', Rides::class)->name('rides');
    Route::get('vehicle-types', VehicleTypes::class)->name('vehicle-types');
    Route::get('vehicle-subcategories', VehicleSubcategories::class)->name('vehicle-subcategories');
    Route::get('service-locations', ServiceLocations::class)->name('service-locations');
    Route::get('zones', Zones::class)->name('zones');
    Route::get('create-zone', CreateZone::class)->name('create-zone');
    Route::get('update-zone/{zoneId}', UpdateZone::class)->name('update-zone');
    Route::get('zone-vehicles', ZoneVehciles::class)->name('zone-vehicles');
    Route::get('approved-drivers', ApprovedDrivers::class)->name('approved-drivers');
    Route::get('pending-drivers', PendingDrivers::class)->name('pending-drivers');
    Route::get('chauffeurs', Chauffeurs::class)->name('chauffeurs');
    Route::get('chauffeur-bookings', ChauffeurBookings::class)->name('chauffeur-bookings');
    Route::get('push-notification', PushNotification::class)->name('push-notification');
    Route::get('driver/{id}', Driver::class)->name('driver');
});