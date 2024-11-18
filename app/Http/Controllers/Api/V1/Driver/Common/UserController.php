<?php

namespace App\Http\Controllers\Api\V1\Driver\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\ContactQuery;
use App\Models\Common\DeleteAccountRequest;
use App\Models\Driver\DriverNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function updateLanguage(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:5', // Assuming short codes like 'en', 'fr', 'es', etc.
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->user();

        // Update the language
        $user->language = $request->language;
        $user->save();

        return jsonResponse(true, __('Language updated successfully'));
    }

    public function requestAccountDeletion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->user();

        // Use the helper function to get the profile picture path
        $profilePicturePath = $user->profile_picture ?? null;

        // Save the delete account request
        DeleteAccountRequest::create([
            'driver_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'profile_picture' => $profilePicturePath,
            'referral_code' => $user->referral_code,
            'reason' => $request->reason,
        ]);

        return jsonResponse(true, __('Your account deletion request has been submitted.'));
    }

    public function submitContactQuery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->id();

        // Save the contact query
        ContactQuery::create([
            'driver_id' => $user,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return jsonResponse(true, __('Your query has been submitted successfully.'));
    }

    public function updateProfile(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'country_code' => 'nullable|min:1|max:5',
            'phone_number' => 'nullable|numeric|digits_between:5,20|unique:users,phone_number,' . auth('driver')->id(),
            'language' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth('driver')->user();

        // Update the user details
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('country_code')) {
            $user->country_code = $request->country_code;
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        if ($request->has('language')) {
            $user->language = $request->language;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = uploadImage($request->file('profile_picture'), 'profile_pictures');
            $user->profile_picture = $profilePicturePath;
        }

        // Save the updated user data
        $user->save();

        return jsonResponseWithData(true, __('Profile updated successfully'), $user);
    }

    public function updateLocation(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric|between:-90,90', // Valid latitude range
            'longitude' => 'nullable|numeric|between:-180,180', // Valid longitude range
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $driver = auth('driver')->user();

        // Update the driver's location only if both latitude and longitude are present in the request
        if ($request->has('latitude') && $request->has('longitude')) {
            $driver->latitude = $request->latitude;
            $driver->longitude = $request->longitude;
            $driver->save();

            return jsonResponse(true, __('Location updated successfully'));
        }

        return jsonResponse(false, __('Both latitude and longitude must be provided to update location.'));
    }

    public function notifications()
    {
        $userId = auth('driver')->id();
        $notifications = DriverNotification::select('title','body','created_at')->where('driver_id', $userId)->latest()->get();

        return jsonResponseData(true, $notifications);
    }
}
