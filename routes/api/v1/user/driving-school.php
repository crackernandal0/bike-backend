<?php

use App\Http\Controllers\Api\V1\User\DrivingSchool\CoursesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user/driving-school', 'middleware' => 'auth:sanctum', 'controller' => CoursesController::class], function () {
    Route::get('courses', 'showAllCourses'); // Show All Courses
    Route::get('courses/{id}', 'showCourse'); // Show Particular Course
    Route::get('instrcutor-profile/{id}', 'instrcutor'); // Show Particular Course
    
    Route::post('enroll', 'enroll'); 
    Route::post('gateway-course-payment-callback', 'gatewayCoursePaymentCallback')->name('gatewayCoursePaymentCallback'); 
    Route::post('e-receipt', 'getEReceipt'); 
    Route::get('enrolled-courses', 'getEnrolledCourses'); 
    Route::get('course-details', 'getCourseDetails'); 
    Route::post('add-rating', 'addRating'); 
    Route::get('certificate-data', 'getCertificateData'); 
});
