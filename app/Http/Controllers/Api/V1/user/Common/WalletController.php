<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Helpers\Payment\PhonePeHelper;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WalletController extends Controller
{
    public function addBalance(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Check if the user already has a wallet, if not create one with 0 balance
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'is_active' => true]
        );

        $amount = $request->amount * 100; // Amount in paisa for PhonePe

        $merchantTransactionId = "TXN-" . rand(11111, 99999) . '-' . time();
        // Create the transaction with pending status
        $transaction = $wallet->transactions()->create([
            'type' => 'credit',
            'amount' => $request->amount,
            'transaction_id' => $merchantTransactionId,
            'reference' => 'Wallet Top-up',
            'status' => 'pending',
        ]);
        $callbackUrl = route('wallet-payment-callback');


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

        return jsonResponseWithData(true, 'Payment initialized successfully!', $paymentData);
    }

    public function walletPaymentCallback(Request $request)
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

        $transaction = Transaction::where('transaction_id', $request->merchant_transaction_id)->first();

        if (!$transaction) {
            return jsonResponse(false, 'Transaction not found!', 404);
        }

        if ($request->payment_status == "success") {

            // Update the transaction status to 'completed'
            $transaction->update(['status' => 'completed']);

            // Add the amount to the user's wallet balance
            $wallet = $transaction->wallet;
            $wallet->balance += $transaction->amount;

            $wallet->save();

            return jsonResponseData(true, 'Payment successful.');
        } else {
            // Payment failed
            $transaction->update(['status' => 'failed']);
            return jsonResponseData(false, 'Transaction failed. Please try again or contact us for support.');
        }
    }

    public function withdrawBalance(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $wallet = Wallet::where('user_id', $user->id)->firstOrFail();
        $amount = $request->amount;

        if ($wallet->balance < $amount) {
            return jsonResponseData(false, 'Insufficient balance');
        }

        // Deduct the amount from wallet
        $wallet->balance -= $amount;
        $wallet->save();

        // Create a transaction record
        $transaction = $wallet->transactions()->create([
            'type' => 'withdraw',
            'amount' => $amount,
            'transaction_id' => null,
            'reference' => 'Withdrawal',
            'status' => 'completed',
        ]);

        return jsonResponseData(true, $transaction);
    }


    public function fetchTransactions()
    {
        $user = auth()->user();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'is_active' => true]
        );

        $transactions = $wallet->transactions()->latest()->get();

        return jsonResponseData(true, ['wallet' => $wallet, 'transactions' => $transactions]);
    }
}
