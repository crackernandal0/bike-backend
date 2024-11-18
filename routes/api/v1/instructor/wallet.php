<?php

use App\Http\Controllers\Api\V1\Instructor\InstructorController;
use App\Http\Controllers\Api\V1\Instructor\TrainingController;
use App\Http\Controllers\Api\V1\Instructor\WalletController;
use App\Http\Middleware\InstructorMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'instructor', 'middleware' => InstructorMiddleware::class, 'controller' => WalletController::class], function () {
    Route::get('wallet-stats', 'fetchInstructorWalletStats');
    Route::get('all-transactions', 'fetchAllInstructorTransactions');
    Route::get('withdraw-full-balance', 'withdrawInstructorFullBalance');
});
