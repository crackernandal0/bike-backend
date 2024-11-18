<?php

namespace App\Http\Controllers\Api\V1\User\Ride;

use App\Http\Controllers\Controller;
use App\Models\Service\ZoneTypePrice;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    public function getVehicleTypes()
    {
        // Cache key
        // $cacheKey = 'vehicle_types';

        // Attempt to retrieve data from cache
        // $vehicleTypes = Cache::rememberForever($cacheKey, function () {
        //     return VehicleType::select('id', 'name', 'icon')->get();
        // });

        $vehicleTypes =  VehicleType::select('id', 'name', 'icon')->get();


        // Return the data as JSON response
        return jsonResponseData(true, $vehicleTypes);
    }

    public function getSubCategories(Request $request)
    {
        // Fetch the ZoneTypePrice based on the request input and active status
        $subCategories = VehicleSubcategory::when($request->vehicle_type_id, function ($q, $request) {
            return $q->where('vehicle_type_id', $request->vehicle_type_id);
        })
            ->latest()
            ->get();

        // Return the formatted data
        return jsonResponseData(true, $subCategories);
    }


    public function fetchZoneSubCategories(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'vehicle_type_id' => 'required|integer',
            'zone_id' => 'required|integer',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the ZoneTypePrice based on the request input and active status
        $zoneTypePrices = ZoneTypePrice::with(['vehicleSubcategory'])
            ->where('vehicle_type_id', $request->vehicle_type_id)
            ->where('zone_id', $request->zone_id)
            ->where('active', 1)
            ->get();

        // Check if any data is found
        if ($zoneTypePrices->isEmpty()) {
            return jsonResponse(false, 'No active zone type prices found.', 404);
        }


        // Calculate distance using the helper function
        $pickupPoint = [$request->pickup_lng, $request->pickup_lat];
        $dropoffPoint = [$request->dropoff_lng, $request->dropoff_lat];
        $data = distanceAndTimeBetweenTwoCoordinates($pickupPoint, $dropoffPoint);
        $distance = $data['distance'];

        // Map the results to the required format
        $responseData = $zoneTypePrices->map(function ($zoneTypePrice) use ($distance) {
            // Calculate the total price based on the pricing model
            $basePrice = $zoneTypePrice->base_price;
            $distancePrice = $distance * $zoneTypePrice->price_per_distance;
            $totalPrice = $basePrice + $distancePrice;
            $totalPrice += $totalPrice * ($zoneTypePrice->admin_commision / 100);

            // Round the total price to the nearest whole number
            $totalPrice = round($totalPrice);

            return [
                'price' => $totalPrice,
                'subcategory_name' => $zoneTypePrice->vehicleSubcategory->name,
                'max_passengers' => $zoneTypePrice->vehicleSubcategory->passangers,
                'image' => $zoneTypePrice->vehicleSubcategory->image,
                'short_amenities' => $zoneTypePrice->vehicleSubcategory->short_amenties,
                'zone_type_id' => $zoneTypePrice->id,
                'vehicle_subcategory_id' => $zoneTypePrice->vehicleSubcategory->id,
            ];
        });

        // Return the formatted data
        return jsonResponseData(true, $responseData);
    }

    public function getVehicleSubcategoryDetails(Request $request)
    {
        // Validate the request input using your provided method
        $validator = Validator::make($request->all(), [
            'vehicle_subcategory_id' => 'required|integer|exists:vehicle_subcategories,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the VehicleSubcategory with related amenities
        $vehicleSubcategory = VehicleSubcategory::with('amenities')
            ->find($request->input('vehicle_subcategory_id'));


        // Prepare the response data
        $responseData = [
            'name' => $vehicleSubcategory->name,
            'short_amenities' => $vehicleSubcategory->short_amenties,
            'specifications' => $vehicleSubcategory->specifications,
            'amenities' => $vehicleSubcategory->amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'description' => $amenity->description,
                ];
            }),
        ];

        // Return the formatted data
        return jsonResponseData(true, $responseData);
    }
}
