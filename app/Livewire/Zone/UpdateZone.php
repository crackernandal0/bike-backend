<?php

namespace App\Livewire\Zone;

use App\Models\Service\ServiceLocation;
use App\Models\Service\Zone;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class UpdateZone extends Component
{
    use WithPagination;

    public $zone_id;
    public $zone_name;
    public $service_location;
    public $active;
    public $latLng = []; // This will store the JSON lat_lng

    // Mount the existing zone for editing
    public function mount($zoneId)
    {
        // Fetch the zone with human-readable WKT format for coordinates
        $zone = Zone::select(
            'id',
            'name',
            'service_location_id',
            'active',
            DB::raw('ST_AsText(coordinates) as coordinates')
        )
            ->findOrFail($zoneId);

        $this->zone_id = $zone->id;
        $this->zone_name = $zone->name;
        $this->service_location = $zone->service_location_id;
        $this->active = $zone->active ? true : false;

        // Decode the coordinates from WKT format into an array
        $this->latLng = $this->convertPolygonToArray($zone->coordinates);
    }


    // Convert the existing polygon coordinates from database format to an array
    private function convertPolygonToArray($coordinates)
    {
        $polygons = [];
        if (!is_null($coordinates)) {
            $wkt = str_replace(['MULTIPOLYGON(((', ')))'], '', $coordinates);
            $polygonGroups = explode(')),((', $wkt);

            foreach ($polygonGroups as $polygon) {
                $points = explode(',', $polygon);
                $polygonArray = [];
                foreach ($points as $point) {
                    [$lng, $lat] = explode(' ', trim($point));
                    $polygonArray[] = ['lat' => (float) $lat, 'lng' => (float) $lng];
                }
                $polygons[] = $polygonArray;
            }
        }

        return $polygons;
    }

    // Listen for updated latLng from the frontend
    public function updatedLatLng($latLngJson = null)
    {
        // Store the passed JSON string in the $latLng variable
        $this->latLng = json_decode($latLngJson, true);
    }

    protected $rules = [
        'zone_name' => 'required|string|max:255',
        'service_location' => 'required|exists:service_locations,id',
        'latLng' => 'required|array|min:1', // At least one polygon should be present
        'latLng.*' => 'required|array|min:3', // Each polygon should have at least 3 points
    ];

    public function submit()
    {
        // Validate the incoming data
        $this->validate();

        // Ensure latLng contains valid polygons
        if (count($this->latLng) > 1) {
            $this->addError('latLng', 'Only one polygon is allowed for a zone.');
            return;
        }

        // Convert the latLng array into a MULTIPOLYGON string
        $multiPolygon = $this->convertToMultiPolygon($this->latLng);

        // Find the existing zone and update its data
        $zone = Zone::findOrFail($this->zone_id);
        $zone->service_location_id = $this->service_location;
        $zone->name = $this->zone_name;
        $zone->coordinates = DB::raw("ST_GeomFromText('$multiPolygon')");
        $zone->active = $this->active ? true : false;
        $zone->save();

        return redirect()->route('zones');
    }

    // Convert latLng array to MULTIPOLYGON string format
    private function convertToMultiPolygon($polygons)
    {
        $polygonStrings = [];

        // Loop through each polygon and format the coordinates
        foreach ($polygons as $polygon) {
            $coordinatePairs = [];

            foreach ($polygon as $point) {
                $coordinatePairs[] = "{$point['lng']} {$point['lat']}"; // Convert to 'lng lat' format
            }

            // Ensure the first and last points are the same (closing the polygon)
            if ($coordinatePairs[0] !== $coordinatePairs[count($coordinatePairs) - 1]) {
                $coordinatePairs[] = $coordinatePairs[0];
            }

            // Create a string for the polygon
            $polygonStrings[] = "((" . implode(", ", $coordinatePairs) . "))";
        }

        // Return the MULTIPOLYGON string
        return 'MULTIPOLYGON(' . implode(", ", $polygonStrings) . ')';
    }

    public function render()
    {
        $serviceLocations = ServiceLocation::where('active', 1)->latest()->get();
        return view('livewire.zone.update-zone', compact('serviceLocations'));
    }
}
