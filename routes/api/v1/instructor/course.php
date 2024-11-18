<?php

use App\Http\Controllers\Api\V1\Instructor\CourseController;
use App\Http\Middleware\InstructorMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'instructor', 'middleware' => InstructorMiddleware::class, 'controller' => CourseController::class], function () {
    Route::post('/courses/{id?}', 'addOrUpdateCourse'); // Add Course
    Route::get('/delete-courses/{id}', 'deleteCourse'); // Delete Course
    Route::get('/courses', 'showAllCourses'); // Show All Courses
    Route::get('/courses/{id}', 'showCourse'); // Show Particular Course
});
