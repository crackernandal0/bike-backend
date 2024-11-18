<?php

namespace App\Http\Controllers\Api\V1\User\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplainController extends Controller
{
    public function submitComplain(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5000', // Validate each image
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $user_id = auth()->id();

        // Initialize an array to hold the paths of the uploaded images
        $imagePaths = [];

        // If images are provided, handle the upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Use the helper function to upload the image and get the path
                $imagePath = uploadImage($image, 'complaints');
                $imagePaths[] = $imagePath;
            }
        }

        // Create the complaint record
        $complaint = Complaint::create([
            'user_id' => $user_id,
            'reason' => $request->reason,
            'details' => $request->details,
            'images' => json_encode($imagePaths), // Store images as JSON
        ]);

        return jsonResponse(true, __('Complaint submitted successfully'));
    }
}
