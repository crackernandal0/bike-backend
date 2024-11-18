<?php

use App\Http\Controllers\Api\V1\Instructor\InstructorController;
use App\Http\Middleware\InstructorMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'instructor', 'middleware' => InstructorMiddleware::class, 'controller' => InstructorController::class], function () {
    Route::prefix(prefix: 'common')->group(function () {
        Route::get('notifications', 'notifications');
        Route::post('update-language', 'updateLanguage');
        Route::post('request-account-deletion', 'requestAccountDeletion');
        Route::post('submit-contact-query', 'submitContactQuery');
        Route::post('submit-complain', 'submitComplain');
    });
    Route::get('instructor-stats', 'getInstructorStats');
    Route::post('request-driver', 'requestDriver');
    Route::get('profile', 'profile');
});