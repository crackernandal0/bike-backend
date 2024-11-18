<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\FavouriteLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavouriteLocationController extends Controller
{
    public function favoriteLocations()
    {
        $userId = auth()->id();
        $locations = FavouriteLocation::where('user_id', $userId)->latest()->get();

        return jsonResponseData(true, $locations);
    }
    public function favoriteLocationsSuggestions()
    {
        $userId = auth()->id();
        $locations = FavouriteLocation::select('name','latitude','longitude','address')->where('user_id', $userId)->take(3)->latest()->get();

        return jsonResponseData(true, $locations);
    }

    public function addFavoriteLocation(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth()->user();

        $location = $user->favoriteLocations()->create([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ]);

        return jsonResponseData(true, $location);
    }

    public function updateFavoriteLocation(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'favourite_location_id' => 'required|integer',
            'name' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth()->user();
        $location = FavouriteLocation::where('user_id', $user->id)->where('id', $request->favourite_location_id)->first();

        if (!$location) {
            return jsonResponseData(false, 'Location not found', 404);
        }

        $location->update([
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
        ]);

        return jsonResponseData(true, $location);
    }

    public function deleteFavoriteLocation(Request $request)
    {
         // Validate the request input
         $validator = Validator::make($request->all(), [
            'favourite_location_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }
        $user = auth()->user();
        $location = FavouriteLocation::where('user_id', $user->id)->where('id', $request->favourite_location_id)->first();

        if (!$location) {
            return jsonResponseData(false, 'Location not found', 404);
        }

        $location->delete();

        return jsonResponseData(true, 'Location deleted successfully');
    }
}
