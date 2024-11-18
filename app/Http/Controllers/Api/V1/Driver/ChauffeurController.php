<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur\Chauffeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChauffeurController extends Controller
{
    // Create or Update Chauffeur Profile
    public function createOrUpdateProfile(Request $request)
    {
        $driverId = auth('driver')->id(); // Get the authenticated driver ID

        $validator = Validator::make($request->all(), [
            'tagline' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string', // URL or path to image
            'skills_certifications' => 'nullable|array',
            'additional_services' => 'nullable|array',
            'availability' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return validationError($validator->errors());
        }

        $chauffeur = Chauffeur::updateOrCreate(
            ['driver_id' => $driverId],
            $request->all()
        );

        return jsonResponseWithData(true, 'Chauffeur profile saved successfully.', ['chauffeur' => $chauffeur]);
    }

    // Fetch Chauffeur Profile
    public function getProfile()
    {
        $driverId = auth('driver')->id();
        $chauffeur = Chauffeur::where('driver_id', $driverId)->with('ratings')->first();

        if (!$chauffeur) {
            return jsonResponseData(false, [], 'Chauffeur profile not found.');
        }

        return jsonResponseData(true, ['chauffeur' => $chauffeur]);
    }

    // Delete Chauffeur Profile
    public function deleteProfile()
    {
        $driverId = auth('driver')->id(); // Get the authenticated driver ID

        // Find the chauffeur profile by driver ID
        $chauffeur = Chauffeur::where('driver_id', $driverId)->first();

        if (!$chauffeur) {
            return jsonResponse(false, 'Chauffeur profile not found.');
        }

        // Delete the chauffeur profile
        $chauffeur->delete();

        return jsonResponse(true, 'Chauffeur profile deleted successfully.');
    }
}
