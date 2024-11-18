<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver\InstructorRequest;
use App\Models\Ride\Ride;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function getAvailability()
    {
        $driver = auth('driver')->user(); // Assuming the authenticated user is related to the driver

        // Fetch the upcoming ride details for the driver
        $upcomingRide = Ride::where('driver_id', $driver->id)
            ->select('id', 'pickup', 'dropoff')
            ->where('ride_status', 'accepted') // Assuming 'accepted' indicates an upcoming ride
            ->orderByRaw("
            CASE 
                WHEN is_schedule_ride = 1 THEN CONCAT(COALESCE(scheduled_date, ''), ' ', COALESCE(scheduled_time, ''))
                ELSE created_at
            END DESC
        ")
            ->first();


        $upcomingRideDetails = null;
        if ($upcomingRide) {
            $upcomingRideDetails = [
                'ride_id' => $upcomingRide->id,
                'pickup' => $upcomingRide->pickup,
                'drop_off' => $upcomingRide->dropoff,
            ];
        }

        // Fetch today's earnings for the driver from the transactions table
        $wallet = Wallet::where('driver_id', $driver->id)->first();
        $todayEarnings = 0;
        if ($wallet) {
            $todayEarnings = Transaction::where('wallet_id', $wallet->id)
                ->where('type', 'credit')
                ->whereDate('created_at', today())
                ->sum('amount');
        }

        // Count the total rides completed today
        $completedRidesCount = Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->whereDate('created_at', today())
            ->count();

        // Sum the total distance of completed rides today
        $totalDistanceTraveled = Ride::where('driver_id', $driver->id)
            ->where('ride_status', 'completed')
            ->whereDate('created_at', today())
            ->sum('total_distance');

        // Prepare response data
        $responseData = [
            'available' => $driver->available,
            'upcoming_ride' => $upcomingRideDetails,
            'earnings_today' => round($todayEarnings, 2),
            'completed_rides_count' => $completedRidesCount,
            'total_distance_traveled' => round($totalDistanceTraveled, 2),
        ];

        return jsonResponseData(true, $responseData);
    }

    public function toggleAvailability()
    {
        $driver = auth('driver')->user(); // Assuming the authenticated user is related to the driver


        // Toggle the availability status
        $driver->available = !$driver->available;
        $driver->save();

        return jsonResponseData(true, ['available' => $driver->available]);
    }

    public function profile()
    {
        return jsonResponseData(true, auth('driver')->user() ?? null);
    }


    public function requestInstructor(Request $request)
    {
        $driver = auth('driver')->user();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'qualifications' => 'required|string',
            'qualifications_attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Validate each file
            'certifications.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Validate each file
            'training_specializations' => 'nullable|string',
            'additional_requests' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Handle qualifications attachments
        $qualifications_attachments = [];
        if ($request->file('qualifications_attachments')) {
            foreach ($request->file('qualifications_attachments') as $file) {
                $qualifications_attachments[] = $this->storeFile($file, 'drivers/qualifications');
            }
        }

        // Handle certifications
        $certifications = [];
        if ($request->file('certifications')) {
            foreach ($request->file('certifications') as $file) {
                $certifications[] = $this->storeFile($file, 'drivers/certifications');
            }
        }

        // Create a new instructor request
       InstructorRequest::create([
            'driver_id' => $driver->id,
            'qualifications' => $request->qualifications,
            'qualifications_attachments' => json_encode($qualifications_attachments), // Store as JSON
            'certifications' => json_encode($certifications), // Store as JSON
            'training_specializations' => $request->training_specializations,
            'additional_requests' => $request->additional_requests,
            'status' => 'pending', // Set the initial status to pending
        ]);

        return jsonResponse(true, 'Instructor request submitted successfully.');
    }

    protected function storeFile($file, $path)
    {
        if ($file->isValid()) {
            $extension = $file->getClientOriginalExtension();
            if (in_array($extension, ['jpeg', 'png', 'jpg', 'webp'])) {
                return uploadImage($file, $path);
            } else {
                return $this->uploadDocument($file, $path);
            }
        }
        return null;
    }
    protected function uploadDocument($file, $path)
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $storagePath = "public/{$path}/{$filename}";
        Storage::put($storagePath, file_get_contents($file));
        return "storage/{$path}/{$filename}";
    }
}
