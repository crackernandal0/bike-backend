<?php

use App\Models\Driver\Driver;
use App\Models\Service\Zone;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

// simple json response
if (!function_exists('jsonResponse')) {
    function jsonResponse($status, $message = '', $statusCode = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message
        ], $statusCode);
    }
}

// json response with data
if (!function_exists('jsonResponseWithData')) {
    function jsonResponseWithData($status, $message = '', $data = [], $statusCode = 200)
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
// json response with data without messsage
if (!function_exists('jsonResponseData')) {
    function jsonResponseData($status, $data = [], $statusCode = 200)
    {
        return response()->json([
            'status' => $status,
            'data' => $data
        ], $statusCode);
    }
}

// json response for validation errors
if (!function_exists('validationError')) {
    function validationError($errors = [], $message = 'Validation Error', $statusCode = 422)
    {
        // If errors are provided, get the first error message.
        if (!empty($errors) && is_object($errors) && $errors->first()) {
            $message = $errors->first(); // Set the message to the first error
        }

        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
}



// generates random otp
if (!function_exists('otp')) {
    function otp()
    {
        return mt_rand(1000, 9999);
    }
}


// includes routes files
if (!function_exists('include_route_files')) {
    function include_route_files($folder)
    {
        $path = base_path('routes' . DIRECTORY_SEPARATOR . $folder);
        $rdi = new RecursiveDirectoryIterator($path);
        $it = new RecursiveIteratorIterator($rdi);

        while ($it->valid()) {
            if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                require $it->key();
            }

            $it->next();
        }
    }
}



// ride system helpers

// check the point is in the zone area
if (!function_exists('isPointInZone')) {
    function isPointInZone($point, $coordinates)
    {
        $pickupPoint = "POINT($point[0] $point[1])";

        $zone = Zone::whereRaw("ST_Contains(ST_GeomFromText('{$coordinates}'), ST_GeomFromText('$pickupPoint'))")->first();

        if ($zone) {
            return true;
        } else {
            return false;
        }
    }
}



// calculate distance between two coordinates
if (!function_exists('calculateDistance')) {
    function calculateDistance($point1, $point2)
    {
        $earthRadius = 6371000; // Radius of the Earth in meters
        $latFrom = deg2rad($point1[0]);
        $lonFrom = deg2rad($point1[1]);
        $latTo = deg2rad($point2[0]);
        $lonTo = deg2rad($point2[1]);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius; // Returns distance in meters
    }
}


if (!function_exists('distance_between_two_coordinates')) {
    function distance_between_two_coordinates($point1, $point2, $unit = "K")
    {

        $lon1 = $point1[0];
        $lat1 = $point1[1];
        $lon2 = $point2[0];
        $lat2 = $point2[1];
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "M") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
}


// calculate time between two coordinates
if (!function_exists('calculateTime')) {
    function calculateTime($distance, $speed = 40)
    {
        $timeInHours = $distance / $speed;
        $timeInMinutes = $timeInHours * 60;

        return $timeInMinutes;
    }
}

// calculate time and distance between two coordinates
if (!function_exists('distanceAndTimeBetweenTwoCoordinates')) {
    function distanceAndTimeBetweenTwoCoordinates($point1, $point2)
    {

        $lon1 = $point1[0];
        $lat1 = $point1[1];
        $lon2 = $point2[0];
        $lat2 = $point2[1];
        $apiKey = 'AIzaSyC8DHtH6KQlFbii460Aegpt25GER2Bhshk';
        $origin = "$lat1,$lon1";
        $destination = "$lat2,$lon2";
        $mode = 'driving'; // can be 'driving', 'walking', 'bicycling', 'transit'


        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=$origin&destinations=$destination&mode=$mode&key=$apiKey";

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] === 'OK') {
            $distance = round($data['rows'][0]['elements'][0]['distance']['value'] / 1000, 1) ?? null; // Distance in kilometers, rounded to 2 decimal places
            $duration = (int) round($data['rows'][0]['elements'][0]['duration']['value'] / 60) ?? null;   // Time in minutes, rounded to the nearest whole number

            $data = [
                'distance' => $distance,
                'duration' => $duration,
            ];

            return $data;
        } else {
            $distance = round(distance_between_two_coordinates($point1, $point2), 1);
            $duration = (int) round(calculateTime($distance) / 60);   // Time in minutes, rounded to the nearest whole number
            $data = [
                'distance' => $distance,
                'duration' => $duration,
            ];
            return $data;
        }
    }
}




if (!function_exists('uploadImage')) {
    /**
     * Upload, resize, and compress an image
     */
    function uploadImage($image, $path, $resizeWidth = 800, $resizeHeight = 800, $quality = 70)
    {
        $extension = 'webp';

        $fileName = rand(111111, 999999) . '-' . time() . '.' . $extension;

        // Using Intervention Image for image processing
        $image = ImageManagerStatic::make($image->getRealPath());

        // Convert the image to WebP format and decrease the size
        $image->encode($extension, $quality)->save(public_path('media/uploads/' . $fileName));

        $uploadedImage = 'public/media/uploads/' . $fileName;


        // Return the full path of the uploaded image
        return $uploadedImage;
    }
}

if (!function_exists('uploadMedia')) {
    /**
     * Upload, and compress an image, delete existing
     */
    function uploadMedia($image, $directory, $quality = 80, $fileToDelete = null)
    {
        // Define the full directory path
        $directoryPath = public_path("media/{$directory}");

        // // Check if the directory exists, if not create it
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true, true);
        }

        $extension = 'webp';
        $fileName = rand(111111, 999999) . '-' . time() . '.' . $extension;
        $image = ImageManagerStatic::make($image->getRealPath());
        $image->encode($extension, $quality)->save("media/{$directory}/" . $fileName);


        if ($image && File::exists(public_path($fileToDelete))) {
            File::delete(public_path($fileToDelete));
        }
        return "media/{$directory}/" . $fileName;
    }
}
if (!function_exists('uploadDocument')) {
    function uploadDocument($file, $path)
    {
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $storagePath = "public/{$path}/{$filename}";
        Storage::put($storagePath, file_get_contents($file));
        return "storage/{$path}/{$filename}";
    }
}

if (!function_exists('findNearestDriver')) {
    function findNearestDriver($pickupLat, $pickupLong, $rideId = null, $vehicleTypeId = null, $vehicleSubcategoryId = null)
    {
        // Define the radius within which to search for drivers
        $radius = 100;

        // Initial query to find drivers based on location and basic status conditions
        $query = Driver::selectRaw("id, (
            6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) AS distance", [$pickupLat, $pickupLong, $pickupLat])
            ->where('role', '!=', 'instructor')
            ->where('status', 'approved')
            ->where('active', true)
            ->where('available', true)
            ->having("distance", "<=", $radius);

        // Exclude drivers who have declined this ride request
        if ($rideId) {
            $query->whereDoesntHave('driverRideRequests', function ($q) use ($rideId) {
                $q->where('ride_id', $rideId)
                    ->where('request_status', 'declined');
            });
        }

        // Filter based on vehicle subcategory if provided
        if ($vehicleSubcategoryId) {
            $query->where('vehicle_subcategory_id', $vehicleSubcategoryId);
        } elseif ($vehicleTypeId) {
            // If subcategory is not provided, filter by vehicle type if provided
            $query->where('vehicle_type_id', $vehicleTypeId);
        }

        // Order drivers by distance, getting the closest one
        $driver = $query->orderBy("distance", 'asc')->first();

        return $driver->id ?? null;
    }
}
