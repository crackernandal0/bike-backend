<?php

namespace App\Http\Controllers;

use App\Helpers\Payment\PhonePeHelper;
use App\Models\DrivingSchool\CoursePayment;
use App\Models\DrivingSchool\Enrollment;
use App\Models\Ride\DriverRideRequest;
use App\Models\Ride\Ride;
use App\Models\Ride\RidePayment;
use App\Models\Transaction;
use App\Models\UserNotification;
use App\Services\FCMService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class PhonePeController extends Controller
{

    public function callback(Request $request)
    {
        $data = $request->all();

        // Retrieve the order using the merchant transaction ID
        $order = RidePayment::where('merchant_transaction_id', $data['transactionId'])->first();

        if (!$order) {
            return jsonResponse(false, 'Order not found', 404);
        }

        // Check the payment status from PhonePe and update the order status
        if (isset($data['code']) && $data['code'] == "PAYMENT_SUCCESS") {
            $order->update(['status' => 'completed', 'provider_reference_id' => $data['providerReferenceId']]);

            $ride = Ride::find($order->ride_id);
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
            $order->update(['status' => 'failed', 'provider_reference_id' => $data['providerReferenceId'] ?? null]);

            Ride::find($order->ride_id)->update(['payment_status' => 'failed']);

            return jsonResponse(false, 'Payment failed!', 400);
        }
    }

    // Gateway callback handler
    public function coursePaymentCallback(Request $request)
    {
        $data = $request->all();
        $payment = CoursePayment::where('transaction_id', operator: $data['transactionId'])->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        if ($data['code'] == "PAYMENT_SUCCESS") {

            $payment->update(['status' => 'completed', 'provider_reference_id' => $data['providerReferenceId']]);

            Enrollment::where('course_id', $payment->course_id)->where('user_id', $payment->user_id)->update(['status' => 'enrolled']);

            return jsonResponseWithData(true, 'Payment completed successfully', $payment);
        } else {
            $payment->update(['status' => 'failed', 'provider_reference_id' => $data['providerReferenceId'] ?? null]);

            Enrollment::where('course_id', $payment->course_id)->where('user_id', $payment->user_id)->update(['status' => 'failed']);

            return response()->json(['success' => false, 'message' => 'Payment failed!']);
        }
    }

    public function rideRefundPayment(Request $request)
    {
        $order = RidePayment::where('merchant_transaction_id', $request->transaction_id)->first();

        if (!$order) {
            return jsonResponse(false, 'Order not found', 404);
        }

        if ($order->status !== 'completed') {
            return jsonResponse(false, 'Cannot refund an incomplete order', 400);
        }

        $client = new Client();
        $apiKey = env('PHONEPE_API_KEY');
        $baseUrl = env('PHONEPE_MODE') === 'prod'
            ? 'https://api.phonepe.com/apis/hermes/pg/v1/refund'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/refund';



        $refundData = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'merchantUserId' => $order->user_id,
            'originalTransactionId' => $order->provider_reference_id,
            'merchantTransactionId' => $order->merchant_transaction_id,
            'amount' => 92687,
            'callbackUrl' => 'http://127.0.0.1:8000/phonepe-callback',
        ];



        $payload = base64_encode(json_encode($refundData));
        $finalPayload = $payload . "/pg/v1/refund" . $apiKey;
        $sha256 = hash("sha256", $finalPayload);
        $xVerify = $sha256 . '###' . env('PHONEPE_KEY_INDEX');

        try {
            $response = $client->post($baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $xVerify,
                    'accept' => 'application/json',
                ],
                'json' => ['request' => $payload],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['success']) && $responseBody['success'] == 1) {
                $order->update(['status' => 'refunded']);
                return jsonResponse(true, 'Refund successful', 200);
            } else {
                return jsonResponse(false, $responseBody['message'] ?? 'Refund failed', 400);
            }
        } catch (\Exception $e) {
            return jsonResponse(false, 'PhonePe Refund Error: ' . $e->getMessage(), 500);
        }
    }


    public function refundPayment(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();

        if (!$transaction) {
            return jsonResponse(false, 'Transaction not found', 404);
        }

        if ($transaction->status !== 'completed') {
            return jsonResponse(false, 'Cannot refund an incomplete transaction', 400);
        }

        $client = new Client();
        $apiKey = env('PHONEPE_API_KEY');
        $baseUrl = env('PHONEPE_MODE') === 'prod'
            ? 'https://api.phonepe.com/apis/hermes/pg/v1/refund'
            : 'https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/refund';

        $refundData = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'merchantTransactionId' => $transaction->transaction_id,
            'amount' => $transaction->amount,
        ];

        $payload = base64_encode(json_encode($refundData));
        $finalPayload = $payload . "/pg/v1/refund" . $apiKey;
        $sha256 = hash("sha256", $finalPayload);
        $xVerify = $sha256 . '###' . env('PHONEPE_KEY_INDEX');

        try {
            $response = $client->post($baseUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-VERIFY' => $xVerify,
                    'accept' => 'application/json',
                ],
                'json' => ['request' => $payload],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['success']) && $responseBody['success'] == 1) {
                // $transaction->update(['status' => 'refunded']);
                dd($responseBody);
                return jsonResponse(true, 'Refund successful', 200);
            } else {
                return jsonResponse(false, $responseBody['message'] ?? 'Refund failed', 400);
            }
        } catch (\Exception $e) {
            // Log::error('PhonePe Refund Error: ' . $e->getMessage());
            return jsonResponse(false, 'PhonePe Refund Error: ' . $e->getMessage(), 500);
        }
    }

    public function handleWalletPaymentCallback(Request $request)
    {
        $data = $request->all();

        if (isset($data['code']) && $data['code'] == "PAYMENT_SUCCESS") {
            // Locate the transaction and mark it as successful
            $transaction = Transaction::where('transaction_id', $data['transactionId'])->firstOrFail();

            // Update the transaction status to 'completed'
            $transaction->update(['status' => 'completed']);

            // Add the amount to the user's wallet balance
            $wallet = $transaction->wallet;
            $wallet->balance += $transaction->amount;

            $wallet->save();

            return jsonResponseData(true, 'Payment successful.');
        } else {
            // Payment failed
            return jsonResponseData(false, 'Payment failed.');
        }
    }
}
