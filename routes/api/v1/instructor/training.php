<?php

use App\Http\Controllers\Api\V1\Instructor\InstructorController;
use App\Http\Controllers\Api\V1\Instructor\TrainingController;
use App\Http\Middleware\InstructorMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'instructor', 'middleware' => InstructorMiddleware::class, 'controller' => TrainingController::class], function () {
    Route::get('enrolled-students', 'fetchEnrolledStudents');
    Route::get('enrolled-completed-students', 'fetchCompletedEnrolledStudents');
    Route::get('course-details-by-enrollment', 'getCourseDetailsByEnrollment');
    Route::post('update-lesson-progress', 'updateLessonProgress');
    Route::post('update-enrollment-location', 'updateEnrollmentLocation');
    Route::post('complete-enrollment-course', 'completeEnrollmentCourse');
    Route::get('instructor-enrolled-students', 'getInstructorEnrolledStudents');
});
