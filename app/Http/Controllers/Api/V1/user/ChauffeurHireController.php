<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur\ChauffeurHire;
use App\Models\Chauffeur\ChauffeurHirePayment;
use App\Models\UserNotification;
use App\Models\Wallet;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;



class ChauffeurHireController extends Controller
{
    public function bookChauffeur(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'pickup' => 'required|max:500',
            'dropoff' => 'required|max:500',
            'pickup_location_type' => 'nullable|string|max:255',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'destination_location_type' => 'nullable|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'vehicle_type' => 'required|string|max:255',
            'preferred_vehicle' => 'nullable|string|max:255',
            'chauffeur_type' => 'required|in:with_vehicle,without_vehicle',
            'hire_type' => 'required|string|max:255',
            'event_type' => 'nullable|string|max:255',
            'child_seats' => 'nullable|integer|min:0',
            'specific_vehicle_models' => 'nullable|string',
            'additional_amenities' => 'nullable|string',
            'additional_requests' => 'nullable|string',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return validationError($validator->errors());
        }


        // Create a new ChauffeurHire record
        ChauffeurHire::create([
            'user_id' => auth()->id(),
            'chauffeur_id' => $request->chauffeur_id,
            'pickup' => $request->pickup,
            'dropoff' => $request->dropoff,
            'pickup_location_type' => $request->pickup_location_type,
            'destination_location_type' => $request->destination_location_type,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'vehicle_type' => $request->vehicle_type,
            'preferred_vehicle' => $request->preferred_vehicle,
            'chauffeur_type' => $request->chauffeur_type,
            'hire_type' => $request->hire_type,
            'event_type' => $request->event_type,
            'child_seats' => $request->child_seats,
            'specific_vehicle_models' => $request->specific_vehicle_models,
            'additional_amenities' => $request->additional_amenities,
            'additional_requests' => $request->additional_requests,
        ]);

        return jsonResponse(true, 'Chauffeur booked successfully');
    }

    public function getPendingChauffeurHires()
    {
        $userId = auth()->id(); // Get the authenticated user ID

        $chauffeurHires = ChauffeurHire::select(['id', 'pickup', 'dropoff', 'start_time', 'end_time', 'chauffeur_id'])->where('user_id', $userId)
            ->where('status', 'pending')
            ->with([
                'chauffeur:id,driver_id,image,tagline',
                'chauffeur.driver:id,full_name,phone_number,vehicle_subcategory_id,vehicle_type_id',
                'chauffeur.driver.vehicleSubcategory:id,name,image,short_amenties,specifications',
                'chauffeur.driver.vehicleType:id,name,icon'
            ])
            ->get();

        return jsonResponseData(true, ['chauffeur_hires' => $chauffeurHires]);
    }

    public function getActiveChauffeurHires()
    {
        $userId = auth()->id(); // Get the authenticated user ID

        $chauffeurHires = ChauffeurHire::select(['id', 'pickup', 'dropoff', 'start_time', 'end_time', 'chauffeur_id'])->where('user_id', $userId)
            ->where('status', 'approved')
            ->with([
                'chauffeur:id,driver_id,image,tagline',
                'chauffeur.driver:id,full_name,phone_number,vehicle_subcategory_id,vehicle_type_id',
                'chauffeur.driver.vehicleSubcategory:id,name,image,short_amenties,specifications',
                'chauffeur.driver.vehicleType:id,name,icon'
            ])
            ->get();

        return jsonResponseData(true, ['chauffeur_hires' => $chauffeurHires]);
    }

    public function getOtherStatusChauffeurHires()
    {
        $userId = auth()->id(); // Get the authenticated user ID

        $statuses = ['canceled', 'rejected', 'service_stopped', 'completed'];

        $chauffeurHires = ChauffeurHire::select(['id', 'pickup', 'dropoff', 'start_time', 'end_time', 'chauffeur_id'])->where('user_id', $userId)
            ->whereIn('status', $statuses)
            ->with([
                'chauffeur:id,driver_id,image,tagline',
                'chauffeur.driver:id,full_name,phone_number,vehicle_subcategory_id,vehicle_type_id',
                'chauffeur.driver.vehicleSubcategory:id,name,image,short_amenties,specifications',
                'chauffeur.driver.vehicleType:id,name,icon'
            ])
            ->get();

        return jsonResponseData(true, ['chauffeur_hires' => $chauffeurHires]);
    }



    public function ChauffeurHiresDetails($id)
    {
        $userId = auth()->id(); // Get the authenticated user ID

        $chauffeurHires = ChauffeurHire::where('id', $id)
            ->where('user_id', $userId)
            ->with([
                'chauffeur',
                'chauffeur.driver:id,full_name,phone_number,vehicle_subcategory_id,vehicle_type_id',
                'chauffeur.driver.vehicleSubcategory:id,name,image,short_amenties,specifications',
                'chauffeur.driver.vehicleType:id,name,icon'
            ])
            ->get();

        return jsonResponseData(true, ['chauffeur_hires' => $chauffeurHires]);
    }

    public function updateStatus(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'chauffeur_hire_id' => 'required|exists:chauffeur_hire,id',
            'status' => 'required|in:service_stopped,canceled',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated user ID
        $userId = auth()->id();

        // Find the chauffeur hire request
        $chauffeurHire = ChauffeurHire::where('id', $request->chauffeur_hire_id)
            ->where('user_id', $userId)
            ->first();

        if (!$chauffeurHire) {
            return jsonResponse(false, 'Chauffeur hire not found or not authorized.');
        }

        // Update the status to service_stopped
        $chauffeurHire->update(['status' => $request->status]);

        return jsonResponse(true, 'Chauffeur hire status updated successfully.');
    }

    public function chauffeurPaymentUsingWallet(Request $request)
    {
        // Get the authenticated user
        $userId = auth()->id();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'chauffeur_hire_id' => 'required|exists:chauffeur_hire,id',
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

        // Fetch the chauffeur hire details
        $chauffeurHire = ChauffeurHire::where('id', $request->chauffeur_hire_id)
            ->where('user_id', $userId)
            ->where('payment_status', 'pending') // Only allow payment for hires with pending payment
            ->first();

        if (!$chauffeurHire) {
            return jsonResponseData(false, 'Chauffeur hire not found or payment is already completed.');
        }

        if (!$chauffeurHire->price) {
            return jsonResponseData(false, 'Chauffeur hire booking payment is not yet added by admin');
        }

        // Calculate the total payment amount
        $price = $chauffeurHire->price;
        $adminCommission = $price * ($chauffeurHire->admin_commission / 100);
        $serviceTax = $price * ($chauffeurHire->service_tax / 100);
        $gst = $price * ($chauffeurHire->gst / 100);
        $totalAmount = $price + $adminCommission + $serviceTax + $gst;

        // Check if the user has sufficient balance for the chauffeur hire
        if ($wallet->balance < $totalAmount) {
            return jsonResponseData(false, 'Insufficient wallet balance for this payment.');
        }

        // Deduct the amount from the user's wallet
        $wallet->balance -= $totalAmount;
        $wallet->save();

        // Update the payment status of the chauffeur hire
        $chauffeurHire->update([
            'payment_status' => 'completed',
        ]);

        // Record the wallet transaction
        $transactionId = Str::uuid();
        $transaction = $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $totalAmount,
            'transaction_id' => $transactionId, // Optionally, generate a unique transaction ID
            'reference' => 'Chauffeur Hire Payment',
            'status' => 'completed',
        ]);

        ChauffeurHirePayment::create([
            'user_id' => $userId,
            'chauffeur_hire_id' => $chauffeurHire->id,
            'transaction_id' => $transactionId,
            'payment_type' => 'wallet',
            'amount' => (int) $totalAmount,
            'status' => 'completed',
        ]);
        // Return a success response with the transaction details
        return jsonResponseWithData(true, 'Payment completed successfully.', $transaction);
    }
    
    public function initializeChauffeurPayment(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'chauffeur_hire_id' => 'required|exists:chauffeur_hire,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }


        // Fetch the chauffeur hire details
        $chauffeurHire = ChauffeurHire::where('id', $request->chauffeur_hire_id)
            ->where('user_id', $user->id)
            ->where('payment_status', 'pending') // Only allow payment for hires with pending payment
            ->first();

        if (!$chauffeurHire) {
            return jsonResponseData(false, 'Chauffeur hire not found or payment is already completed.');
        }

        if (!$chauffeurHire->price) {
            return jsonResponseData(false, 'Chauffeur hire booking payment is not yet added by admin');
        }
        // Calculate the total payment amount
        $price = $chauffeurHire->price;
        $adminCommission = $price * ($chauffeurHire->admin_commission / 100);
        $serviceTax = $price * ($chauffeurHire->service_tax / 100);
        $gst = $price * ($chauffeurHire->gst / 100);
        $totalAmount = $price + $adminCommission + $serviceTax + $gst;

        $merchantTransactionId = "TXN-" . $chauffeurHire->id . '-' . time();

        $amount = $totalAmount * 100;

        // Create a payment record with pending status
        ChauffeurHirePayment::create([
            'user_id' => $user->id,
            'chauffeur_hire_id' => $chauffeurHire->id,
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

    public function gatewayChauffeurPaymentCallback(Request $request)
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
        $chauffeur_hire_payment = ChauffeurHirePayment::where('transaction_id', $request->merchant_transaction_id)->first();

        if (!$chauffeur_hire_payment) {
            return jsonResponse(false, 'Transaction not found!', 404);
        }

        // Check the payment status from PhonePe and update the ride status
        if ($request->payment_status == "success") {
            $chauffeur_hire_payment->update(['status' => 'completed', 'provider_reference_id' => $request->provider_reference_id]);


            $chauffeur_hire = ChauffeurHire::find($chauffeur_hire_payment->chauffeur_hire_id);
            $chauffeur_hire->update(['payment_status' => 'completed']);


            // Send a notification to the user about the successful payment
            $user = $chauffeur_hire_payment->user;
            if ($user && $user->fcm_token) {
                $userToken = $user->fcm_token;
                $title = 'Payment Successful';
                $body = 'Your payment for the chauffeur hire has been completed successfully.';
                FCMService::sendNotification($userToken, $title, $body, 'courses');

                // Log the notification in the database
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }


            return jsonResponseWithData(true, 'Payment completed successfully', $chauffeur_hire_payment);
        } else {
            $chauffeur_hire_payment->update(['status' => 'failed', 'provider_reference_id' => $request->provider_reference_id ?? null]);

            return jsonResponse(false, 'Payment failed!', 400);
        }
    }
}
