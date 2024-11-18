<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Helpers\Payment\PhonePeHelper;
use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use App\Models\Driver\DriverNotification;
use App\Models\Ride\DriverRideRequest;
use App\Models\Ride\Ride;
use App\Models\Ride\RidePayment;
use App\Models\Service\ZoneTypePrice;
use App\Models\UserNotification;
use App\Models\Wallet;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function getRideRequest()
    {
        $driverId = auth('driver')->id();
        $rideRequest = DriverRideRequest::with(['ride' => function ($query) {
            $query->select([
                'id',
                'pickup',
                'dropoff',
                'total_distance',
                'estimated_time',
                'ride_status',
                'is_schedule_ride',
                'scheduled_date',
                'scheduled_time',
                'ride_type',
                'return_trip',
                'return_date',
                'return_time',
                'is_for_someone_else',
                'rider_name',
                'rider_phone_number',
                'additional_notes',
                'passenger_count',
                'user_id',
            ])
                ->with([
                    'user:id,name,phone_number,profile_picture',
                    'rideStops:id,ride_id,stop' // Select necessary fields from rideStops
                ]);
        }])
            ->where('driver_id', $driverId)
            ->where('request_status', "pending")
            ->first();

        if (!$rideRequest) {
            return jsonResponseData(true, ['ride_request' => null]);
        }

        $ride = $rideRequest->ride;

        $response = [
            'ride_id' => $rideRequest->ride->id,
            'ride_request_id' => $rideRequest->id,
            'pickup' => $ride->pickup,
            'dropoff' => $ride->dropoff,
            'distance_from_pickup_to_dropoff' => $ride->total_distance, // Assuming it's the same as total_distance
            'estimated_ride_time' => $ride->estimated_time,
            'ride_status' => $ride->estimated_time,
            'is_schedule_ride' => $ride->is_schedule_ride,
            'scheduled_date' => $ride->scheduled_date,
            'scheduled_time' => $ride->scheduled_time,
            'ride_type' => $ride->ride_type,
            'return_trip' => $ride->return_trip,
            'return_date' => $ride->return_date,
            'return_time' => $ride->return_time,
            'is_for_someone_else' => $ride->is_for_someone_else,
            'rider_name' => $ride->rider_name,
            'rider_phone_number' => $ride->rider_phone_number,
            'additional_notes' => $ride->additional_notes,
            'passenger_name' => $ride->user->name,
            'total_passengers' => $ride->passenger_count,
            'passenger_phone_number' => $ride->user->phone_number,
            'passenger_profile_picture' => $ride->user->profile_picture,
            'ride_stops' => $ride->rideStops->map(function ($stop) {
                return [
                    'stop' => $stop->stop,
                ];
            }),
        ];

        return jsonResponseData(true, ['ride_request' => $response]);
    }

    public function respondToRideRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_request_id' => 'required|exists:driver_ride_requests,id',
            'response' => 'required|in:accept,decline',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $driver = auth('driver')->user();
        $rideRequest = DriverRideRequest::where('id', $request->ride_request_id)
            ->where('driver_id', $driver->id)
            ->with('ride')
            ->firstOrFail();

        if ($rideRequest->request_status !== 'pending') {
            return jsonResponse(false, 'Ride request is no longer pending.');
        }



        if ($request->response === 'accept') {
            // Accept the ride request
            $rideRequest->update([
                'request_status' => 'accepted',
                'accepted_at' => now(),
            ]);



            // Update the ride with driver ID and accepted timestamp
            $rideRequest->ride->update([
                'driver_id' => $driver->id,
                'ride_status' => 'accepted',
                'ride_accepted_at' => now(),
            ]);

            $driver->update(['total_accepts' => $driver->total_accepts + 1]);

            return jsonResponse(true, 'Ride request accepted.');
        } elseif ($request->response === 'decline') {
            // Decline the ride request
            $rideRequest->update([
                'request_status' => 'declined',
                'declined_at' => now(),
            ]);

            // Decode the pickup JSON to extract latitude and longitude
            $pickup = json_decode($rideRequest->ride->pickup, true);
            $pickupLat = $pickup['lat'];
            $pickupLong = $pickup['long'];

            // Find the next nearest driver, excluding those who have declined the ride
            $nearestDriverId = findNearestDriver($pickupLat, $pickupLong, $rideRequest->ride->id);

            if ($nearestDriverId) {
                // Create a new ride request for the nearest driver
                $driverRideRequest = DriverRideRequest::create([
                    'ride_id' => $rideRequest->ride->id,
                    'driver_id' => $nearestDriverId,
                    'request_status' => 'pending',
                ]);

                // Fetch the nearest driver details
                $driver = Driver::select('fcm_token')->find($nearestDriverId);

                if ($driver && $driver->fcm_token) {
                    // Prepare ride data for notification
                    $rideNumber = $rideRequest->ride->ride_number; // Assuming ride number is set during ride creation
                    $pickup = $rideRequest->ride->pickup['address'] ?? null; // Pickup location
                    $dropoff = $rideRequest->ride->dropoff['address'] ?? null; // Drop-off location
                    $requestType = $request->is_schedule_ride ? 'Scheduled' : 'Instant';

                    // Prepare title, body, and route for the notification
                    $title = "New " . $requestType . " Ride Request!";
                    $body = "Ride #{$rideNumber} request from {$pickup} to {$dropoff}. Accept to start the trip.";
                    $route = "ride_request/{$driverRideRequest->id}"; // Assuming this route is where driver sees ride details

                    // Send notification to the driver using the FCM service
                    FCMService::sendNotification($driver->fcm_token, $title, $body, $route);

                    DriverNotification::create([
                        'driver_id' => $nearestDriverId,
                        'title' => $title,
                        'body' => $body,
                    ]);
                }
            }


            $driver->update(['total_rejects' => $driver->total_rejects + 1]);

            return jsonResponse(true, 'Ride request declined.');
        }

        return jsonResponse(false, 'Invalid response.');
    }

    public function getActiveRide()
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Define the statuses to filter by
        $statuses = ['driver_arrived', 'in_progress'];

        // Fetch the active ride for the authenticated driver
        $ride = Ride::where('driver_id', $driverId)
            ->whereIn('ride_status', $statuses)
            ->with(['user:id,name,phone_number','rideStops:id,ride_id,stop']) // Fetch the related user details
            ->select([
                'id',
                'user_id',
                'pickup',
                'dropoff',
                'total_distance',
                'estimated_time',
                'payment_status',
                'ride_status',
                'ride_type',
                'return_trip',
                'return_date',
                'return_time',
                'passenger_count',
                'is_for_someone_else',
                'rider_name',
                'rider_phone_number',
                'additional_notes',
                'waiting_minutes',
                'waiting_charges',
                'final_fare'
            ])
            ->first(); // Retrieve only one active ride

        // Check if an active ride exists
        if (!$ride) {
            return jsonResponse(false, 'No active ride found.');
        }

        // Prepare the ride data to be returned
        $rideData = [
            'ride_id' => $ride->id,
            'pickup' => $ride->pickup,
            'dropoff' => $ride->dropoff,
            'rideStops' => $ride->rideStops,
            'total_distance' => $ride->total_distance,
            'estimated_time' => $ride->estimated_time,
            'payment_status' => $ride->payment_status,
            'ride_status' => $ride->ride_status,
            'ride_type' => $ride->ride_type,
            'return_trip' => $ride->return_trip,
            'return_date' => $ride->return_date,
            'return_time' => $ride->return_time,
            'passenger_count' => $ride->passenger_count,
            'is_for_someone_else' => $ride->is_for_someone_else,
            'rider_name' => $ride->rider_name,
            'rider_phone_number' => $ride->rider_phone_number,
            'additional_notes' => $ride->additional_notes,
            'waiting_minutes' => $ride->waiting_minutes,
            'waiting_charges' => $ride->waiting_charges,
            'total_amount' => $ride->final_fare,
            'user' => [
                'id' => $ride->user->id,
                'name' => $ride->user->name,
                'phone_number' => $ride->user->phone_number
            ]
        ];

        // Return the active ride details in the response
        return jsonResponseData(true, ['ride' => $rideData]);
    }

    public function cancelRide(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
            'cancel_type' => 'required|string|max:255',
            'cancel_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Find the ride and ensure it belongs to the authenticated driver and is not completed
        $ride = Ride::where('id', $request->ride_id)
            ->where('driver_id', $driverId)
            ->whereNotIn('ride_status', ['completed', 'in_progress', 'canceled'])
            ->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride not found or cannot be canceled.');
        }

        // Update the ride status to 'canceled', set the cancellation timestamp, and store the reason
        $ride->update([
            'ride_status' => 'canceled',
            'ride_cancelled_at' => now(),
            'cancel_type' => $request->cancel_type,
            'cancel_reason' => $request->cancel_reason,
            'canceled_by' => 'driver',
        ]);

        DriverRideRequest::where('driver_id', $driverId)
            ->where('ride_id', $ride->id)
            ->where('request_status', "accepted")
            ->update(['request_status' => 'canceled']);


        return jsonResponse(true, 'Ride has been canceled successfully.');
    }

    public function updatePaymentStatus(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Find the ride that belongs to the authenticated driver
        $ride = Ride::where('id', $request->ride_id)
            ->where('driver_id', $driverId)
            ->where('ride_status', '!=', 'canceled')
            ->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride not found or not authorized.');
        }

        // Update the payment status and payment type
        $ride->update([
            'payment_status' => 'completed',
            'payment_type' => 'Cash',
        ]);

        return jsonResponse(true, 'Payment status updated successfully.');
    }

    public function initiateRidePayment(Request $request)
    {
        $rideId = $request->ride_id;
        $driverId = auth('driver')->id(); // Assuming the user is authenticated

        $ride = Ride::where('id', $rideId)->where('driver_id', $driverId)->with('driver')->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride is not booked!', 400);
        }
        if (!$ride->driver) {
            return jsonResponse(false, 'You are nt the driver of this ride', 400);
        }

        if ($ride->payment_status == 'completed') {
            return jsonResponse(false, 'Ride payment is already completed!', 400);
        }

        if ($ride->driver_arrived_at) {
            $waitingMinutes = now()->diffInMinutes($ride->driver_arrived_at);
            $waitingChargePerMinute = $ride->zoneType->waiting_charge; // Fetch the per-minute waiting charge

            // Calculate total waiting charges
            $waitingCharges = $waitingMinutes * $waitingChargePerMinute;
        } else {
            // If no arrival time is recorded, set waiting minutes and charges to zero
            $waitingMinutes = 0;
            $waitingCharges = 0;
        }

        $amount = $ride->payment_amount * 100; // Convert to paisa

        $amount = $amount + $waitingCharges;

        $merchantTransactionId = "TXN-" . $rideId . '-' . time();
        // // Include user and ride details in the message
        // $message = "Payment for Ride #" . $ride->ride_number .
        //     " - Passenger: " . $ride->user->name .
        //     ", Phone: " . $ride->user->phone_number .
        //     ", Email: " . $ride->user->email .
        //     ". Amount: Rs. " . ($amount / 100) . ".";

        // Check if a payment record already exists with status 'pending' or 'failed'
        $order = RidePayment::where('ride_id', $rideId)
            ->where('user_id', $ride->user->id)
            ->whereIn('status', ['pending', 'failed'])
            ->first();

        if ($order) {
            // Update the existing order with new details
            $order->update([
                'merchant_transaction_id' => $merchantTransactionId,
                'payment_amount' => $ride->payment_amount,
                'status' => 'pending',
            ]);
        } else {
            // Create a new order entry with status 'pending'
            $order = RidePayment::create([
                'user_id' => $ride->user->id,
                'ride_id' => $rideId,
                'merchant_transaction_id' => $merchantTransactionId,
                'payment_amount' => $ride->payment_amount,
                'status' => 'pending',
            ]);
        }

        $callbackUrl = route('phonepe-ride-payment-callback');
        // Call the helper function to initiate the payment
        $response = PhonePeHelper::makePayment(
            $merchantTransactionId,
            $ride->user->id,
            $amount,
            $callbackUrl,
            $ride->user->phone_number,
         
        );

        if (is_array($response) && !$response['status']) {
            // Update the order status to 'failed' if payment initiation fails
            $order->update(['status' => 'failed']);
            return jsonResponse(false, $response['message'], 400);
        }

        // If payment initiation is successful, return the response
        return $response;
    }

    public function checkRidePaymentStatus(Request $request)
    {
        // Validate the incoming request to ensure ride_id is provided
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $rideId = $request->ride_id;
        $driverId = auth('driver')->id(); // Assuming the user is authenticated

        // Fetch the ride with the specified ride ID and user ID
        $ride = Ride::where('id', $rideId)->where('driver_id', $driverId)->first();

        // Check if the ride exists and belongs to the authenticated user
        if (!$ride) {
            return jsonResponse(false, 'Ride not found or does not belong to the user.', 404);
        }

        // Return the payment status of the ride
        return jsonResponseWithData(true, 'Payment status fetched successfully.', [
            'ride_id' => $ride->id,
            'payment_status' => $ride->payment_status,
        ]);
    }


    public function markAsArrived(Request $request)
    {
        // Validate the incoming request to ensure 'ride_id' is provided
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Fetch the ride with the specified ID and ensure it belongs to the authenticated driver
        $ride = Ride::where('id', $request->ride_id)
            ->where('driver_id', $driverId)
            ->with('user:id,fcm_token') // Fetch the user relation with FCM token
            ->firstOrFail();

        // Check if the ride is in a status that can be marked as arrived
        if ($ride->ride_status !== 'accepted') {
            if ($ride->ride_status !== 'driver_arrived') {
                return jsonResponse(false, 'Ride cannot be marked as arrived at this stage.');
            }
        }



        if ($ride->ride_status !== 'driver_arrived') {
            // Update the ride status to 'in_progress' and set the ride_started_at timestamp
            $ride->update([
                'ride_status' => 'driver_arrived',
                'driver_arrived_at' => now(), // Track when the ride started
            ]);
        }


        // Send FCM notification to the user if the FCM token is not null
        if ($ride->user && $ride->user->fcm_token) {
            $token = $ride->user->fcm_token;
            $title = 'Driver Arrived';
            $body = 'Your driver has arrived at the pickup location.';
            $route = 'ride_details/' . $ride->id;

            // Send notification using FCMService
            FCMService::sendNotification($token, $title, $body, $route);

            UserNotification::create([
                'user_id' => $ride->user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }

        // Return success response
        return jsonResponse(true, 'Ride status updated to arrived and user notified.');
    }

    public function startRide(Request $request)
    {
        // Validate the incoming request to ensure 'ride_id' is provided
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
            'ride_otp' => 'required',

        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Fetch the ride with the specified ID and ensure it belongs to the authenticated driver
        $ride = Ride::where('id', $request->ride_id)
            ->where('driver_id', $driverId)
            ->with(['user:id,name,phone_number,fcm_token','zoneType:id,waiting_charge']) // Fetch the user relation with FCM token
            ->firstOrFail();

        if ($ride->ride_otp !== $request->ride_otp) {
            return jsonResponse(false, 'Incorrect Ride OTP.');
        }

        // Check if the ride is in a status that can be marked as in_progress
        if ($ride->ride_status !== 'driver_arrived') {
            if ($ride->ride_status !== 'accepted') {
                return jsonResponse(false, 'Ride cannot be started at this stage.');
            }
        }


        // Calculate waiting time if driver arrived timestamp is available
        if ($ride->driver_arrived_at) {
            $waitingMinutes = abs(now()->diffInMinutes($ride->driver_arrived_at));
            $waitingChargePerMinute = $ride->zoneType->waiting_charge ?? null; // Fetch the per-minute waiting charge

            // Calculate total waiting charges
            $waitingCharges = $waitingMinutes * $waitingChargePerMinute;
        } else {
            // If no arrival time is recorded, set waiting minutes and charges to zero
            $waitingMinutes = 0;
            $waitingCharges = 0;
        }

        // Update the ride status to 'in_progress' and set the ride_started_at timestamp
        $ride->update([
            'ride_status' => 'in_progress',
            'ride_started_at' => now(), // Track when the ride started
            'waiting_minutes' => $waitingMinutes,
            'waiting_charges' => $waitingCharges,
        ]);


        if ($ride->user && $ride->user->fcm_token) {
            $token = $ride->user->fcm_token;
            $title = 'Ride Started';
            $body = 'Your ride has started. Enjoy your trip!';
            $route = 'ride_details/' . $ride->id; // Example route; adjust as needed for your frontend

            // Send notification using FCMService
            FCMService::sendNotification($token, $title, $body, $route);

            // Log the notification in the UserNotification table
            UserNotification::create([
                'user_id' => $ride->user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }

        // Prepare ride data to be returned in the response
        $rideData = [
            'ride_id' => $ride->id,
            'pickup' => $ride->pickup,
            'dropoff' => $ride->dropoff,
            'total_distance' => $ride->total_distance,
            'estimated_time' => $ride->estimated_time,
            'payment_status' => $ride->payment_status,
            'waiting_minutes' => $waitingMinutes,
            'waiting_charges' => $waitingCharges,
        ];


        // Return success response with ride details
        return jsonResponseWithData(true, 'Ride status updated to in progress and user notified.', [
            'ride' => $rideData,
        ]);
    }

    public function completeRide(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Find the ride that belongs to the authenticated driver and has the provided OTP
        $ride = Ride::where('id', $request->ride_id)
            ->where('driver_id', $driverId)
            ->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride not found or unauthorized.');
        }


        // Ensure the ride is not already completed
        if ($ride->ride_status === 'completed') {
            return jsonResponse(false, 'Ride is already completed.');
        }

        if ($ride->payment_status !== 'completed') {
            return jsonResponse(false, 'Ride Payment is not completed. Please complete payment to complete the ride.');
        }

        // Calculate the total price and driver amount
        $zoneTypePrice = ZoneTypePrice::where('zone_id', $ride->zone_id)
            ->where('vehicle_type_id', $ride->vehicle_type_id)
            ->where('vehicle_subcategory_id', $ride->vehicle_subcategory_id)
            ->first();

        if (!$zoneTypePrice) {
            return jsonResponse(false, 'Pricing information not found for this ride.');
        }

        // Calculate the total price
        $basePrice = $zoneTypePrice->base_price;
        $distancePrice = $ride->total_distance * $zoneTypePrice->price_per_distance;
        $totalPrice = $basePrice + $distancePrice;

        // Add admin commission
        $adminCommission = $totalPrice * ($zoneTypePrice->admin_commision / 100);
        $totalPrice += $adminCommission;

        // Add service tax and GST separately
        $serviceTax = $totalPrice * ($zoneTypePrice->service_tax / 100);
        $gst = $totalPrice * ($zoneTypePrice->gst_tax / 100);
        $totalPrice = round($totalPrice + $serviceTax + $gst, 2);

        // Calculate the driver's amount (payment amount - admin commission - service tax - GST)
        $driverAmount = round($ride->payment_amount - $adminCommission - $serviceTax - $gst, 2);

        // Update the ride status to completed and set the ride completion timestamp
        $ride->update([
            'ride_status' => 'completed',
            'ride_completed_at' => now(),
            'final_fare' => $totalPrice, // Save the final fare in the ride record
        ]);
        $totalDriverAmount = $driverAmount + $ride->waiting_charges;

        $wallet = Wallet::firstOrCreate(
            ['driver_id' => $driverId],
            ['balance' => 0.00, 'is_active' => true]
        );

        $wallet->balance += $totalDriverAmount;

        $wallet->save();

        $wallet->transactions()->create([
            'type' => 'credit',
            'amount' => $totalDriverAmount,
            'transaction_id' => null,
            'reference' => $ride->ride_number . ' Payment',
            'status' => 'completed',
        ]);


        // Send notification to the user about the ride completion
        if ($ride->user && $ride->user->fcm_token) {
            $userToken = $ride->user->fcm_token;
            $userTitle = 'Ride Completed';
            $userBody = 'Your ride has been completed successfully. Thank you for riding with us!';
            FCMService::sendNotification($userToken, $userTitle, $userBody, 'ride_details/' . $ride->id);

            // Log the user notification in the database
            UserNotification::create([
                'user_id' => $ride->user->id,
                'title' => $userTitle,
                'body' => $userBody,
            ]);
        }

        // Send notification to the driver about the completed ride and earnings
        $driver = auth('driver')->user();
        if ($driver && $driver->fcm_token) {
            $driverToken = $driver->fcm_token;
            $driverTitle = 'Ride Completed';
            $driverBody = "You have successfully completed the ride and earned ₹{$totalDriverAmount}. Keep up the great work!";
            FCMService::sendNotification($driverToken, $driverTitle, $driverBody, 'wallet');

            // Log the driver notification in the database
            DriverNotification::create([
                'driver_id' => $driver->id,
                'title' => $driverTitle,
                'body' => $driverBody,
            ]);
        }

        // Return the driver amount in the response with a success message
        return jsonResponseWithData(true, 'Ride completed successfully.', [
            'base_fare' => $driverAmount,
            'waiting_carges' => $ride->waiting_charges ?? 0,
            'driver_amount' => $totalDriverAmount,
        ]);
    }


    public function getUpcomingRides()
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Define the statuses to filter by
        $statuses = [
            'accepted',
            'driver_arrived',
            'in_progress',
            'waiting'
        ];

        // Fetch the rides with the specified statuses for the authenticated driver
        $rides = Ride::select(['id', 'user_id', 'pickup', 'dropoff', 'ride_status', 'is_schedule_ride', 'scheduled_date', 'scheduled_time', 'created_at'])->where('driver_id', $driverId)
            ->whereIn('ride_status', $statuses)
            ->with('user:id,name,phone_number')
            ->orderByRaw("
        CASE 
            WHEN is_schedule_ride = 1 THEN CONCAT(COALESCE(scheduled_date, ''), ' ', COALESCE(scheduled_time, ''))
            ELSE created_at
        END DESC
    ")
            ->get();

        // Return the rides in the response
        return jsonResponseData(true, ['rides' => $rides]);
    }

    public function getCompletedRides()
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Define the statuses to filter by
        $statuses = [
            'completed'
        ];

        // Fetch the rides with the specified statuses for the authenticated driver
        $rides = Ride::select(['id', 'user_id', 'pickup', 'dropoff', 'ride_status', 'is_schedule_ride', 'scheduled_date', 'scheduled_time', 'created_at'])->where('driver_id', $driverId)
            ->whereIn('ride_status', $statuses)
            ->with('user:id,name,phone_number')
            ->orderByRaw("
            CASE 
                WHEN is_schedule_ride = 1 THEN CONCAT(COALESCE(scheduled_date, ''), ' ', COALESCE(scheduled_time, ''))
                ELSE created_at
            END DESC
        ")
            ->get();

        // Return the rides in the response
        return jsonResponseData(true, ['rides' => $rides]);
    }

    public function getCanceledRides()
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Define the statuses to filter by
        $statuses = [
            'canceled'
        ];

        // Fetch the rides with the specified statuses for the authenticated driver
        $rides = Ride::select(['id', 'user_id', 'pickup', 'dropoff', 'ride_status', 'is_schedule_ride', 'scheduled_date', 'scheduled_time', 'created_at'])->where('driver_id', $driverId)
            ->whereIn('ride_status', $statuses)
            ->with('user:id,name,phone_number')
            ->orderByRaw("
            CASE 
                WHEN is_schedule_ride = 1 THEN CONCAT(COALESCE(scheduled_date, ''), ' ', COALESCE(scheduled_time, ''))
                ELSE created_at
            END DESC
        ")
            ->get();

        // Return the rides in the response
        return jsonResponseData(true, ['rides' => $rides]);
    }

    public function getRideDetails(Request $request)
    {
        // Validate the incoming request to ensure 'ride_id' is provided
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Fetch the ride with the specified ID and ensure it belongs to the authenticated driver
        $ride = Ride::where('id', $request->ride_id)
            ->where('driver_id', $driverId)
            ->with(['user:id,name,phone_number']) // Only fetch the user's name and phone number
            ->firstOrFail();

        // Calculate the total price and driver amount
        $zoneTypePrice = ZoneTypePrice::where('zone_id', $ride->zone_id)
            ->where('vehicle_type_id', $ride->vehicle_type_id)
            ->where('vehicle_subcategory_id', $ride->vehicle_subcategory_id)
            ->first();

        if (!$zoneTypePrice) {
            return jsonResponse(false, 'Pricing information not found for this ride.');
        }

        // Calculate the total price
        $basePrice = $zoneTypePrice->base_price;
        $distancePrice = $ride->total_distance * $zoneTypePrice->price_per_distance;
        $totalPrice = $basePrice + $distancePrice;

        // Add admin commission
        $adminCommission = $totalPrice * ($zoneTypePrice->admin_commision / 100);
        $totalPrice += $adminCommission;

        // Add service tax and GST separately
        $serviceTax = $totalPrice * ($zoneTypePrice->service_tax / 100);
        $gst = $totalPrice * ($zoneTypePrice->gst_tax / 100);
        $totalPrice = round($totalPrice + $serviceTax + $gst, 2);

        // Calculate the driver's amount (payment amount - admin commission - service tax - GST)
        $driverAmount = round($ride->payment_amount - $adminCommission - $serviceTax - $gst, 2);

        // Update the ride status to completed and set the ride completion timestamp
        $totalDriverAmount = $driverAmount + $ride->waiting_charges;

        // Return the ride details in the response
        return jsonResponseData(true, [
            'ride' => $ride,
            'base_fare' => $driverAmount,
            'waiting_carges' => $ride->waiting_charges ?? 0,
            'driver_amount' => $totalDriverAmount,
        ]);
    }
}
