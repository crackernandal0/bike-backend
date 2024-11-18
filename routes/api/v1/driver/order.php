<?php

use App\Http\Controllers\Api\V1\Driver\OrderController;
use App\Http\Middleware\DriverMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'driver', 'controller' => OrderController::class, 'middleware' => DriverMiddleware::class], function () {
    Route::get('order-requests', 'fetchOrderRequests');
    Route::post('order-details', 'fetchOrderDetails');
    Route::post('orders', 'fetchAssignedOrders');
    Route::post('order-request/response', 'respondToOrderRequest');
    Route::post('order/mark-as-delivered', 'markAsDelivered');
    Route::post('cancel-order', 'cancelOrder');
});
