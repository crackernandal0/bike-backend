<?php

namespace App\Http\Controllers\Api\V1\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Common\Complaint;
use App\Models\Common\ContactQuery;
use App\Models\Common\DeleteAccountRequest;
use App\Models\Driver\Driver;
use App\Models\Driver\DriverNotification;
use App\Models\Driver\DriverRequest;
use App\Models\Driver\InstructorWallet;
use App\Models\DrivingSchool\Course;
use App\Models\DrivingSchool\Enrollment;
use App\Models\DrivingSchool\LessonProgress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InstructorController extends Controller
{
    public function notifications()
    {
        $userId = auth('driver')->id();
        $notifications = DriverNotification::select('title', 'body', 'created_at')->where('instructor_id', $userId)->latest()->get();

        return jsonResponseData(true, $notifications);
    }

    public function updateLanguage(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:5', // Assuming short codes like 'en', 'fr', 'es', etc.
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->user();

        // Update the language
        $user->language = $request->language;
        $user->save();

        return jsonResponse(true, __('Language updated successfully'));
    }

    public function requestAccountDeletion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->user();

        // Use the helper function to get the profile picture path
        $profilePicturePath = $user->profile_picture ?? null;

        // Save the delete account request
        DeleteAccountRequest::create([
            'driver_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'profile_picture' => $profilePicturePath,
            'referral_code' => $user->referral_code,
            'reason' => $request->reason,
        ]);

        return jsonResponse(true, __('Your account deletion request has been submitted.'));
    }

    public function submitContactQuery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->id();

        // Save the contact query
        ContactQuery::create([
            'driver_id' => $user,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return jsonResponse(true, __('Your query has been submitted successfully.'));
    }

    public function updateProfile(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'country_code' => 'nullable|min:1|max:5',
            'phone_number' => 'nullable|numeric|digits_between:5,20|unique:users,phone_number,' . auth('driver')->id(),
            'language' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->user();

        // Update the user details
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('country_code')) {
            $user->country_code = $request->country_code;
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        if ($request->has('language')) {
            $user->language = $request->language;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = uploadImage($request->file('profile_picture'), 'profile_pictures');
            $user->profile_picture = $profilePicturePath;
        }

        // Save the updated user data
        $user->save();

        return jsonResponseWithData(true, __('Profile updated successfully'), $user);
    }

    public function submitComplain(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5000', // Validate each image
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $driver_id = auth('driver')->id();

        // Initialize an array to hold the paths of the uploaded images
        $imagePaths = [];

        // If images are provided, handle the upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Use the helper function to upload the image and get the path
                $imagePath = uploadImage($image, 'complaints');
                $imagePaths[] = $imagePath;
            }
        }

        // Create the complaint record
        $complaint = Complaint::create([
            'driver_id' => $driver_id,
            'reason' => $request->reason,
            'details' => $request->details,
            'images' => json_encode($imagePaths), // Store images as JSON
        ]);

        return jsonResponse(true, __('Complaint submitted successfully'));
    }


    public function getInstructorStats()
    {
        // Get authenticated instructor's ID
        $instructorId = auth('driver')->id();

        // Fetch all courses by the instructor
        $courses = Course::where('instructor_id', $instructorId)->get();

        // Calculate total enrolled students across all courses
        $totalEnrolledStudents = Enrollment::whereIn('course_id', $courses->pluck('id'))->where('status', 'enrolled')->count();

        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => $instructorId],
            ['balance' => 0.00, 'is_active' => true]
        );
        // Calculate total earnings by summing course price * number of enrolled students
        // $totalEarnings = $courses->sum(function ($course) {
        //     $enrollmentCount = Enrollment::where('course_id', $course->id)->count();
        //     return $course->price * $enrollmentCount;
        // });
        $totalEarnings = $wallet->transactions()
            ->where('status', 'completed')
            ->sum('amount');


        // Fetch the next 5 upcoming sessions from LessonProgress
        $upcomingSessions = LessonProgress::whereHas('lesson', function ($query) use ($courses) {
            $query->whereIn('course_id', $courses->pluck('id'));
        })
            ->whereNotNull('learning_day')
            ->whereNotNull('learning_time')
            ->with(['lesson.course' => function ($query) {
                $query->select('id', 'title', 'banner_image');
            }, 'user' => function ($query) {
                $query->select('id', 'name', 'phone_number'); // Ensure you have phone_number in User model
            }])
            ->orderBy('learning_day')
            ->orderBy('learning_time')
            ->take(5)
            ->get()
            ->map(function ($session) {
                return [
                    'course_title' => $session->lesson->course->title,
                    'banner_image' => $session->lesson->course->banner_image,
                    'user_name' => $session->user->name,
                    'user_phone' => $session->user->phone_number,
                    'learning_day' => Carbon::parse($session->learning_day)->format('d/m/Y'), // Format the learning day
                    'learning_time' => Carbon::parse($session->learning_time)->format('H:i'), // Format the learning time
                ];
            });

        // Fetch total lessons count and completed lessons count for each course
        $coursesWithLessonStats = $courses->map(function ($course) use ($instructorId) {
            $totalLessons = $course->lessons()->count();
            $completedLessons = LessonProgress::whereIn('lesson_id', $course->lessons->pluck('id'))
                ->where('status', 'completed')
                ->count();

            return [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
            ];
        });

        // Prepare the response data
        $responseData = [
            'total_enrolled_students' => $totalEnrolledStudents,
            'total_earnings' => $totalEarnings,
            'upcoming_sessions' => $upcomingSessions,
            'courses_lesson_stats' => $coursesWithLessonStats,
        ];

        // Return a success response with the collected stats
        return jsonResponseWithData(true, 'Instructor stats fetched successfully.', $responseData);
    }

    public function requestDriver(Request $request)
    {
        $driver = auth('driver')->user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'available_for_chauffeur' => 'required|boolean',
            'available_for_trips' => 'required|boolean',
            'joining_type' => 'required|string',
            'additional_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Create a new driver request
        $driverRequest = DriverRequest::create([
            'driver_id' => $driver->id,
            'available_for_chauffeur' => $request->available_for_chauffeur,
            'available_for_trips' => $request->available_for_trips,
            'joining_type' => $request->joining_type,
            'additional_requests' => $request->additional_requests,
            'status' => 'pending', // Set the initial status to pending
        ]);

        return jsonResponse(true, 'Driver request submitted successfully.');
    }

    public function profile()
    {
        return jsonResponseData(true, Driver::findOrFail(auth('driver')->id())->with('additionalInfo')->first());
    }
}
