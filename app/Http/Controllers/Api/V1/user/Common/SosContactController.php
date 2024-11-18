<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\SosContact;
use App\Services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SosContactController extends Controller
{
    public function sosContacts()
    {
        $userId = auth()->id();
        $contacts = SosContact::where('user_id', $userId)->latest()->get();

        return jsonResponseData(true, $contacts);
    }

    public function addSosContact(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'contact_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth()->user();

        $contact = $user->sosContacts()->create([
            'contact_name' => $request->contact_name,
            'phone_number' => $request->phone_number,
        ]);

        return jsonResponseData(true, $contact);
    }

    public function updateSosContact(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|integer|exists:sos_contacts,id',
            'contact_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth()->user();

        // Find the contact by ID and ensure it belongs to the authenticated user
        $contact = $user->sosContacts()->find($request->contact_id);

        if (!$contact) {
            return jsonResponse(false, 'Contact not found.', 404);
        }

        // Update the contact with new data
        $contact->update($request->only('contact_name', 'phone_number'));

        return jsonResponseWithData(true, 'Contact updated successfully.', $contact);
    }

    public function deleteSosContact(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'contact_id' => 'required|integer|exists:sos_contacts,id',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user = auth()->user();

        // Find the contact by ID and ensure it belongs to the authenticated user
        $contact = $user->sosContacts()->find($request->contact_id);

        if (!$contact) {
            return jsonResponse(false, 'Contact not found.', 404);
        }

        // Delete the contact
        $contact->delete();

        return jsonResponse(true, 'Contact deleted successfully.');
    }



    public function shareLocation(Request $request)
    {

        // Validate the request input
        $validator = Validator::make($request->all(), [
            'sos_contact_id' => 'required|exists:sos_contacts,id',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        // Fetch the SosContact record based on id and user_id
        $sosContact = SosContact::where('id', $request->sos_contact_id)
            ->where('user_id', auth()->id())
            ->first();

        // If the contact is not found, return an error
        if (!$sosContact) {
            return response()->json(['error' => 'SOS contact not found.'], 404);
        }

        // Get the phone number and contact name
        $phoneNumber = $sosContact->phone_number;
        $contactName = $sosContact->contact_name;

        // Get the authenticated user's name
        $user = auth()->user();

        // Construct the Google Maps URL
        $googleMapsLink = "https://www.google.com/maps?q={$request->latitude},{$request->longitude}";

        // Construct the message
        $message = "{$user->name} shared their live location with you at: {$request->address}. 
                    Please click here to see the location on the map: {$googleMapsLink}";

        // Send the SMS using the helper function
        // sendSMS($phoneNumber, $message);

        if ($user && $user->fcm_token) {
            $userToken = $user->fcm_token;
            $title = "{$user->name} shared their live location";
            $body = $message;
            FCMService::sendNotification($userToken, $title, $body, 'notifications');
        }


        // Return a success response
        return response()->json(['message' => 'Location shared successfully.']);
    }
}
