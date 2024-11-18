<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Trip\CustomTourPackage;
use App\Models\Trip\TourPackage;
use App\Models\Trip\TourPayment;
use App\Models\Trip\TripCategory;
use App\Models\Trip\UserTourBooking;
use App\Models\UserNotification;
use App\Models\Wallet;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;

class TripController extends Controller
{
    public function getAllCategoriesAndPackages(Request $request)
    {
        // Fetch all trip categories
        $categories = TripCategory::all(['id', 'name']);

        // Optional category_id filter
        $categoryId = $request->input('category_id');

        // Fetch popular tour packages where is_popular = 1 and optionally filter by category_id
        $popularTourPackagesQuery = TourPackage::where('is_popular', 1)
            ->withAvg('ratings', 'rating')
            ->select('id', 'package_name', 'location', 'banner_image')
            ->limit(10)
            ->latest();

        // Apply category filter if category_id is provided
        if ($categoryId) {
            $popularTourPackagesQuery->where('category_id', $categoryId);
        }

        $popularTourPackages = $popularTourPackagesQuery->get()
            ->map(function ($tourPackage) {
                return [
                    'id' => $tourPackage->id,
                    'package_name' => $tourPackage->package_name,
                    'location' => $tourPackage->location,
                    'banner_image' => $tourPackage->banner_image,
                    'average_rating' => $tourPackage->ratings_avg_rating ? round($tourPackage->ratings_avg_rating, 1) : 0,
                ];
            });

        // Fetch non-popular tour packages where is_popular = 0 and optionally filter by category_id
        $nonPopularTourPackagesQuery = TourPackage::where('is_popular', 0)
            ->withAvg('ratings', 'rating')
            ->select('id', 'package_name', 'location', 'banner_image')
            ->limit(10)
            ->latest();

        // Apply category filter if category_id is provided
        if ($categoryId) {
            $nonPopularTourPackagesQuery->where('category_id', $categoryId);
        }

        $nonPopularTourPackages = $nonPopularTourPackagesQuery->get()
            ->map(function ($tourPackage) {
                return [
                    'id' => $tourPackage->id,
                    'package_name' => $tourPackage->package_name,
                    'location' => $tourPackage->location,
                    'banner_image' => $tourPackage->banner_image,
                    'average_rating' => $tourPackage->ratings_avg_rating ? round($tourPackage->ratings_avg_rating, 1) : 0,
                ];
            });

        // Prepare the response
        return jsonResponseData(true, [
            'categories' => $categories,
            'popular_tour_packages' => $popularTourPackages,
            'non_popular_tour_packages' => $nonPopularTourPackages,
        ], 200);
    }
    public function getAllPackages()
    {
        $tourPackages = TourPackage::withAvg('ratings', 'rating')
            ->select('id', 'package_name', 'location', 'banner_image')
            ->latest()
            ->get()
            ->map(function ($tourPackage) {
                return [
                    'id' => $tourPackage->id,
                    'package_name' => $tourPackage->package_name,
                    'location' => $tourPackage->location,
                    'banner_image' => $tourPackage->banner_image,
                    'average_rating' => $tourPackage->ratings_avg_rating ? round($tourPackage->ratings_avg_rating, 1) : 0,
                ];
            });


        // Prepare the response
        return jsonResponseData(true, [
            'tour_packages' => $tourPackages,
        ], 200);
    }

    public function searchTourPackages(Request $request)
    {
        // Retrieve the search query from the request
        $searchQuery = $request->input('query');
        if (!$searchQuery) {
            // Check if search query exists
            return jsonResponse(false, 'Search query is required.');
        }

        // Fetch tour packages based on the search query
        $tourPackages = TourPackage::where(function ($query) use ($searchQuery) {
            $query->where('package_name', 'LIKE', "%{$searchQuery}%")
                ->orWhere('location', 'LIKE', "%{$searchQuery}%")
                ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                ->orWhere('duration', 'LIKE', "%{$searchQuery}%")
                ->orWhere('members', 'LIKE', "%{$searchQuery}%")
                ->orWhere('detailed_itinerary', 'LIKE', "%{$searchQuery}%")
                ->orWhere('tour_stops_places', 'LIKE', "%{$searchQuery}%")
                ->orWhere('tour_price', 'LIKE', "%{$searchQuery}%");
        })
            ->withAvg('ratings', 'rating') // Fetch average rating
            ->select('id', 'package_name', 'location', 'banner_image')
            ->latest()
            ->get()
            ->map(function ($tourPackage) {
                return [
                    'id' => $tourPackage->id,
                    'package_name' => $tourPackage->package_name,
                    'location' => $tourPackage->location,
                    'banner_image' => $tourPackage->banner_image,
                    'average_rating' => $tourPackage->ratings_avg_rating ? round($tourPackage->ratings_avg_rating, 1) : 0,
                ];
            });

        // Return response with filtered tour packages
        return jsonResponseData(true, $tourPackages);
    }

    public function getTourPackageDetails($id)
    {
        // Find the tour package by ID with its related category and average rating
        $tourPackage = TourPackage::with('category:id,name') // Include category name
            ->withAvg('ratings', 'rating') // Include average rating
            ->find($id);

        // Check if the tour package exists
        if (!$tourPackage) {
            return jsonResponseData(false, 'Tour package not found.');
        }

        $tourPackage->vehicle_type_id = 5;
        // Prepare the response data

        // Return the response with tour package details
        return jsonResponseData(true, $tourPackage);
    }

    public function bookTour(Request $request)
    {
        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'tour_package_id' => 'required|exists:tour_packages,id',
            'pickup_location' => 'required|string',
            'vehicle_subcategory_id' => 'nullable|exists:vehicle_subcategories,id',
            'booking_date' => 'required|date',
            'no_of_passengers' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'payment_method' => 'required|in:cash,gateway,wallet',
            'stops' => 'nullable|array',
            'stops.*.long' => 'required_with:stops|numeric',
            'stops.*.lat' => 'required_with:stops|numeric',
            'stops.*.address' => 'required_with:stops|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return validationError($validator->errors());
        }
        // Prepare the booking data
        $bookingData = [
            'tour_package_id' => $request->tour_package_id,
            'user_id' => auth()->id(),
            'pickup_location' => $request->pickup_location,
            'vehicle_subcategory_id' => $request->vehicle_subcategory_id,
            'booking_date' => $request->booking_date,
            'no_of_passengers' => $request->no_of_passengers,
            'special_requests' => $request->special_requests ?? null,
            'stops' => isset($request->stops) ? json_encode($request->stops) : null,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending', // Default to pending
            'booking_status' => 'pending', // Default to pending
        ];

        // Create the booking in the database
        UserTourBooking::create($bookingData);


        return jsonResponse(true, 'Tour Booked Successfully!');
    }

    public function bookCustomTour(Request $request)
    {
        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'pickup_location' => 'required|string',
            'vehicle_subcategory_id' => 'nullable|exists:vehicle_subcategories,id',
            'tour_location' => 'required|string',
            'start_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:start_date',
            'no_of_passengers' => 'required|integer|min:1',
            'budget' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string',
            'stops' => 'nullable|array',
            'stops.*.long' => 'required_with:stops|numeric',
            'stops.*.lat' => 'required_with:stops|numeric',
            'stops.*.address' => 'required_with:stops|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Prepare the booking data
        $customTourData = [
            'user_id' => auth()->id(),
            'pickup_location' => $request->pickup_location,
            'vehicle_subcategory_id' => $request->vehicle_subcategory_id,
            'tour_location' => $request->tour_location,
            'start_date' => $request->start_date,
            'return_date' => $request->return_date,
            'no_of_passengers' => $request->no_of_passengers,
            'budget' => $request->budget,
            'special_requests' => $request->special_requests ?? null,
            'stops' => isset($request->stops) ? json_encode($request->stops) : null,
        ];

        // Create the custom tour booking in the database
        CustomTourPackage::create($customTourData);

        // Respond with the booking details
        return jsonResponse(true, 'Custom tour booking successfull submitted.');
    }

    public function getUserBookings(Request $request)
    {
        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:pending,completed,cancelled',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch user bookings based on the type
        $bookings = UserTourBooking::where('user_id', auth()->id())
            ->where('booking_status', $request->type)
            ->with([
                'tourPackage:id,package_name,location,banner_image', // Fetch related tour package data
                'tourPackage.ratings' // Fetch ratings for average calculation
            ])
            ->get()
            ->map(function ($booking) {
                // Include tour package details in the response
                return [
                    'id' => $booking->id,
                    'booking_date' => Carbon::parse($booking->booking_date)->format('d/m/Y'), // Format booking date
                    'created_at' => $booking->created_at->format('d/m/Y H:i:s'), // Format creation date
                    'tour_package' => [
                        'id' => $booking->tourPackage->id,
                        'name' => $booking->tourPackage->package_name,
                        'location' => $booking->tourPackage->location,
                        'banner_image' => $booking->tourPackage->banner_image,
                        'average_rating' => $booking->tourPackage->ratings->avg('rating') ? round($booking->tourPackage->ratings->avg('rating'), 1) : 0
                    ]
                ];
            });

        // Respond with the user bookings
        return jsonResponseData(true, $bookings);
    }

    public function getBookingDetails(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:user_tour_bookings,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the booking details
        $booking = UserTourBooking::where('id', $request->booking_id)
            ->with([
                'tourPackage:id,package_name,location,banner_image,description,duration',
                'tourPackage.ratings', // Fetch ratings to calculate average rating
                'vehicleSubcategory:id,name,image,short_amenties,specifications'
            ])
            ->firstOrFail();

        // Prepare the response data
        $bookingDetails = [
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'pickup_location' => $booking->pickup_location,
            'vehicle_subcategory_id' => $booking->vehicle_subcategory_id,
            'booking_date' => Carbon::parse($booking->booking_date)->format('d/m/Y'),
            'no_of_passengers' => $booking->no_of_passengers,
            'special_requests' => $booking->special_requests,
            'stops' => $booking->stops,
            'payment_method' => $booking->payment_method,
            'payment_status' => $booking->payment_status,
            'booking_status' => $booking->booking_status,
            'created_at' => $booking->created_at->format('d/m/Y H:i:s'),
            'tour_package' => [
                'id' => $booking->tourPackage->id,
                'name' => $booking->tourPackage->package_name,
                'banner_image' => $booking->tourPackage->banner_image,
                'description' => $booking->tourPackage->description,
                'location' => $booking->tourPackage->location,
                'duration' => $booking->tourPackage->duration,
                'average_rating' => $booking->tourPackage->ratings->avg('rating') ? round($booking->tourPackage->ratings->avg('rating'), 1) : 0,
            ],
            'vehicle_subcategory' => $booking->vehicleSubcategory,

        ];

        // Return the response with booking and tour package details

        return jsonResponseData(true, $bookingDetails);
    }

    public function cancelBooking(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:user_tour_bookings,id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the booking
        $booking = UserTourBooking::find($request->booking_id);

        // Check if the booking already has a status of cancelled
        if ($booking->booking_status === 'cancelled') {
            return jsonResponse(false, 'Booking is already cancelled.');
        }

        // Update the booking status to cancelled
        $booking->booking_status = 'cancelled';
        $booking->save();

        // Return success response
        return jsonResponse(false, 'Booking cancelled successfully.');
    }

    public function tourPaymentUsingWallet(Request $request)
    {
        // Get the authenticated user
        $userId = auth()->id();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'user_tour_booking_id' => 'required|exists:user_tour_bookings,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the user's wallet
        $wallet = Wallet::where('user_id', $userId)->first();

        // Check if the user has a wallet and sufficient balance
        if (!$wallet || $wallet->balance <= 0) {
            return jsonResponseData(false, 'Insufficient wallet balance. Please add sufficient funds to your wallet.');
        }

        // Fetch the Tour package details
        $userTourBooking = UserTourBooking::where('id', $request->user_tour_booking_id)
            ->with('tourPackage')
            ->where('user_id', $userId)
            ->where('payment_status', 'pending') // Only allow payment for hires with pending payment
            ->first();

        if (!$userTourBooking) {
            return jsonResponseData(false, 'Tour package not found or payment is already completed.');
        }

        if (!$userTourBooking->tourPackage) {
            return jsonResponseData(false, 'Tour package not found.');
        }

        if (!$userTourBooking->tourPackage->tour_price) {
            return jsonResponseData(false, 'Tour package booking payment is not yet added by admin');
        }

        // Calculate the total payment amount
        $totalAmount = $userTourBooking->tourPackage->tour_price;
        // $adminCommission = $price * ($userTourBooking->admin_commission / 100);
        // $serviceTax = $price * ($userTourBooking->service_tax / 100);
        // $gst = $price * ($userTourBooking->gst / 100);
        // $totalAmount = $price + $adminCommission + $serviceTax + $gst;

        // Check if the user has sufficient balance for the Tour package
        if ($wallet->balance < $totalAmount) {
            return jsonResponseData(false, 'Insufficient wallet balance for this payment.');
        }

        // Deduct the amount from the user's wallet
        $wallet->balance -= $totalAmount;
        $wallet->save();

        // Update the payment status of the chauffeur hire
        $userTourBooking->update([
            'payment_status' => 'completed',
            'payment_method' => 'wallet'
        ]);

        // Record the wallet transaction
        $transactionId = Str::uuid();
        $transaction = $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $totalAmount,
            'transaction_id' => $transactionId, // Optionally, generate a unique transaction ID
            'reference' => 'Trip Payment',
            'status' => 'completed',
        ]);

        TourPayment::create([
            'user_id' => $userId,
            'user_tour_booking_id' => $userTourBooking->id,
            'transaction_id' => $transactionId,
            'payment_type' => 'wallet',
            'amount' => (int) $totalAmount,
            'status' => 'completed',
        ]);
        // Return a success response with the transaction details
        return jsonResponseWithData(true, 'Payment completed successfully.', $transaction);
    }

    public function initializeTourPayment(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'user_tour_booking_id' => 'required|exists:user_tour_bookings,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }


        // Fetch the chauffeur hire details
        $userTourBooking = UserTourBooking::where('id', $request->user_tour_booking_id)
            ->with('tourPackage')
            ->where('user_id', $user->id)
            ->where('payment_status', 'pending') // Only allow payment for hires with pending payment
            ->first();

        if (!$userTourBooking) {
            return jsonResponseData(false, 'Tour package not found or payment is already completed.');
        }

        if (!$userTourBooking->tourPackage) {
            return jsonResponseData(false, 'Tour package not found.');
        }

        if (!$userTourBooking->tourPackage->tour_price) {
            return jsonResponseData(false, 'Tour package booking payment is not yet added by admin');
        }

        // Calculate the total payment amount
        $totalAmount = $userTourBooking->tourPackage->tour_price;
        // $adminCommission = $price * ($chauffeurHire->admin_commission / 100);
        // $serviceTax = $price * ($chauffeurHire->service_tax / 100);
        // $gst = $price * ($chauffeurHire->gst / 100);
        // $totalAmount = $price + $adminCommission + $serviceTax + $gst;

        $merchantTransactionId = "TXN-" . $userTourBooking->id . '-' . time();

        $amount = $totalAmount * 100;

        // Create a payment record with pending status
        TourPayment::create([
            'user_id' => $user->id,
            'user_tour_booking_id' => $userTourBooking->id,
            'transaction_id' => $merchantTransactionId,
            'payment_type' => 'gateway',
            'amount' => (int) $amount,
            'status' => 'pending',
        ]);

        $callbackUrl = route('coursePaymentCallback');
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



        // Return a success response with the transaction details
        return jsonResponseWithData(true, 'Payment completed successfully.', $paymentData);
    }

    public function gatewayTourPaymentCallback(Request $request)
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
        $tour_payment = TourPayment::where('transaction_id', $request->merchant_transaction_id)->first();

        if (!$tour_payment) {
            return jsonResponse(false, 'Transaction not found!', 404);
        }

        // Check the payment status from PhonePe and update the ride status
        if ($request->payment_status == "success") {
            $tour_payment->update(['status' => 'completed', 'provider_reference_id' => $request->provider_reference_id]);


            $chauffeur_hire = UserTourBooking::find($tour_payment->user_tour_booking_id);
            $chauffeur_hire->update(['payment_status' => 'completed', 'payment_method' => 'gateway']);


            // Send a notification to the user about the successful payment
            $user = $tour_payment->user;
            if ($user && $user->fcm_token) {
                $userToken = $user->fcm_token;
                $title = 'Payment Successful';
                $body = 'Your payment for the trip has been completed successfully.';
                FCMService::sendNotification($userToken, $title, $body, 'courses');

                // Log the notification in the database
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }


            return jsonResponseWithData(true, 'Payment completed successfully', $tour_payment);
        } else {
            $tour_payment->update(['status' => 'failed', 'provider_reference_id' => $request->provider_reference_id ?? null]);

            return jsonResponse(false, 'Payment failed!', 400);
        }
    }
}
