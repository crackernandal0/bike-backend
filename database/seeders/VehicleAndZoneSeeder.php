<?php

namespace Database\Seeders;

use App\Models\Service\Promo;
use App\Models\Service\ServiceLocation;
use App\Models\Service\Zone;
use App\Models\Service\ZoneType;
use App\Models\Service\ZoneTypePrice;
use App\Models\Vehicles\Amenity;
use App\Models\Vehicles\Brand;
use App\Models\Vehicles\VehicleAmenity;
use App\Models\Vehicles\VehicleModel;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleAndZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // // Create a VehicleType
            // $vehicleType = VehicleType::create([
            //     'name' => 'Car',
            //     'icon' => asset('media/vehicle_types/car.png'),
            // ]);

            // // Create a Brand
            // $brand = Brand::create([
            //     'name' => 'Toyota',
            //     'icon' => asset('media/brands/toyota.webp'),
            //     'status' => 1,
            // ]);

            // // Create a VehicleSubcategory
            // $vehicleSubcategory = VehicleSubcategory::create([
            //     'name' => 'Sedan',
            //     'vehicle_type_id'=> $vehicleType->id
            // ]);

            // // Create a VehicleModel
            // $vehicleModel = VehicleModel::create([
            //     'name' => 'Camry',
            //     'brand_id' => $brand->id,
            //     'vehicle_subcategory_id' => $vehicleSubcategory->id,
            //     'type_id' => $vehicleType->id,
            //     'color' => 'Red',
            //     'license_plate' => 'ABC-1234',
            //     'year' => 2022,
            //     'image' => asset('media/vehicles/camry.webp'),
            //     'vehicle_passing_till' => now()->addYear(),
            //     'special_services' => null,
            //     'status' => 1,
            // ]);

            // // Create an Amenity
            // $amenity = Amenity::create([
            //     'name' => 'Air Conditioning',
            //     'description' => 'Automatic Climate Control',
            // ]);

            // // Attach the Amenity to the VehicleModel
            // VehicleAmenity::create([
            //     'vehicle_model_id' => $vehicleModel->id,
            //     'amenity_id' => $amenity->id,
            // ]);

            // // Create a ServiceLocation
            // $serviceLocation = ServiceLocation::create([
            //     'name' => 'New York',
            //     'country_id' => 1, // Assuming country_id = 1 exists in the countries table
            //     'timezone' => 'America/New_York',
            //     'active' => 1,
            // ]);

     
            // $zone = new Zone();
            // $zone->service_location_id = $serviceLocation->id;
            // $zone->name = 'Test Zone';
            // $zone->coordinates = DB::raw("ST_GeomFromText('MULTIPOLYGON(((1 1, 2 2, 3 2, 3 4, 1 1)))')");
            // $zone->active = 1;
            // $zone->save();

            // // Create a ZoneType
            // ZoneTypePrice::create([
            //     'zone_id' => $zone->id,
            //     'vehicle_type_id' => $vehicleType->id,
            //     'vehicle_subcategory_id' => $vehicleSubcategory->id,
            //     'payment_type' => 'Cash',
            //     'base_price' => 50.00,
            //     'base_distance' => 5, // 5 km
            //     'price_per_distance' => 10.00, // per km
            //     'waiting_charge' => 2.00, // per minute
            //     'price_per_time' => 1.50, // per minute
            //     'cancellation_fee' => 20.00,
            //     'admin_commision' => 10.00,
            //     'service_tax' => 5.00,
            //     'gst_tax' => 18.00,
            //     'active' => 1,
            // ]);


            // // Create a Promo
            // Promo::create([
            //     'service_location_id' => $serviceLocation->id,
            //     'code' => 'Femi',
            //     'minimum_trip_amount' => 100.00,
            //     'maximum_discount_amount' => 50.00,
            //     'discount_percent' => 50,
            //     'max_usage' => 1000,
            //     'usage_count' => 0,
            //     'from' => now()->subDay(),
            //     'to' => now()->addMonth(),
            //     'active' => 1,
            // ]);

            // // Central Park Area Zone
            // $zone1 = new Zone();
            // $zone1->service_location_id = $serviceLocation->id;
            // $zone1->name = 'Central Park Area 2';
            // $zone1->coordinates = DB::raw("ST_GeomFromText('MULTIPOLYGON(((-73.9818934 40.7680852, -73.9733849 40.7643617, -73.9719166 40.7724573, -73.9818934 40.7680852)))')");
            // $zone1->active = 1;
            // $zone1->save();

            // // Times Square Area Zone
            // $zone2 = new Zone();
            // $zone2->service_location_id = $serviceLocation->id;
            // $zone2->name = 'Times Square Area 2';
            // $zone2->coordinates = DB::raw("ST_GeomFromText('MULTIPOLYGON(((-73.98640 40.75804, -73.98376 40.75418, -73.98082 40.75867, -73.98331 40.76143, -73.98640 40.75804)))')");
            // $zone2->active = 1;
            // $zone2->save();

            // // Add ZoneType and ZoneTypePrice for Zone 1
            // ZoneTypePrice::create([
            //     'zone_id' => $zone1->id,
            //     'vehicle_type_id' => $vehicleType->id,
            //     'vehicle_subcategory_id' => $vehicleSubcategory->id,
            //     'payment_type' => 'Cash',
            //     'base_price' => 50.00,
            //     'base_distance' => 5, // 5 km
            //     'price_per_distance' => 10.00, // per km
            //     'waiting_charge' => 2.00, // per minute
            //     'price_per_time' => 1.50, // per minute
            //     'cancellation_fee' => 20.00,
            //     'admin_commision' => 10.00,
            //     'service_tax' => 5.00,
            //     'gst_tax' => 18.00,
            //     'active' => 1,
            // ]);

           

            // // Add ZoneType and ZoneTypePrice for Zone 2
            // ZoneTypePrice::create([
            //     'zone_id' => $zone2->id,
            //     'vehicle_type_id' => $vehicleType->id,
            //     'vehicle_subcategory_id' => $vehicleSubcategory->id,
            //     'payment_type' => 'Cash',
            //     'base_price' => 50.00,
            //     'base_distance' => 5, // 5 km
            //     'price_per_distance' => 10.00, // per km
            //     'waiting_charge' => 2.00, // per minute
            //     'price_per_time' => 1.50, // per minute
            //     'cancellation_fee' => 20.00,
            //     'admin_commision' => 10.00,
            //     'service_tax' => 5.00,
            //     'gst_tax' => 18.00,
            //     'active' => 1,
            // ]);

            

             // Central Park Area Zone
             $zone1 = new Zone();
             $zone1->service_location_id = 5;
             $zone1->name = 'Vishakhapatnam Zone';
             $zone1->coordinates = DB::raw("ST_GeomFromText('MULTIPOLYGON(((82.919708340135 17.437785646146,82.99661263701 17.473157034961,83.014465420213 17.505902193498,83.085876553025 17.530784567495,83.164154140916 17.559591473217,83.220459072556 17.596248179421,83.243805019822 17.62111813866,83.247924892869 17.631588675041,83.264404385056 17.653836545358,83.297363369431 17.676081666685,83.297363369431 17.686549007007,83.313842861619 17.7035571344,83.345428554978 17.724488001918,83.352295010056 17.742800506597,83.359161465135 17.766342403756,83.385253994431 17.772881269813,83.430572597947 17.827798293825,83.186813442674 17.86440024437,82.96502694365 17.695707429611,82.884002773728 17.622958706671,82.82083138701 17.531316554686,82.919708340135 17.437785646146)))')");
             $zone1->active = 1;
             $zone1->save();

              // Add ZoneType and ZoneTypePrice for Zone 1
            ZoneTypePrice::create([
                'zone_id' => $zone1->id,
                'vehicle_type_id' => 5,
                'vehicle_subcategory_id' => null,
                'payment_type' => 'Cash',
                'base_price' => 30.00,
                'base_distance' => 5, // 5 km
                'price_per_distance' => 10.00, // per km
                'waiting_charge' => 2.00, // per minute
                'price_per_time' => 1.50, // per minute
                'cancellation_fee' => 20.00,
                'admin_commision' => 10,
                'service_tax' => 5,
                'gst_tax' => 18,
                'active' => 1,
            ]);
        });
    }
}
