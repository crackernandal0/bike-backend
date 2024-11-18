<?php

namespace App\Http\Controllers\Api\V1\User\DrivingSchool;

use App\Helpers\Payment\PhonePeHelper;
use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use App\Models\Driver\InstructorWallet;
use App\Models\DrivingSchool\Course;
use App\Models\DrivingSchool\CoursePayment;
use App\Models\DrivingSchool\CourseRating;
use App\Models\DrivingSchool\Enrollment;
use App\Models\DrivingSchool\LessonProgress;
use App\Models\UserNotification;
use App\Models\Wallet;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CoursesController extends Controller
{
    // Show All Courses
    public function showAllCourses()
    {
        $courses = Course::select(['id', 'title', 'subtitle', 'total_enrollments', 'status', 'banner_image'])->where('status', 'approved')->get();

        return jsonResponseWithData(true, 'Courses fetched successfully.', ['courses' => $courses]);
    }

    // Show Particular Course
    public function showCourse($id)
    {
        $course = Course::where('id', $id)->where('status', "approved")->with('lessons', 'instructor:id,full_name,instructor_bio,profile_photo', 'ratings', 'ratings.user:id,name,profile_picture')->withAvg('ratings', 'rating')->first();

        if (!$course) {
            return jsonResponse(false, 'Course not found.');
        }

        return jsonResponseWithData(true, 'Course fetched successfully.', ['course' => $course]);
    }


    public function instrcutor($id)
    {
        // Fetch the instructor with courses and average rating for each course
        $instructor = Driver::where('id', $id)
            ->where('status', "approved")
            ->with([
                'courses' => function ($query) {
                    $query->select('id', 'instructor_id', 'title', 'subtitle', 'banner_image')
                        ->with('ratings:id,course_id,user_id,rating,description', 'ratings.user:id,name,profile_picture')
                        ->withAvg('ratings', 'rating'); // Fetch average rating of the course
                },
                'additionalInfo'
            ])
            ->first();

        // Check if instructor exists
        if (!$instructor) {
            return jsonResponse(false, 'Instructor not found.');
        }

        // Prepare courses data with average rating and separate reviews
        $courses = $instructor->courses->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'subtitle' => $course->subtitle,
                'banner_image' => $course->banner_image,
                'average_rating' => $course->ratings_avg_rating ? round($course->ratings_avg_rating, 1) : null, // Format the average rating
            ];
        });

        // Prepare reviews data
        $reviews = $instructor->courses->flatMap(function ($course) {
            return $course->ratings->map(function ($rating) {
                return [
                    'course_id' => $rating->course_id,
                    'user_id' => $rating->user_id,
                    'rating' => $rating->rating,
                    'description' => $rating->description,
                    'user' => [
                        'name' => $rating->user->name,
                        'profile_picture' => $rating->user->profile_picture,
                    ],
                ];
            });
        });

        // Fetch total enrollments across all courses for the instructor
        $totalEnrolledStudents = Enrollment::whereIn('course_id', $instructor->courses->pluck('id'))
            ->where('status', 'enrolled')
            ->count();

        $totalRatings = CourseRating::whereIn('course_id', $instructor->courses->pluck('id'))
            ->count();

        // Include instructor data, courses, reviews, total enrollments, and ratings in the response
        $instructorData = [
            'instructor' => $instructor,
            'reviews' => $reviews,
            'totalEnrolledStudents' => $totalEnrolledStudents,
            'totalRatings' => $totalRatings,
        ];

        return jsonResponseWithData(true, 'Instructor fetched successfully.', $instructorData);
    }


    public function enroll(Request $request)
    {
        $user = auth()->user();

        // Validate request data
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'payment_type' => 'required|in:wallet,gateway',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $course = Course::find($request->course_id);

        // Create a pending enrollment record
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        // Handle payment type
        if ($request->payment_type == 'gateway') {
            return $this->processGatewayPayment($course, $user, $enrollment);
        } else {
            return $this->processWalletPayment($course, $user->id, $enrollment);
        }
    }

    private function processGatewayPayment($course, $user, $enrollment)
    {

        $merchantTransactionId = "TXN-" . $course->id . '-' . time();

        $amount = $course->price * 100;

        // Create a payment record with pending status
        $payment = CoursePayment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'transaction_id' => $merchantTransactionId,
            'payment_type' => 'gateway',
            'amount' => (int) $amount,
            'status' => 'pending',
        ]);

        $callbackUrl = route('gatewayCoursePaymentCallback');


        $paymentData = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'saltIndex' => env('PHONEPE_KEY_INDEX'),
            'saltKey' => env('PHONEPE_API_KEY'),
            'paymentMode' => env('PHONEPE_MODE'),
            'merchantTransactionId' => $merchantTransactionId,
            'userId' => $user->id,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl,
            'phone_number' => $user->phone_number,
        ];

        return jsonResponseWithData(true, 'Payment initialized successfull!', $paymentData);
    }

    public function gatewayCoursePaymentCallback(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'merchant_transaction_id' => 'required',
            'payment_status' => 'required|in:success,failed',
            'provider_reference_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Retrieve the order using the merchant transaction ID
        $course_payment = CoursePayment::where('transaction_id', $request->merchant_transaction_id)->first();

        if (!$course_payment) {
            return jsonResponse(false, 'Transaction not found!', 404);
        }

        // Check the payment status from PhonePe and update the ride status
        if ($request->payment_status == "success") {
            $course_payment->update(['status' => 'completed', 'provider_reference_id' => $request->provider_reference_id]);


            $enrollment = Enrollment::find($course_payment->enrollment_id);
            $enrollment->update(['status' => 'enrolled']);


            // Send a notification to the user about the successful payment
            $user = $course_payment->user;
            if ($user && $user->fcm_token) {
                $userToken = $user->fcm_token;
                $title = 'Payment Successful';
                $body = 'Your payment for the course enrollment has been completed successfully.';
                FCMService::sendNotification($userToken, $title, $body, 'courses');

                // Log the notification in the database
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }


            return jsonResponseWithData(true, 'Payment completed successfully', $course_payment);
        } else {
            $course_payment->update(['status' => 'failed', 'provider_reference_id' => $request->provider_reference_id ?? null]);

            return jsonResponse(false, 'Payment failed!', 400);
        }
    }


    // Handle wallet payment
    private function processWalletPayment($course, $userId, $enrollment)
    {
        $wallet = Wallet::where('user_id', $userId)->first();
        $coursePrice = $course->price;

        if (!$wallet || $wallet->balance < $coursePrice) {
            return jsonResponse(false, 'Insufficient wallet balance.');
        }

        // Deduct amount from wallet
        $wallet->balance -= $coursePrice;
        $wallet->save();

        $transaction_id = Str::uuid();
        // Update payment and enrollment status to completed
        $payment = CoursePayment::create([
            'user_id' => $userId,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'transaction_id' => $transaction_id,
            'payment_type' => 'wallet',
            'amount' => $coursePrice,
            'status' => 'completed',
        ]);

        $enrollment->update(['status' => 'enrolled']);

        $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $coursePrice,
            'transaction_id' => $transaction_id, // Optionally, generate a unique transaction ID
            'reference' => 'Course Enrollment Payment',
            'status' => 'completed',
        ]);

        $transaction_id = Str::uuid();

        $wallet = InstructorWallet::firstOrCreate(
            ['instructor_id' => $course->instructor_id],
            ['balance' => 0.00, 'is_active' => true]
        );

        $adminComission = $coursePrice * ($course->admin_commision / 100);

        $instrcutorAmount = $coursePrice - $adminComission;
        $wallet->balance += $instrcutorAmount;

        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'credit',
            'amount' => $instrcutorAmount,
            'transaction_id' => $transaction_id, // Optionally, generate a unique transaction ID
            'reference' => 'Course Enrollment Payment for enrollment ' . $enrollment->enrollment_id,
            'status' => 'completed',
        ]);

        return jsonResponseWithData(true, 'Enrollment completed successfully.', $payment);
    }

    public function getEReceipt(Request $request)
    {
        // Validate the incoming request to ensure 'course_payment_id' is provided and exists
        $validator = Validator::make($request->all(), [
            'course_payment_id' => 'required|exists:course_payments,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the course payment data along with the related user and course details
        $coursePayment = CoursePayment::where('id', $request->course_payment_id)
            ->with(['user:id,name,email,phone_number', 'course:id,title']) // Include user and course details
            ->firstOrFail();

        // Prepare the e-receipt data
        $eReceiptData = [
            'payment_id' => $coursePayment->id,
            'transaction_id' => $coursePayment->transaction_id,
            'payment_type' => $coursePayment->payment_type,
            'amount' => $coursePayment->amount,
            'status' => $coursePayment->status,
            'provider_reference_id' => $coursePayment->provider_reference_id,
            'user' => [
                'name' => $coursePayment->user->name,
                'email' => $coursePayment->user->email,
                'phone_number' => $coursePayment->user->phone_number,
            ],
            'course' => [
                'title' => $coursePayment->course->title,
            ],
            'payment_date' => $coursePayment->created_at->format('d-m-Y H:i:s'), // Format the payment date
        ];

        // Return success response with e-receipt data
        return jsonResponseWithData(true, 'E-Receipt generated successfully.', $eReceiptData);
    }

    public function getEnrolledCourses()
    {
        $userId = auth()->id();

        // Fetch courses the user is enrolled in
        $courses = Enrollment::where('user_id', $userId)
            ->with(['course' => function ($query) {
                $query->select('id', 'title', 'banner_image', 'duration')
                    ->withCount('lessons') // Fetch total lessons count
                    ->withAvg('ratings', 'rating'); // Fetch average rating of the course
            }])
            ->get()
            ->map(function ($enrollment) use ($userId) {
                $course = $enrollment->course;

                // Fetch completed lessons count for the user
                $completedLessonsCount = LessonProgress::where('user_id', $userId)
                    ->whereIn('lesson_id', $course->lessons->pluck('id'))
                    ->where('status', 'completed')
                    ->count();

                return [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'banner_image' => $course->banner_image,
                    'duration' => $course->duration,
                    'total_lessons' => $course->lessons_count, // Total lessons count
                    'completed_lessons' => $completedLessonsCount, // Completed lessons count
                    'average_rating' => $course->ratings_avg_rating ? round($course->ratings_avg_rating, 1) : 0, // Average rating, rounded to 1 decimal place
                ];
            });

        // Return response with enrolled courses list
        return jsonResponseWithData(true, 'Enrolled courses fetched successfully.', [
            'courses' => $courses,
        ]);
    }



    public function getCourseDetails(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the course details along with lessons and enrollment data
        $course = Course::where('id', $request->course_id)
            ->with(['lessons' => function ($query) use ($request) {
                $query->select('id', 'course_id', 'title', 'resources', 'duration');
            }])
            ->withAvg('ratings', 'rating') // Fetch average rating of the course
            ->firstOrFail();

        // Fetch enrollment details to get the location
        $enrollment = $course->enrollments()->where('user_id', auth()->id())->first();

        // Fetch the lesson progress for the user
        $lessonProgress = LessonProgress::where('user_id', auth()->id())
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $totalLessons = 0;
        if ($course->lessons) {
            $totalLessons = $course->lessons->count();
        }

        $completedLessonsCount = $lessonProgress->where('status', 'completed')->count();



        // Prepare detailed course data with lessons and progress status
        $courseDetails = [
            'course_id' => $course->id,
            'title' => $course->title,
            'description' => $course->description,
            'banner_image' => $course->banner_image,
            'status' => $course->status,
            'average_rating' => $course->ratings_avg_rating ? round($course->ratings_avg_rating, 1) : 0, // Average rating, rounded to 1 decimal place
            'location' => $enrollment->location ?? null, // Location assigned by the driver
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessonsCount,
            'lessons' => $course->lessons->map(function ($lesson) use ($lessonProgress) {
                $progress = $lessonProgress->get($lesson->id);
                return [
                    'lesson_id' => $lesson->id,
                    'title' => $lesson->title,
                    'duration' => $lesson->duration,
                    'resources' => $lesson->resources,
                    'learning_day' => $progress ? ($progress->learning_day ? $progress->learning_day->format('d/m/Y') : 'Not Scheduled') : null, // Display formatted learning day
                    'learning_time' => $progress ? ($progress->learning_time ? $progress->learning_time->format('H:i A') : 'Not Scheduled') : null, // Display formatted learning time
                    'status' => $progress ? $progress->status : 'not_started',
                    'started_at' => $progress ? $progress->started_at : null,
                    'completed_at' => $progress ? $progress->completed_at : null,
                ];
            }),
        ];

        // Return response with course details
        return jsonResponseWithData(true, 'Course details fetched successfully.', [
            'course' => $courseDetails,
        ]);
    }

    public function addRating(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'nullable|string|max:1000',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480', // Allow images and videos up to 20MB
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Check if the user is enrolled in the course
        $isEnrolled = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $request->course_id)
            ->exists();

        if (!$isEnrolled) {
            return jsonResponse(false, 'You must be enrolled in this course to add a rating.');
        }

        // Handle file upload if media is provided
        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('course_ratings', 'public'); // Store media in public storage
        }

        // Create the course rating
        CourseRating::create([
            'user_id' => auth()->id(),
            'course_id' => $request->course_id,
            'rating' => $request->rating,
            'description' => $request->description,
            'attachment' => $mediaPath,
        ]);

        // Return success response
        return jsonResponse(true, 'Rating added successfully.');
    }

    public function getCertificateData(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the enrollment data for the specified course and authenticated user
        $userId = auth()->id();
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $request->course_id)
            ->where('course_status', 'completed') // Ensure the course is completed
            ->with([
                'course' => function ($query) {
                    $query->select('id', 'title', 'instructor_id'); // Fetch the course title and instructor id
                },
                'course.instructor' => function ($query) {
                    $query->select('id', 'full_name'); // Fetch the instructor's name
                },
                'user' => function ($query) {
                    $query->select('id', 'name'); // Fetch the user's name
                }
            ])
            ->first();

        // Check if the enrollment exists and the course is completed
        if (!$enrollment) {
            return jsonResponse(false, 'Course not completed or enrollment not found.');
        }

        // Prepare response data with formatted dates
        $certificateData = [
            'course_title' => $enrollment->course->title,
            'completed_at' => Carbon::parse($enrollment->completed_at)->format('d/m/Y h:i A'), // Format completed_at date
            'started_at' => Carbon::parse($enrollment->created_at)->format('d/m/Y h:i A'), // Format started_at date
            'user_name' => $enrollment->user->name,
            'enrollment_id' => $enrollment->id, // Ensure to use correct field if needed
            'instructor_name' => $enrollment->course->instructor->full_name,
        ];

        // Return a success response with the certificate data
        return jsonResponseWithData(true, 'Certificate data fetched successfully.', $certificateData);
    }
}
