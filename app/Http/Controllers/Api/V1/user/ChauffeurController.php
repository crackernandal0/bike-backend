<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur\Chauffeur;

class ChauffeurController extends Controller
{
    public function getChauffeurs()
    {
        $chauffeurs = Chauffeur::select(['id', 'driver_id', 'image', 'tagline'])
            ->where('status', 'approved')
            ->whereHas('driver', function ($query) {
                $query->where('available_for_chauffeur', true);
            })
            ->with([
                'driver:id,experience_years,vehicle_subcategory_id,vehicle_type_id',
                'driver.vehicleSubcategory:id,name,image,short_amenties',
                'driver.vehicleType:id,name,icon'
            ])
            ->withCount('ratings') // Get the total count of ratings
            ->withAvg('ratings', 'rating') // Get the average of ratings
            ->get();

        return jsonResponseData(true, ['chauffeurs' => $chauffeurs]);
    }

    public function getChauffeurProfile($id)
    {
        $chauffeurs = Chauffeur::where('id', $id)->where('status', 'approved')
            ->whereHas('driver', function ($query) {
                $query->where('available_for_chauffeur', true);
            })
            ->with([
                'driver:id,experience_years,vehicle_subcategory_id,vehicle_type_id',
                'driver.vehicleSubcategory:id,name,image,short_amenties',
                'driver.vehicleType:id,name,icon',
                'ratings'
            ])
            ->withCount('ratings') // Get the total count of ratings
            ->withAvg('ratings', 'rating') // Get the average of ratings
            ->get();

        return jsonResponseData(true, ['chauffeurs' => $chauffeurs]);
    }
}
