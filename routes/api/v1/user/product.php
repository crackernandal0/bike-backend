<?php

use App\Http\Controllers\Api\V1\User\Order\OrderController;
use App\Http\Controllers\Api\V1\User\Order\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/product', 'middleware' => 'auth:sanctum'], function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('categories-with-products', 'getAllCategoriesAndProducts');
        Route::post('filter', 'filterProducts');
        Route::post('apply-coupon', 'applyCoupon');
    });
    Route::controller(OrderController::class)->group(function () {
        Route::post('place-order', 'placeOrder');
        Route::get('orders', 'fetchOrders');
        Route::get('order-details/{id}', 'fetchOrderDetails');
        Route::post('cancel-order', 'cancelOrder');

        Route::post('order-payment-using-wallet', 'orderPaymentUsingWallet');
        Route::post('initialize-order-payment', 'initializeOrderPayment');
        Route::post('order-payment-callback', 'gatewayOrderPaymentCallback');
    });
});
