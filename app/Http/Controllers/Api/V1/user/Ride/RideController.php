<?php

namespace App\Http\Controllers\Api\V1\User\Ride;

use App\Helpers\Payment\PhonePeHelper;
use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use App\Models\Driver\DriverNotification;
use App\Models\Ride\DriverRideRequest;
use App\Models\Ride\Ride;
use App\Models\Ride\RideFeedback;
use App\Models\Ride\RidePayment;
use App\Models\Service\Promo;
use App\Models\Service\Zone;
use App\Models\Service\ZoneTypePrice;
use App\Models\UserNotification;
use App\Models\Wallet;
use App\Services\FCMService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
    public function checkZone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
        ]);


        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $pickupPoint = [$request->pickup_lng, $request->pickup_lat];
        $dropoffPoint = [$request->dropoff_lng, $request->dropoff_lat];




        // Fetch zones from the cache or database
        // $zones = Cache::rememberForever('zones', function () {
        //     return Zone::with('zoneTypes.vehicleType', 'zoneTypes.zoneTypePrices')
        //         ->selectRaw('id, service_location_id, name, ST_AsText(coordinates) as coordinates, active')
        //         ->get();
        // });
        $zones = Zone::with('zoneTypePrice.vehicleType')
            ->selectRaw('id, service_location_id, name, ST_AsText(coordinates) as coordinates, active')
            ->where('active', 1)
            ->get();



        $zone = $zones->first(function ($zone) use ($pickupPoint, $dropoffPoint) {
            return isPointInZone($pickupPoint, $zone->coordinates) && isPointInZone($dropoffPoint, $zone->coordinates);
        });


        if (!$zone) {
            return jsonResponse(false, __('ride.service_not_available'), 404);
        }

        // Calculate distance and duration between pickup and dropoff
        $data = distanceAndTimeBetweenTwoCoordinates($pickupPoint, $dropoffPoint);

        $distance = $data['distance'];
        $estimatedTime = $data['duration'];

        if ($distance < 0.5) {
            return jsonResponse(false, 'The distance between pickup and dropoff is too less. Please choose a longer distance for the ride.', 400);
        }


        // Filter and calculate prices for bike and auto only
        // Filter and calculate prices for bike and auto only

        $vehicleTypePrices = $zone->zoneTypePrice->map(function ($zoneType) use ($distance) {
            $priceDetails = $zoneType;

            if (!$priceDetails) {
                return null; // If there's no pricing details, skip this zoneType
            }

            if ($zoneType->vehicleType->name == "Car") {
                // Calculate the total price based on the pricing model
                $basePrice = 0;
                // $extraDistance = max(0, $distance - $priceDetails->base_distance);
                $distancePrice = 0;

                $totalPrice = 0;
            } else {

                // Calculate the total price based on the pricing model
                $basePrice = $priceDetails->base_price;
                // $extraDistance = max(0, $distance - $priceDetails->base_distance);
                $distancePrice = $distance * $priceDetails->price_per_distance;

                $totalPrice = $basePrice + $distancePrice;
                $totalPrice += $totalPrice * ($zoneType->admin_commision / 100);
                // $totalPrice += $totalPrice * ($zoneType->service_tax / 100);
                // $totalPrice += $totalPrice * ($zoneType->gst_tax / 100);

                $totalPrice = round($totalPrice, 2);
            }


            return [
                'zone_type_id' => $zoneType->id,
                'vehicle_type_id' => $zoneType->vehicleType->id,
                'name' => $zoneType->vehicleType->name,
                'icon' => $zoneType->vehicleType->icon,
                'price' => $totalPrice,
                // 'base_price' => $basePrice,
            ];
        })->filter()
            ->unique('vehicle_type_id') // Ensure only unique vehicle types by 'vehicle_type_id'
            ->values(); // Re-index the array after filtering; // Remove null values


        $data = [
            'distance' => $distance,
            'estimatedTime' => $estimatedTime,
            'zone_id' => $zone->id,
            'vehicle_types' => $vehicleTypePrices->values(), // Re-index the array after filtering
            // 'payment_type' => $zone->zoneTypePrice->first()->payment_type,
        ];

        return jsonResponseData(true, $data);
    }

    public function fetchRentDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'zone_type_id' => 'required|integer|exists:zone_type_prices,id',  // Ensure zone_type_id is passed
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Calculate distance and duration between pickup and dropoff
        $pickupPoint = [$request->pickup_lng, $request->pickup_lat];
        $dropoffPoint = [$request->dropoff_lng, $request->dropoff_lat];
        $data = distanceAndTimeBetweenTwoCoordinates($pickupPoint, $dropoffPoint);
        $distance = $data['distance'];
        $estimatedTime = $data['duration'];

        // Fetch the relevant ZoneTypePrice with VehicleType and optionally VehicleSubcategory
        $zoneTypePrice = ZoneTypePrice::with(['vehicleType', 'vehicleSubcategory'])
            ->where('id', $request->zone_type_id)
            ->where('active', 1)
            ->first();

        if (!$zoneTypePrice) {
            return jsonResponse(false, 'Zone type not found.', 404);
        }

        // Calculate the total price
        $basePrice = $zoneTypePrice->base_price;
        $distancePrice = $distance * $zoneTypePrice->price_per_distance;
        $totalPrice = $basePrice + $distancePrice;

        // Add admin commission
        $totalPrice += $totalPrice * ($zoneTypePrice->admin_commision / 100);

        $vehicleTypePrice = $totalPrice;
        // Add service tax and GST separately
        $serviceTax = $totalPrice * ($zoneTypePrice->service_tax / 100);
        $gst = $totalPrice * ($zoneTypePrice->gst_tax / 100);
        $totalPrice = round($totalPrice + $serviceTax + $gst, 2);

        // Prepare response data
        $responseData = [
            'zone_type_id' => $zoneTypePrice->id,
            'name' => $zoneTypePrice->vehicleType->name,
            'icon' => $zoneTypePrice->vehicleType->icon,
            'price' => $totalPrice,
            'vehicle_type_price' => $vehicleTypePrice,
            'service_tax' => $zoneTypePrice->service_tax,
            'service_tax_amount' => round($serviceTax, 2),
            'gst' => round($gst, 2),
            'cancellation_fee' => $zoneTypePrice->cancellation_fee,
            'waiting_charge' => $zoneTypePrice->waiting_charge,
            'has_subcategories' => false,
        ];

        // If vehicleSubcategory is not null, add subcategory details to the response
        if ($zoneTypePrice->vehicle_subcategory_id) {
            $responseData['has_subcategories'] = true;
            $responseData['subcategory_name'] = $zoneTypePrice->vehicleSubcategory->name;
            $responseData['short_amenities'] = $zoneTypePrice->vehicleSubcategory->short_amenties;
            $responseData['max_passengers'] = 3;
        }

        // Return the formatted data
        return jsonResponseData(true, [
            'distance' => $distance,
            'estimated_time' => $estimatedTime,
            'rent_details' => $responseData,
        ]);
    }

    public function checkPromo(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'promo_code' => 'required|string',
            'total_amount' => 'required|numeric',
            'zone_type_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the ZoneTypePrice to get the service_location_id
        $zoneTypePrice = ZoneTypePrice::find($request->zone_type_id);

        if (!$zoneTypePrice) {
            return jsonResponse(false, 'Invalid zone type ID.', 404);
        }

        // Fetch the promo code details
        $promo = Promo::where('code', $request->promo_code)
            ->where('active', 1)
            ->whereDate('from', '<=', now())
            ->whereDate('to', '>=', now())
            ->where('service_location_id', $zoneTypePrice->zone->service_location_id) // Check service location ID
            ->first();

        if (!$promo) {
            return jsonResponse(false, 'Promo code is invalid or not applicable.', 404);
        }

        // Check if the total amount is equal to or greater than the minimum trip amount
        if ($request->total_amount < $promo->minimum_trip_amount) {
            return jsonResponse(false, 'Total amount does not meet the minimum trip amount for this promo.', 400);
        }

        // Calculate the discount
        $discount = $request->total_amount * ($promo->discount_percentage / 100);

        // Ensure the discount does not exceed the maximum discount amount set
        if ($discount > $promo->maximum_discount_amount) {
            $discount = $promo->maximum_discount_amount;
        }

        // Prepare the response data with the discount amount
        $responseData = [
            'promo_code' => $promo->code,
            'discount_percentage' => $promo->discount_percentage,
            'discount_total_amount' => round($discount, 2), // Return the discount amount
        ];

        return jsonResponseData(true, $responseData);
    }

    public function bookRide(Request $request)
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'zone_type_id' => 'required|integer|exists:zone_type_prices,id',

            'pickup' => 'required|array',
            'pickup.long' => 'required_with:pickup|numeric',
            'pickup.lat' => 'required_with:pickup|numeric',
            'pickup.address' => 'required_with:pickup|string',

            'dropoff' => 'required|array',
            'dropoff.long' => 'required_with:dropoff|numeric',
            'dropoff.lat' => 'required_with:dropoff|numeric',
            'dropoff.address' => 'required_with:dropoff|string',

            'is_schedule_ride' => 'required|in:1,0',
            'scheduled_date' => 'nullable|required_if:is_schedule_ride,1|date|after_or_equal:today',
            'scheduled_time' => 'nullable|required_if:is_schedule_ride,1|date_format:H:i',

            'is_for_someone_else' => 'required|boolean',
            'ride_type' => 'required|in:simple,outstation',
            'return_trip' => 'required|boolean',
            'return_date' => 'nullable|date|after_or_equal:today',
            'return_time' => 'nullable|date_format:H:i',
            'promo_code' => 'nullable',
            'stop_points' => 'nullable|array',
            'stop_points.*.long' => 'required_with:stop_points|numeric',
            'stop_points.*.lat' => 'required_with:stop_points|numeric',
            'stop_points.*.address' => 'required_with:stop_points|string',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Check if user has a ride scheduled at the same date and time
        $existingRide = Ride::select(['id', 'ride_status'])->where('user_id', auth()->user()->id)
            ->where('scheduled_date', $request->scheduled_date)
            ->where('scheduled_time', $request->scheduled_time)
            ->first();

        if ($existingRide && $existingRide->ride_status != "canceled") {
            return jsonResponse(false, 'You already have a ride scheduled at this date and time. You need to cancel existing ride to book new at that time.', 400);
        }

        // Calculate distance and estimated time
        $pickupPoint = [$request->pickup['long'], $request->pickup['lat']];
        $dropoffPoint = [$request->dropoff['long'], $request->dropoff['lat']];



        $data = distanceAndTimeBetweenTwoCoordinates($pickupPoint, $dropoffPoint);
        $totalDistance = $data['distance'];
        $estimatedTime = $data['duration'];


        // Fetch zone and vehicle type details
        $zoneTypePrice = ZoneTypePrice::with('zone')->findOrFail($request->zone_type_id);


        // Assign the ride to the nearest driver
        $nearestDriverId = findNearestDriver($request->pickup['lat'], $request->pickup['long'], null, $zoneTypePrice->vehicle_type_id, $zoneTypePrice->vehicle_subcategory_id);

        // if (!$nearestDriverId && $request->is_schedule_ride == 0) {
        if (!$nearestDriverId) {
            return jsonResponse(false, 'No driver currenly available in your area.', 400);
        }


        $zone = $zoneTypePrice->zone;

        // Calculate the total price
        $basePrice = $zoneTypePrice->base_price;
        $distancePrice = $totalDistance * $zoneTypePrice->price_per_distance;
        $totalPrice = $basePrice + $distancePrice;

        // Add admin commission
        $totalPrice += $totalPrice * ($zoneTypePrice->admin_commision / 100);

        // Add service tax and GST separately
        $serviceTax = $totalPrice * ($zoneTypePrice->service_tax / 100);
        $gst = $totalPrice * ($zoneTypePrice->gst_tax / 100);
        $totalPrice = round($totalPrice + $serviceTax + $gst, 2);

        // Check if promo code is valid and apply discount if applicable
        $promo_id = null;
        if ($request->promo_code) {
            $promoCheck = $this->checkPromoValid($request->promo_code, $totalPrice, $zoneTypePrice->id);

            if (!$promoCheck['status']) {
                return jsonResponse(false, $promoCheck['message'], 400);
            }

            // Apply the discounted total
            $totalPrice = $promoCheck['data']['discount_total_amount'];
            $promo_id = $promoCheck['data']['promo_id'];
        }

        // Generate unique ride number and OTP
        $rideNumber = 'Ride_' . str_pad(Ride::max('id') + 1, 5, '0', STR_PAD_LEFT);
        $rideOtp = mt_rand(1000, 9999);

        // Prepare the ride data
        $rideData = [
            'ride_number' => $rideNumber,
            'ride_otp' => $rideOtp,
            'user_id' => auth()->user()->id,
            'service_location_id' => $zone->service_location_id,
            'ride_status' => 'pending',
            'vehicle_type_id' => $zoneTypePrice->vehicle_type_id,
            'vehicle_subcategory_id' => $zoneTypePrice->vehicle_subcategory_id,
            'zone_id' => $zone->id,
            'zone_type_id' => $zoneTypePrice->id,
            'is_schedule_ride' => $request->is_schedule_ride,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'ride_booked_at' => now(),
            'ride_type' => $request->ride_type ?? 'simple',
            'is_for_someone_else' => $request->is_for_someone_else ?? false,
            'rider_name' => $request->rider_name ?? null,
            'rider_phone_number' => $request->rider_phone_number ?? null,
            'additional_notes' => $request->additional_notes ?? null,
            'return_trip' => $request->return_trip ?? false,
            'return_date' => $request->return_date ?? null,
            'return_time' => $request->return_time ?? null,
            'passenger_count' => $request->passenger_count ?? 1,
            'payment_type' => $request->payment_type,
            'payment_amount' => $totalPrice,
            'final_fare' => $totalPrice, // This may be updated later with waiting charges
            'total_distance' => $totalDistance,
            'estimated_time' => $estimatedTime,
            'promo_id' => $promo_id ?? null,
            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,
        ];

        // Insert ride record
        $ride = Ride::create($rideData);

        if ($request->has('stop_points')) {
            foreach ($request->stop_points as $point) {
                $ride->rideStops()->create([
                    'stop' => $point
                ]);
            }
        }


        if ($nearestDriverId) {
            // Create a ride request for the nearest driver
            $driverRideRequest = DriverRideRequest::create([
                'ride_id' => $ride->id,
                'driver_id' => $nearestDriverId,
                'request_status' => 'pending',
            ]);

            // Fetch the nearest driver details
            $driver = Driver::select('fcm_token')->find($nearestDriverId);

            if ($driver && $driver->fcm_token) {
                // Prepare ride data for notification
                $rideNumber = $ride->ride_number; // Assuming ride number is set during ride creation
                $pickup = $ride->pickup['address'] ?? null; // Pickup location
                $dropoff = $ride->dropoff['address'] ?? null; // Drop-off location
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



        // Return ride details in response
        return jsonResponseData(true, [
            'ride_id' =>  $ride->id,
            'ride_status' =>  $ride->ride_status,
        ]);
    }

    public function checkStatus($ride_id)
    {
        // Find the ride request by ride ID
        $rideRequest = DriverRideRequest::with('driver:id,full_name,country_code,phone_number')->where('ride_id', $ride_id)->where('request_status', 'accepted')->first();

        // Check if the ride request exists
        if (!$rideRequest) {
            return jsonResponse(true, [
                'ride_id' => $ride_id,
                'status' => 'pending',
            ]);
        }

        // Return the status of the ride request
        return jsonResponse(true, [
            'ride_id' => $ride_id,
            'status' => $rideRequest->request_status,
            'driver' => $rideRequest->driver,
        ]);
    }


    public function checkPromoValid($promo_code, $total_amount, $zone_type_id)
    {

        // Fetch the ZoneTypePrice to get the service_location_id
        $zoneTypePrice = ZoneTypePrice::find($zone_type_id);

        if (!$zoneTypePrice) {
            return [
                'status' => false,
                'message' => 'Invalid zone type ID.',
            ];
        }

        // Fetch the promo code details
        $promo = Promo::where('code', $promo_code)
            ->where('active', 1)
            ->whereDate('from', '<=', now())
            ->whereDate('to', '>=', now())
            ->where('service_location_id', $zoneTypePrice->zone->service_location_id) // Check service location ID
            ->first();

        if (!$promo) {
            return [
                'status' => false,
                'message' => 'Promo code is invalid or not applicable.',
            ];
        }

        // Check if the total amount is equal to or greater than the minimum trip amount
        if ($total_amount < $promo->minimum_trip_amount) {
            return [
                'status' => false,
                'message' => 'Total amount does not meet the minimum trip amount for this promo.',
            ];
        }

        // Calculate the discount
        $discount = $total_amount * ($promo->discount_percentage / 100);

        // Ensure the discount does not exceed the maximum discount amount set
        if ($discount > $promo->maximum_discount_amount) {
            $discount = $promo->maximum_discount_amount;
        }

        // Calculate the final total after discount
        $finalTotal = $total_amount - $discount;

        // Prepare the response data
        return [
            'status' => true,
            'data' => [
                'promo_id' => $promo->id,
                'discount_total_amount' => round($finalTotal, 2),
            ]
        ];
    }


    // public function initiateRidePayment(Request $request)
    // {
    //     $rideId = $request->ride_id;
    //     $userId = auth()->id(); // Assuming the user is authenticated

    //     $ride = Ride::where('id', $rideId)->where('user_id', $userId)->with('user')->first();

    //     if (!$ride) {
    //         return jsonResponse(false, 'Ride is not booked!', 400);
    //     }

    //     if ($ride->payment_status == 'completed') {
    //         return jsonResponse(false, 'Ride payment is already completed!', 400);
    //     }

    //     $amount = $ride->payment_amount * 100; // Convert to paisa


    //     $merchantTransactionId = "TXN-" . $rideId . '-' . time();
    //     // // Include user and ride details in the message
    //     // $message = "Payment for Ride #" . $ride->ride_number .
    //     //     " - Passenger: " . $ride->user->name .
    //     //     ", Phone: " . $ride->user->phone_number .
    //     //     ", Email: " . $ride->user->email .
    //     //     ". Amount: Rs. " . ($amount / 100) . ".";

    //     // Check if a payment record already exists with status 'pending' or 'failed'
    //     $order = RidePayment::where('ride_id', $rideId)
    //         ->where('user_id', $userId)
    //         ->whereIn('status', ['pending', 'failed'])
    //         ->first();

    //     if ($order) {
    //         // Update the existing order with new details
    //         $order->update([
    //             'merchant_transaction_id' => $merchantTransactionId,
    //             'payment_amount' => $ride->payment_amount,
    //             'status' => 'pending',
    //         ]);
    //     } else {
    //         // Create a new order entry with status 'pending'
    //         $order = RidePayment::create([
    //             'user_id' => $userId,
    //             'ride_id' => $rideId,
    //             'merchant_transaction_id' => $merchantTransactionId,
    //             'payment_amount' => $ride->payment_amount,
    //             'status' => 'pending',
    //         ]);
    //     }

    //     $callbackUrl = route('phonepe-ride-payment-callback');
    //     // Call the helper function to initiate the payment
    //     $response = PhonePeHelper::makePayment(
    //         $merchantTransactionId,
    //         $userId,
    //         $amount,
    //         $callbackUrl,
    //         $ride->user->phone_number,
    //     );

    //     if (is_array($response) && !$response['status']) {
    //         // Update the order status to 'failed' if payment initiation fails
    //         $order->update(['status' => 'failed']);
    //         return jsonResponse(false, $response['message'], 400);
    //     }

    //     // If payment initiation is successful, return the response
    //     return $response;
    // }

    public function initiateRidePayment(Request $request)
    {
        $rideId = $request->ride_id;
        $userId = auth()->id(); // Assuming the user is authenticated

        $ride = Ride::where('id', $rideId)->where('user_id', $userId)->with('user')->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride is not booked!', 400);
        }

        if ($ride->payment_status == 'completed') {
            return jsonResponse(false, 'Ride payment is already completed!', 400);
        }

        $amount = $ride->final_fare  * 100;


        $merchantTransactionId = "RIDE-" . $rideId . '-' . time();

        // Check if a payment record already exists with status 'pending' or 'failed'
        $order = RidePayment::where('ride_id', $rideId)
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'failed'])
            ->first();

        if ($order) {
            // Update the existing order with new details
            $order->update([
                'merchant_transaction_id' => $merchantTransactionId,
                'payment_amount' => $amount,
                'status' => 'pending',
            ]);
        } else {
            // Create a new order entry with status 'pending'
            $order = RidePayment::create([
                'user_id' => $userId,
                'ride_id' => $rideId,
                'merchant_transaction_id' => $merchantTransactionId,
                'payment_amount' => (int) $amount,
                'status' => 'pending',
            ]);
        }

        $callbackUrl = route('phonepe-ride-payment-callback');
        // Call the helper function to initiate the payment
        $paymentData = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'saltIndex' => env('PHONEPE_KEY_INDEX'),
            'saltKey' => env('PHONEPE_API_KEY'),
            'paymentMode' => env('PHONEPE_MODE'),
            'merchantTransactionId' => $merchantTransactionId,
            'userId' => $userId,
            'amount' => $amount,
            'callbackUrl' => $callbackUrl,
            'phone_number' => $ride->user->phone_number,
        ];

        return jsonResponseWithData(true, 'Payment initialized successfully!', $paymentData);
    }

    public function gatewayRidePayment(Request $request)
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
        $ride = RidePayment::where('merchant_transaction_id', $request->merchant_transaction_id)->first();

        if (!$ride) {
            return jsonResponse(false, 'Transaction not found!', 404);
        }

        // Check the payment status from PhonePe and update the ride status
        if ($request->payment_status == "success") {
            $ride->update(['status' => 'completed', 'provider_reference_id' => $request->provider_reference_id]);

            $ride = Ride::find($ride->ride_id);
            $ride->update(['payment_status' => 'completed']);


            // Send a notification to the user about the successful payment
            $user = $ride->user; // Assuming the Ride model has a relationship with the User model
            if ($user && $user->fcm_token) {
                $userToken = $user->fcm_token;
                $title = 'Payment Successful';
                $body = 'Your payment for the ride has been completed successfully.';
                FCMService::sendNotification($userToken, $title, $body, 'notifications');

                // Log the notification in the database
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }


            return jsonResponse(true, 'Payment completed successfully', 200);
        } else {
            $ride->update(['status' => 'failed', 'provider_reference_id' => $request->provider_reference_id ?? null]);

            Ride::find($ride->ride_id)->update(['payment_status' => 'failed']);

            return jsonResponse(false, 'Payment failed!', 400);
        }
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
        $userId = auth()->id(); // Assuming the user is authenticated

        // Fetch the ride with the specified ride ID and user ID
        $ride = Ride::where('id', $rideId)->where('user_id', $userId)->first();

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


    public function ridePaymentUsingWallet(Request $request)
    {
        // Get the authenticated user
        $userId = auth()->id();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the user's wallet
        $wallet = Wallet::where('user_id', $userId)->first();

        // Check if the user has a wallet and sufficient balance
        if (!$wallet || $wallet->balance <= 0) {
            return jsonResponse(false, 'Insufficient wallet balance. Please add sufficient funds to your wallet.');
        }

        // Fetch the ride details
        $ride = Ride::where('id', $request->ride_id)
            ->where('user_id', $userId)
            ->where('payment_status', 'pending') // Only allow payment for rides with pending payment
            ->where('payment_status', '!=', 'completed') // Only allow payment for rides with pending payment
            ->where('ride_status', '!=', 'canceled')
            ->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride not found or payment is already completed.');
        }

        // Check if the user has sufficient balance for the ride
        $rideAmount = $ride->payment_amount; // Assuming 'payment_amount' is the total ride cost
        if ($wallet->balance < $rideAmount) {
            return jsonResponse(false, 'Insufficient wallet balance for this payment.');
        }

        // Deduct the amount from the user's wallet
        $wallet->balance -= $rideAmount;
        $wallet->save();

        // Update the payment status and payment type of the ride
        $ride->update([
            'payment_status' => 'completed',
            'payment_type' => 'Wallet',
        ]);

        $transactionId = Str::uuid();
        // Record the wallet transaction
        $transaction = $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $rideAmount,
            'transaction_id' => $transactionId, // Optionally, generate a unique transaction ID
            'reference' => 'Ride Payment',
            'status' => 'completed',
        ]);

        $user = auth()->user();
        if ($user && $user->fcm_token) {
            $userToken = $user->fcm_token;
            $title = 'Payment Successful';
            $body = "Your payment of ₹{$rideAmount} for the ride has been successfully completed.";
            FCMService::sendNotification($userToken, $title, $body, 'wallet');

            // Log the user notification in the database
            UserNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }

        // Return a success response with the transaction details
        return jsonResponseWithData(true, 'Payment completed successfully.', $transaction);
    }


    public function addRideRating(Request $request)
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the ride
        $ride = Ride::where('id', $request->ride_id)->first();

        if (!$ride && auth()->id() == $ride->user_id) {
            return jsonResponse(false, 'Ride not found.', 404);
        }

        // Check if the rating already exists for the ride
        $rideFeedback = RideFeedback::where('ride_id', $request->ride_id)->first();

        if ($rideFeedback) {
            // Update the existing rating
            $rideFeedback->update([
                'rating' => $request->rating,
                'feedback' => $request->feedback,
            ]);

            return jsonResponse(true, 'Ride rating updated successfully.');
        } else {
            // Create a new rating record
            RideFeedback::create([
                'ride_id' => $request->ride_id,
                'rating' => $request->rating,
                'feedback' => $request->feedback,
            ]);

            // Send notification to the driver about the new or updated rating
            $driver = $ride->driver; // Assuming the Ride model has a relationship with the Driver model
            if ($driver && $driver->fcm_token) {
                $driverToken = $driver->fcm_token;
                $title = 'New Ride Rating';
                $body = "You've received a new rating of {$request->rating} stars.";
                FCMService::sendNotification($driverToken, $title, $body, 'notifications');

                // Log the driver notification in the database
                DriverNotification::create([
                    'driver_id' => $driver->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }


            return jsonResponse(true, 'Ride rating submitted successfully.');
        }
    }

    public function getActiveRide()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Get today's date and the current time
        // $today = Carbon::today()->toDateString();
        // $currentTime = Carbon::now()->format('H:i:s');


        // Fetch the user's active ride with related details
        $ride = Ride::with([
            'driver:id,full_name,phone_number,country_code',
            'vehicleType:id,name',
            'vehicleSubcategory:id,name,image'
        ])
            ->where('user_id', $userId)
            ->whereIn('ride_status', ['driver_arrived', 'in_progress', 'waiting'])
            // ->whereDate('scheduled_date', $today)
            // ->whereTime('scheduled_time', '<=', $currentTime)
            ->latest('ride_booked_at')
            ->first();

        // Check if an active ride exists
        if (!$ride) {
            return jsonResponse(false, 'No active ride found.', 404);
        }

        // Prepare the response data
        $responseData = [
            'ride_id' => $ride->id,
            'ride_number' => $ride->ride_number,
            'ride_status' => $ride->ride_status,
            'payment_status' => $ride->payment_status,
            'ride_otp' => $ride->ride_otp,
            'scheduled_date' => $ride->scheduled_date,
            'scheduled_time' => $ride->scheduled_time,
            'ride_type' => $ride->ride_type,
            'total_distance' => $ride->total_distance,
            'estimated_time' => $ride->estimated_time,
            'pickup' => $ride->pickup,
            'dropoff' => $ride->dropoff,
            'driver' => $ride->driver ? [
                'name' => $ride->driver->full_name,
                'country_code' => $ride->driver->country_code,
                'phone_number' => $ride->driver->phone_number
            ] : null,
            'vehicle_type' => $ride->vehicleType->name ?? null,
            'vehicle_subcategory' => $ride->vehicleSubcategory->name ?? null,
            'vehicle_subcategory_image' => $ride->vehicleSubcategory->image ?? null,
        ];

        return jsonResponseData(true, $responseData);
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
        $userId = auth()->id();

        // Find the ride and ensure it belongs to the authenticated driver and is not completed
        $ride = Ride::where('id', $request->ride_id)
            ->where('user_id', $userId)
            ->whereNotIn('ride_status', ['completed', 'in_progress', 'canceled'])
            ->first();

        if (!$ride) {
            return jsonResponse(false, 'Ride cannot be canceled.');
        }

        // Update the ride status to 'canceled', set the cancellation timestamp, and store the reason
        $ride->update([
            'ride_status' => 'canceled',
            'ride_cancelled_at' => now(),
            'cancel_type' => $request->cancel_type,
            'cancel_reason' => $request->cancel_reason,
            'canceled_by' => 'user',
        ]);

        $drivers = DriverRideRequest::where('ride_id', $ride->id)
            ->whereIn('request_status', ['pending', 'accepted'])
            ->pluck('driver_id');

        DriverRideRequest::where('driver_id', $ride->driver_id)
            ->where('ride_id', $ride->id)
            ->where('request_status', "accepted")
            ->update(['request_status' => 'canceled']);


        foreach ($drivers as $driverId) {
            $driver = Driver::select('fcm_token')->find($driverId);

            if ($driver && $driver->fcm_token) {
                $title = "Ride Canceled";
                $body = "The ride (ID: {$ride->ride_number}) has been canceled by the user.";

                // Send notification using the FCM service
                FCMService::sendNotification($driver->fcm_token, $title, $body, 'canceled_rides');

                DriverNotification::create([
                    'driver_id' => $driverId,
                    'title' => $title,
                    'body' => $body,
                ]);
            }
        }


        return jsonResponse(true, 'Ride has been canceled successfully.');
    }



    public function getUpcomingRides()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Get today's date and the current time
        // $today = Carbon::today()->toDateString();
        // $currentTime = Carbon::now()->format('H:i:s');

        // Fetch upcoming rides
        $rides = Ride::select([
            'id',
            'pickup',
            'dropoff',
            'ride_status',
            'scheduled_date',
            'scheduled_time',
            'vehicle_type_id',
            'vehicle_subcategory_id'
        ])
            ->with([
                'vehicleType:id,name,icon',
                'vehicleSubcategory:id,name,image'
            ])
            ->where('user_id', $userId)
            ->whereIn('ride_status', ['pending', 'accepted', 'driver_arrived', 'in_progress'])
            // ->where(function ($query) use ($today, $currentTime) {
            //     $query->whereDate('scheduled_date', '>', $today)
            //         ->orWhere(function ($query) use ($today, $currentTime) {
            //             $query->whereDate('scheduled_date', $today)
            //                 ->whereTime('scheduled_time', '>', $currentTime);
            //         });
            // })
            // ->orderBy('scheduled_date')
            // ->orderBy('scheduled_time')
            ->latest()
            ->get();

        return jsonResponseData(true, $rides);
    }

    public function getCompletedRides()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Fetch completed rides
        $rides = Ride::select([
            'id',
            'pickup',
            'dropoff',
            'ride_status',
            'scheduled_date',
            'scheduled_time',
            'vehicle_type_id',
            'vehicle_subcategory_id'
        ])
            ->with([
                'vehicleType:id,name,icon',
                'vehicleSubcategory:id,name,image'
            ])
            ->where('user_id', $userId)
            ->where('ride_status', 'completed')
            ->orderByDesc('ride_completed_at')
            ->get();

        return jsonResponseData(true, $rides);
    }

    public function getCanceledRides()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Fetch canceled rides
        $rides = Ride::select([
            'id',
            'pickup',
            'dropoff',
            'ride_status',
            'scheduled_date',
            'scheduled_time',
            'vehicle_type_id',
            'vehicle_subcategory_id'
        ])
            ->with([
                'vehicleType:id,name,icon',
                'vehicleSubcategory:id,name,image'
            ])
            ->where('user_id', $userId)
            ->whereIn('ride_status', ['canceled', 'no_show'])
            ->orderByDesc('ride_cancelled_at')
            ->get();

        return jsonResponseData(true, $rides);
    }

    public function getRideDetails(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'ride_id' => 'required|integer|exists:rides,id',
        ]);

        // Get the authenticated user's ID
        $userId = auth()->id();

        // Get the ride ID from the request payload
        $rideId = $request->ride_id;

        // Fetch the ride details
        $ride = Ride::with([
            'vehicleType:id,name,icon',              // Fetch vehicle type details
            'vehicleSubcategory:id,name,image',      // Fetch vehicle subcategory details
            'driver:id,full_name,phone_number,profile_photo',  // Fetch driver details
            'rideStops:ride_id,stop'
        ])
            ->where('id', $rideId)
            ->where('user_id', $userId)
            ->first();

        // Check if the ride was found
        if (!$ride) {
            return jsonResponse(false, 'Ride not found or not authorized', 404);
        }

        // Return the ride details
        return jsonResponseData(true, $ride);
    }
}
