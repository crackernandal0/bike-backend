<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\DriverNotification;
use App\Models\Product\DriverOrderRequest;
use App\Models\Product\Order;
use App\Models\UserNotification;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function fetchOrderRequests()
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();


        // Fetch order requests for the driver with associated order details
        $orderRequests = DriverOrderRequest::with('order')
            ->where('driver_id', $driverId)
            ->where('status', 'pending')
            ->get();

        // Prepare response data with total quantity and amount per order


        return jsonResponseWithData(true, 'Order requests fetched successfully.', $orderRequests);
    }

    public function fetchOrderDetails(Request $request)
    {
        // Validate the request to ensure order_id is provided
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Retrieve the order details along with its items and user information
        $order = Order::with(['orderItems.product', 'user'])
            ->where('id', $request->order_id)
            ->first();

        if (!$order) {
            return jsonResponse(false, 'Order not found.', 404);
        }

        // Prepare order details including products and user information
        $orderDetails = [
            'order_id' => $order->id,
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'phone' => $order->user->phone,
            ],
            'address' => $order->address,
            'phone' => $order->phone,
            'payment_method' => $order->payment_method,
            'total_price' => $order->total_price,
            'order_status' => $order->order_status,
            'delivery_status' => $order->delivery_status,
            'created_at' => $order->created_at,
            'products' => $order->orderItems->map(function ($item) {
                return [
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }),
        ];

        return jsonResponseWithData(true, 'Order details fetched successfully.', $orderDetails);
    }

    public function fetchAssignedOrders(Request $request)
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();


        // Validate the request to ensure order_id is provided
        $validator = Validator::make($request->all(), [
            'status' => 'nullable|in:pending,delivered,cancelled'
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Initialize query to fetch orders assigned to the driver
        $query = Order::where('driver_id', $driverId);

        // Apply status filter if provided
        if ($request->has('status')) {
            $query->where('order_status', $request->status);
        }

        // Fetch orders with minimal details
        $orders = $query->get(['id', 'user_id', 'address', 'phone', 'payment_method', 'total_price', 'order_status', 'delivery_status', 'created_at']);

        return jsonResponseWithData(true, 'Assigned orders fetched successfully.', $orders);
    }

    public function respondToOrderRequest(Request $request)
    {
        // Get the authenticated driver ID
        $driverId = auth('driver')->id();


        // Validate the request to ensure order_id is provided
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:accept,cancel',
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the driver order request
        $driverOrderRequest = DriverOrderRequest::where('order_id', $request->order_id)
            ->where('driver_id', $driverId)
            ->where('status', 'pending')
            ->first();

        if (!$driverOrderRequest) {
            return jsonResponse(false, 'Order request not found or already processed.', 404);
        }

        // Fetch the associated order
        $order = Order::find($driverOrderRequest->order_id);

        if (!$order) {
            return jsonResponse(false, 'Order not found.', 404);
        }

        // Check if the order is already accepted
        if ($order->driver_id && $order->driver_id != $driverId) {
            return jsonResponse(false, 'Order has already been accepted by another driver.', 400);
        }

        // Handle the action
        if ($request->action === 'accept') {
            // Accept the order
            $driverOrderRequest->update(['status' => 'accepted', 'accepted_at' => now()]);
            $order->update([
                'driver_id' => $driverId,
                'order_status' => 'accepted',
            ]);

            // Mark other pending requests for this order as 'expired'
            DriverOrderRequest::where('order_id', $order->id)
                ->where('id', '!=', $driverOrderRequest->id)
                ->update(['status' => 'expired']);

            // Notify the user
            if ($order->user && $order->user->fcm_token) {
                $userToken = $order->user->fcm_token;
                $title = 'Order Accepted';
                $body = "Your order #{$order->id} has been accepted by a driver.";
                FCMService::sendNotification($userToken, $title, $body, 'orders');

                // Log the user notification in the database
                UserNotification::create([
                    'user_id' => $order->user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }

            return jsonResponse(true, 'Order request accepted successfully.');
        } elseif ($request->action === 'cancel') {
            // Cancel the order request
            $driverOrderRequest->update(['status' => 'cancelled']);

            // Notify the user
            if ($order->user && $order->user->fcm_token) {
                $userToken = $order->user->fcm_token;
                $title = 'Order Request Cancelled';
                $body = "The driver has cancelled the order request for order #{$order->id}.";
                FCMService::sendNotification($userToken, $title, $body, 'order');

                // Log the user notification in the database
                UserNotification::create([
                    'user_id' => $order->user->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }

            return jsonResponse(true, 'Order request cancelled successfully.');
        }
    }

    public function cancelOrder(Request $request)
    {
        // Validate the request to ensure order_id is provided
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Fetch the order
        $order = Order::find($request->order_id);

        if (!$order) {
            return jsonResponse(false, 'Order not found.', 404);
        }

        // Check if the order is assigned to the current driver
        if ($order->driver_id !== $driverId) {
            return jsonResponse(false, 'Unauthorized action.', 403);
        }

        // Update the order status to pending
        $order->update([
            'order_status' => 'pending',
            'driver_id' => null, // Remove the driver from the order
        ]);

        // Update driver order requests
        DriverOrderRequest::where('order_id', $order->id)
            ->where('driver_id', $driverId)
            ->update(['status' => 'declined']);

        // Set other requests to pending if they were expired
        DriverOrderRequest::where('order_id', $order->id)
            ->where('status', 'expired')
            ->update(['status' => 'pending']);

        // Notify all drivers about the new pending order request
        $driverOrderRequests = DriverOrderRequest::where('order_id', $order->id)
            ->where('status', 'pending')
            ->get();

        foreach ($driverOrderRequests as $driverOrderRequest) {
            $driver = $driverOrderRequest->driver;

            if ($driver && $driver->fcm_token) {
                $driverToken = $driver->fcm_token;
                $title = 'New Order Request';
                $body = "A new order #{$order->id} is available for you.";
                FCMService::sendNotification($driverToken, $title, $body, 'order');

                // Log the driver notification in the database
                DriverNotification::create([
                    'driver_id' => $driver->id,
                    'title' => $title,
                    'body' => $body,
                ]);
            }
        }

        // Notify the user about the cancellation
        $user = $order->user;

        if ($user && $user->fcm_token) {
            $userToken = $user->fcm_token;
            $title = 'Order Cancelled';
            $body = "Your order #{$order->id} has been cancelled by the driver. Please wait until other driver accept your order.";
            FCMService::sendNotification($userToken, $title, $body, 'orders');

            // Log the user notification in the database
            UserNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }

        return jsonResponse(true, 'Order cancelled successfully.');
    }

    public function markAsDelivered(Request $request)
    {
        // Validate the request to ensure order_id is provided
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',

        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Get the authenticated driver ID
        $driverId = auth('driver')->id();

        // Fetch the order
        $order = Order::find($request->order_id);

        if (!$order) {
            return jsonResponse(false, 'Order not found.', 404);
        }

        // Check if the order is assigned to the current driver
        if ($order->driver_id !== $driverId) {
            return jsonResponse(false, 'Unauthorized action.', 403);
        }

        // Update the order status to delivered
        $order->update(['delivery_status' => 'delivered']);

        // Notify the user
        $user = $order->user;

        if ($user && $user->fcm_token) {
            $userToken = $user->fcm_token;
            $title = 'Order Delivered';
            $body = "Your order #{$order->id} has been successfully delivered.";
            FCMService::sendNotification($userToken, $title, $body, 'order');

            // Log the user notification in the database
            UserNotification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);
        }

        return jsonResponse(true, 'Order delivered successfully.');
    }
}
