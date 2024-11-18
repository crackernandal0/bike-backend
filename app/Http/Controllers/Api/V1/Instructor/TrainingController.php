<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\DrivingSchool\Course;
use App\Models\DrivingSchool\Enrollment;
use App\Models\DrivingSchool\LessonProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainingController extends Controller
{
    public function fetchEnrolledStudents()
    {
        // Assuming the authenticated user is an instructor
        $instructorId = auth('driver')->id();

        // Fetch courses taught by the instructor
        $courses = Course::where(column: 'instructor_id', $instructorId)->pluck('id');

        if ($courses->isEmpty()) {
            return jsonResponseWithData(false, 'No courses found for this instructor.', []);
        }

        // Fetch enrolled students where the status is 'enrolled' and course_status is 'pending'
        $enrollments = Enrollment::whereIn('course_id', $courses)
            ->where('status', operator: 'enrolled')
            ->where('course_status', 'pending')
            ->with([
                'course:id,title,duration,banner_image',  // Fetch course details
                'user:id,name,phone_number',                    // Fetch enrolled user details
                'course.lessons:id,course_id',           // Fetch course lessons for progress calculation
            ])
            ->get()
            ->map(function ($enrollment) {
                $course = $enrollment->course;
                $userId = $enrollment->user_id;

                // Fetch completed lessons count for the user
                $completedLessonsCount = LessonProgress::where('user_id', $userId)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->where('status', 'completed')
                    ->count();

                return [
                    'enrollment_id' => $enrollment->id,
                    'user' => [
                        'id' => $enrollment->user->id,
                        'name' => $enrollment->user->name,
                        'phone_number' => $enrollment->user->phone_number,
                    ],
                    'course' => [
                        'course_id' => $course->id,
                        'title' => $course->title,
                        'duration' => $course->duration,
                        'banner_image' => $course->banner_image,
                        'total_lessons' => $course->lessons->count(),
                        'completed_lessons' => $completedLessonsCount,
                    ],
                    'status' => $enrollment->status,
                    'course_status' => $enrollment->course_status,
                    'enrolled_at' => $enrollment->created_at->format('d/m/Y h:i A'),
                ];
            });

        // Return the list of enrollments
        return jsonResponseWithData(true, 'Enrolled students fetched successfully.', [
            'enrollments' => $enrollments,
        ]);
    }

    public function fetchCompletedEnrolledStudents()
    {
        // Assuming the authenticated user is an instructor
        $instructorId = auth('driver')->id();

        // Fetch courses taught by the instructor
        $courses = Course::where('instructor_id', $instructorId)->pluck('id');

        if ($courses->isEmpty()) {
            return jsonResponseWithData(false, 'No courses found for this instructor.', []);
        }

        // Fetch enrolled students where the status is 'enrolled' and course_status is 'pending'
        $enrollments = Enrollment::whereIn('course_id', $courses)
            ->where('status', 'enrolled')
            ->where('course_status', 'completed')
            ->with([
                'course:id,title,duration,banner_image',  // Fetch course details
                'user:id,name,phone_number',                    // Fetch enrolled user details
                'course.lessons:id,course_id',           // Fetch course lessons for progress calculation
            ])
            ->get()
            ->map(function ($enrollment) {
                $course = $enrollment->course;
                $userId = $enrollment->user_id;

                // Fetch completed lessons count for the user
                $completedLessonsCount = LessonProgress::where('user_id', $userId)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->where('status', 'completed')
                    ->count();

                return [
                    'enrollment_id' => $enrollment->id,
                    'user' => [
                        'id' => $enrollment->user->id,
                        'name' => $enrollment->user->name,
                        'phone_number' => $enrollment->user->phone_number,
                    ],
                    'course' => [
                        'course_id' => $course->id,
                        'title' => $course->title,
                        'duration' => $course->duration,
                        'banner_image' => $course->banner_image,
                        'total_lessons' => $course->lessons->count(),
                        'completed_lessons' => $completedLessonsCount,
                    ],
                    'status' => $enrollment->status,
                    'course_status' => $enrollment->course_status,
                    'enrolled_at' => $enrollment->created_at->format('d/m/Y h:i A'),
                    'completed_at' => Carbon::parse($enrollment->completed_at)->format('d/m/Y h:i A'),
                ];
            });

        // Return the list of enrollments
        return jsonResponseWithData(true, 'Enrolled students fetched successfully.', [
            'enrollments' => $enrollments,
        ]);
    }


    public function getCourseDetailsByEnrollment(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the enrollment details and the associated course
        $enrollment = Enrollment::where('id', $request->enrollment_id)
            ->with([
                'course' => function ($query) {
                    $query->select('id', 'title', 'banner_image', 'duration', 'instructor_id')
                        ->with([
                            'lessons' => function ($query) {
                                $query->select('id', 'course_id', 'title', 'resources', 'duration');
                            }
                        ]);
                },
                'user:id,name,phone_number'
            ])
            ->firstOrFail();

        // Ensure the instructor owns this course
        if ($enrollment->course->instructor_id != auth('driver')->id()) {
            return jsonResponse(false, 'Unauthorized access.');
        }

        $course = $enrollment->course;
        $totalLessons = $course->lessons->count();

        // Fetch the lesson progress for the enrolled user
        $lessonProgress = LessonProgress::where('user_id', $enrollment->user_id)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $completedLessonsCount = $lessonProgress->where('status', 'completed')->count();

        // Prepare detailed course data with lessons and progress status
        $courseDetails = [
            'enrollment_id' => $enrollment->id,
            'course_id' => $course->id,
            'title' => $course->title,
            'banner_image' => $course->banner_image,
            'duration' => $course->duration,
            'location' => $enrollment->location ?? null,
            'status' => $enrollment->status,
            'course_status' => $enrollment->course_status,
            'enrollment_number' => $enrollment->enrollment_id,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessonsCount,
            'enrolled_at' => $enrollment->created_at->format('d/m/Y h:i A'),
            'completed_at' => $enrollment->completed_at ? Carbon::parse($enrollment->completed_at)->format('d/m/Y h:i A') : null,
            'user' => $enrollment->user,
            'lessons' => $course->lessons->map(function ($lesson) use ($lessonProgress) {
                $progress = $lessonProgress->get($lesson->id);
                return [
                    'lesson_id' => $lesson->id,
                    'title' => $lesson->title,
                    'duration' => $lesson->duration,
                    'resources' => $lesson->resources,
                    'learning_day' => $progress ? ($progress->learning_day ? Carbon::parse($progress->learning_day)->format('d/m/Y') : 'Not Scheduled') : null,
                    'learning_time' => $progress ? ($progress->learning_time ? Carbon::parse($progress->learning_time)->format('h:i A') : 'Not Scheduled') : null,
                    'status' => $progress ? $progress->status : 'not_started',
                    'started_at' => $progress ? Carbon::parse($progress->started_at)->format('d/m/Y h:i A') : null,
                    'completed_at' => $progress ? Carbon::parse($progress->completed_at)->format('d/m/Y h:i A') : null,
                ];
            }),
        ];

        // Return response with course details
        return jsonResponseWithData(true, 'Course details fetched successfully.', [
            'course' => $courseDetails,
        ]);
    }

    public function updateLessonProgress(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'lesson_id' => 'required|exists:lessons,id',
            'status' => 'required|in:not_started,in_progress,completed',
            'learning_day' => 'nullable|date',
            'learning_time' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch or create a new lesson progress record
        $lessonProgress = LessonProgress::firstOrNew([
            'user_id' => $request->user_id,
            'lesson_id' => $request->lesson_id,
        ]);

        // Set or update the lesson progress fields
        $lessonProgress->status = $request->status;
        $lessonProgress->learning_day = $request->learning_day;
        $lessonProgress->learning_time = $request->learning_time;

        // Set timestamps based on the status
        if ($request->status == 'in_progress' && !$lessonProgress->started_at) {
            $lessonProgress->started_at = now(); // Set current timestamp for started_at
        }
        if ($request->status == 'completed' && !$lessonProgress->completed_at) {
            $lessonProgress->completed_at = now(); // Set current timestamp for completed_at
        }

        // Save the lesson progress record
        $lessonProgress->save();

        return jsonResponse(true, 'Lesson progress updated successfully.');
    }

    public function updateEnrollmentLocation(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'enrollment_id' => 'required|exists:enrollments,id', // Ensure the enrollment_id exists
            'location' => 'required|string|max:255', // Validate that location is a string with max length
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the enrollment record by enrollment_id
        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        // Update the location column
        $enrollment->location = $request->location;

        // Save the changes
        $enrollment->save();

        return jsonResponse(true, 'Enrollment location updated successfully.');
    }

    public function completeEnrollmentCourse(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'enrollment_id' => 'required|exists:enrollments,id', // Ensure the enrollment_id exists
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the enrollment record by enrollment_id
        $enrollment = Enrollment::findOrFail($request->enrollment_id);

        // Update the course_status to 'completed' and set the completed_at timestamp
        $enrollment->course_status = 'completed';
        $enrollment->completed_at = now(); // Set the current timestamp

        // Save the changes
        $enrollment->save();

        return jsonResponse(true, 'Enrollment course status updated to completed.');
    }

    public function getInstructorEnrolledStudents()
    {
        // Get the instructor ID from authenticated user
        $instructorId = auth('driver')->id();

        // Fetch all enrollments for the courses created by this instructor
        $enrollments = Enrollment::whereHas('course', function ($query) use ($instructorId) {
            $query->where('instructor_id', $instructorId);
        })
            ->with([
                'user:id,name,phone_number', // Fetch enrolled user details
            ])
            ->where('status', 'enrolled') // Only enrolled students
            ->get();

        // Map the result to provide a clear response
        $enrollmentData = $enrollments->map(function ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,
                'name' => $enrollment->user->name,
                'phone_number' => $enrollment->user->phone_number,
                'status' => $enrollment->status,
                'course_status' => $enrollment->course_status,
                'location' => $enrollment->location,
                'enrolled_at' => $enrollment->created_at->format('d/m/Y h:i A'),
            ];
        });

        // Return the response
        return jsonResponseWithData(true, 'Enrolled students fetched successfully.', [
            'enrollments' => $enrollmentData,
        ]);
    }
}
