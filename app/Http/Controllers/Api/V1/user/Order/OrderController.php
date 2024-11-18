<?php

namespace App\Http\Controllers\Api\V1\User\Order;

use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use App\Models\Driver\DriverNotification;
use App\Models\Product\Coupon;
use App\Models\Product\DriverOrderRequest;
use App\Models\Product\Order;
use App\Models\Product\OrderItem;
use App\Models\Product\OrderPayment;
use App\Models\Product\Product;
use App\Models\Service\Zone;
use App\Models\UserNotification;
use App\Models\Wallet;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'payment_method' => 'required|in:gateway,wallet,cash',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'coupon' => 'nullable|string|exists:coupons,coupon',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $deliveryPoint = [$request->longitude, $request->latitude];

        $zones = Zone::selectRaw('id, service_location_id, name, ST_AsText(coordinates) as coordinates, active')
            ->where('active', 1)
            ->get();

        $zone = $zones->first(function ($zone) use ($deliveryPoint) {
            return isPointInZone($deliveryPoint, $zone->coordinates);
        });


        if (!$zone) {
            return jsonResponse(false, __('Delivery for this order currently not available in your area.'), 404);
        }

        // Fetch drivers based on the zone and create driver requests
        // $drivers = Driver::where('zone_id', $zone->id)->where('status', 'approved')->where('role', '!=', 'instructor')->get();
        $drivers = Driver::where('status', 'approved')->where('role', '!=', 'instructor')->get();

        if (!$drivers) {
            return jsonResponse(false, __('No driver available currently in your area.'), 404);
        }


        $user = auth()->user();
        $totalPrice = 0;

        // Calculate total price
        foreach ($request->products as $product) {
            $productModel = Product::find($product['id']);
            $totalPrice += $productModel->price * $product['quantity'];
        }

        // Apply coupon if provided
        $discount = 0;
        if ($request->coupon) {
            $coupon = Coupon::where('coupon', $request->coupon)->where('status', 'active')->first();
            if ($coupon) {
                $discount = ($totalPrice * $coupon->discount) / 100;
                $totalPrice -= $discount;
            }
        }

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'address' => $request->address,
            'phone' => $request->phone,
            'payment_method' => $request->payment_method,
            'total_price' => $totalPrice,
        ]);

        // Create order items
        foreach ($request->products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product['id'],
                'quantity' => $product['quantity'],
                'price' => Product::find($product['id'])->price,
            ]);
        }

        // Send push notification to the user
        if ($user && $user->fcm_token) {
            $userToken = $user->fcm_token;
            $title = 'Order Placed';
            $body = "Your order of ₹{$totalPrice} has been successfully placed.";
            FCMService::sendNotification($userToken, $title, $body, 'order');

            // Log the user notification in the database
            UserNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }


        foreach ($drivers as $driver) {
            // Create a driver order request
            DriverOrderRequest::create([
                'order_id' => $order->id,
                'driver_id' => $driver->id,
                'status' => 'pending',
            ]);

            // Send push notification to the driver
            if ($driver->fcm_token) {
                $driverToken = $driver->fcm_token;
                $driverTitle = 'New Order Available';
                $driverBody = "A new order is available in your zone. Please check your driver app for details.";
                FCMService::sendNotification($driverToken, $driverTitle, $driverBody, 'new_order');

                // Log the driver notification in the database (if you have a similar table for drivers)
                DriverNotification::create([
                    'driver_id' => $driver->id,
                    'title' => $driverTitle,
                    'body' => $driverBody,
                ]);
            }
        }


        return response()->json(['message' => 'Order placed successfully.', 'order' => $order], 201);
    }

    public function fetchOrders(Request $request)
    {
        // Validate the request payload to ensure the order status is valid
        $validator = Validator::make($request->all(), [
            'order_status' => 'nullable|string|in:pending,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the user's orders, optionally filtering by order status
        $orders = Order::with(['orderItems', 'driver'])
            ->when($request->order_status, function ($query) use ($request) {
                $query->where('order_status', $request->order_status);
            })
            ->where('user_id', auth()->id()) // Fetch orders for the authenticated user
            ->latest()
            ->get();

        // Calculate total quantity and total amount for each order
        $orders = $orders->map(function ($order) {
            $totalQuantity = $order->orderItems->sum('quantity');
            // $totalAmount = $order->orderItems->sum(function ($item) {
            //     return $item->price * $item->quantity;
            // });

            // Add calculated fields to each order
            $order->total_quantity = $totalQuantity;
            // $order->total_amount = $totalAmount;

            return $order;
        });

        // Return the response with the order list
        return jsonResponseWithData(true, 'Order list fetched successfully.', $orders);
    }

    public function fetchOrderDetails($id)
    {

        // Fetch the order details, including order items and driver information
        $order = Order::with(['orderItems.product', 'driver:id,full_name,phone_number,profile_photo'])
            ->where('id', $id)
            ->where('user_id', auth()->id()) // Ensure the order belongs to the authenticated user
            ->first();

        if (!$order) {
            return jsonResponse(false, 'Order not found.', 404);
        }

        // Calculate total quantity for the order
        $totalQuantity = $order->orderItems->sum('quantity');
        $order->total_quantity = $totalQuantity;


        // Return the order details with the calculated total quantity
        return jsonResponseWithData(true, 'Order details fetched successfully.', $order);
    }

    public function cancelOrder(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the order
        $order = Order::where('id', $request->order_id)
            ->where('user_id', auth()->id()) // Ensure the order belongs to the authenticated user
            ->first();

        if (!$order) {
            return jsonResponse(false, 'Order not found or unauthorized access.', 404);
        }

        // Check if the order is already cancelled or completed
        if ($order->order_status == 'cancelled') {
            return jsonResponse(false, 'Order is already cancelled.', 400);
        }

        if ($order->order_status == 'delivered') {
            return jsonResponse(false, 'Delivered orders cannot be cancelled.', 400);
        }

        // Update the order status to cancelled
        $order->update([
            'order_status' => 'cancelled',
            'delivery_status' => 'cancelled',
        ]);

        // Notify the driver who accepted the order
        $acceptedDriverRequest = DriverOrderRequest::where('order_id', $order->id)
            ->where('status', 'accepted')
            ->first();

        // Expire all driver requests associated with the order
        DriverOrderRequest::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->update(['status' => 'expired']);



        if ($acceptedDriverRequest) {
            $driver = $acceptedDriverRequest->driver;

            if ($driver && $driver->fcm_token) {
                $driverToken = $driver->fcm_token;
                $title = 'Order Cancelled';
                $body = "The order #{$order->id} you accepted has been cancelled by the user.";
                FCMService::sendNotification($driverToken, $title, $body, 'orders');

                // Log the driver notification in the database
                DriverNotification::create([
                    'driver_id' => $driver->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }
        }

        // Send push notification to the user about the order cancellation
        $user = auth()->user();
        if ($user && $user->fcm_token) {
            $userToken = $user->fcm_token;
            $title = 'Order Cancelled';
            $body = "Your order #{$order->id} has been successfully cancelled.";
            FCMService::sendNotification($userToken, $title, $body, 'order_cancelled');

            // Log the user notification in the database
            UserNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }

        // Return the response
        return jsonResponse(true, 'Order has been cancelled successfully.');
    }

    public function orderPaymentUsingWallet(Request $request)
    {
        // Get the authenticated user
        $userId = auth()->id();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
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

        // Fetch the Order details
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $userId)
            ->where('payment_status', 'pending') // Only allow payment for hires with pending payment
            ->first();

        if (!$order) {
            return jsonResponseData(false, 'Order not found or payment is already completed.');
        }


        // Calculate the total payment amount
        $totalAmount = $order->total_price;
        // $adminCommission = $price * ($order->admin_commission / 100);
        // $serviceTax = $price * ($order->service_tax / 100);
        // $gst = $price * ($order->gst / 100);
        // $totalAmount = $price + $adminCommission + $serviceTax + $gst;

        // Check if the user has sufficient balance for the Order
        if ($wallet->balance < $totalAmount) {
            return jsonResponseData(false, 'Insufficient wallet balance for this payment.');
        }

        // Deduct the amount from the user's wallet
        $wallet->balance -= $totalAmount;
        $wallet->save();

        // Update the payment status of the Order
        $order->update([
            'payment_status' => 'completed',
        ]);

        // Record the wallet transaction
        $transactionId = Str::uuid();
        $transaction = $wallet->transactions()->create([
            'type' => 'debit',
            'amount' => $totalAmount,
            'transaction_id' => $transactionId, // Optionally, generate a unique transaction ID
            'reference' => 'Order Payment',
            'status' => 'completed',
        ]);

        OrderPayment::create([
            'user_id' => $userId,
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'payment_type' => 'wallet',
            'amount' => (int) $totalAmount,
            'status' => 'completed',
        ]);
        // Return a success response with the transaction details
        return jsonResponseWithData(true, 'Payment completed successfully.', $transaction);
    }

    public function initializeOrderPayment(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }


        // Fetch the Order details
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('payment_status', 'pending') // Only allow payment for hires with pending payment
            ->first();

        if (!$order) {
            return jsonResponseData(false, 'Order not found or payment is already completed.');
        }

    
        // Calculate the total payment amount
        $totalAmount = $order->total_price;
        // $adminCommission = $price * ($order->admin_commission / 100);
        // $serviceTax = $price * ($order->service_tax / 100);
        // $gst = $price * ($order->gst / 100);
        // $totalAmount = $price + $adminCommission + $serviceTax + $gst;

        $merchantTransactionId = "TXN-" . $order->id . '-' . time();

        $amount = $totalAmount * 100;

        // Create a payment record with pending status
        OrderPayment::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
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

    public function gatewayOrderPaymentCallback(Request $request)
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
        $order_payment = OrderPayment::where('transaction_id', $request->merchant_transaction_id)->first();

        if (!$order_payment) {
            return jsonResponse(false, 'Transaction not found!', 404);
        }

        // Check the payment status from PhonePe and update the ride status
        if ($request->payment_status == "success") {
            $order_payment->update(['status' => 'completed', 'provider_reference_id' => $request->provider_reference_id]);


            $order = Order::find($order_payment->order_id);
            $order->update(['payment_status' => 'completed']);


            // Send a notification to the user about the successful payment
            $user = $order_payment->user;
            if ($user && $user->fcm_token) {
                $userToken = $user->fcm_token;
                $title = 'Payment Successful';
                $body = 'Your payment for the Order has been completed successfully.';
                FCMService::sendNotification($userToken, $title, $body, 'courses');

                // Log the notification in the database
                UserNotification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }


            return jsonResponseWithData(true, 'Payment completed successfully', $order_payment);
        } else {
            $order_payment->update(['status' => 'failed', 'provider_reference_id' => $request->provider_reference_id ?? null]);

            return jsonResponse(false, 'Payment failed!', 400);
        }
    }
}
