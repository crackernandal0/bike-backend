<?php

namespace Database\Seeders;

use App\Models\Trip\TourPackage;
use App\Models\Trip\TripCategory;
use Illuminate\Database\Seeder;

class TourPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TripCategory::insert([
            [
                'name' => 'Adventure',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pilgrimage',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        TourPackage::insert([
            [
                'category_id' => 1,  // Assuming Adventure has id 1
                'package_name' => 'Goa Adventure Trip',
                'location' => 'Goa, India',
                'detailed_itinerary' => json_encode([
                    [
                        'day' => 'Day 1',
                        'title' => 'Arrival and Beach Exploration',
                        'activities' => [
                            'Pick up from the airport and check-in at the hotel.',
                            'Visit Calangute and Baga Beach.',
                            'Dinner at a beachside restaurant.',
                        ],
                    ],
                    [
                        'day' => 'Day 2',
                        'title' => 'Water Sports and Nightlife',
                        'activities' => [
                            'Scuba diving at Grande Island.',
                            'Party at Tito’s Nightclub.',
                        ],
                    ]
                ]),
                'tour_stops_places' => json_encode([
                    [
                        'stop' => 'Calangute Beach',
                        'description' => 'Known for its bustling atmosphere and water sports.',
                    ],
                    [
                        'stop' => 'Baga Beach',
                        'description' => 'Famous for its nightlife and shacks.',
                    ],
                ]),
                'tour_price' => 15000.00,
                'admin_commission' => 1000.00,
                'banner_image' => 'media/tris/goa_adventure.jpg',
                'duration' => '3 Days / 2 Nights',
                'members' => 'Total 4 :- Womens: 3, Children: 1',
                'longitude' => 73.856743,
                'latitude' => 15.299326,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,  // Assuming Pilgrimage has id 2
                'package_name' => 'Varanasi Pilgrimage',
                'location' => 'Varanasi, India',
                'detailed_itinerary' => json_encode([
                    [
                        'day' => 'Day 1',
                        'title' => 'Ganga Aarti and Temple Visits',
                        'activities' => [
                            'Evening Ganga Aarti at Dashashwamedh Ghat.',
                            'Visit to Kashi Vishwanath Temple.',
                        ],
                    ],
                    [
                        'day' => 'Day 2',
                        'title' => 'Boat Ride and Exploration',
                        'activities' => [
                            'Morning boat ride on the Ganges.',
                            'Exploring the narrow streets and local temples.',
                        ],
                    ]
                ]),
                'tour_stops_places' => json_encode([
                    [
                        'stop' => 'Dashashwamedh Ghat',
                        'description' => 'One of the most famous ghats in Varanasi, known for the evening Ganga Aarti.',
                    ],
                    [
                        'stop' => 'Kashi Vishwanath Temple',
                        'description' => 'A major Hindu temple dedicated to Lord Shiva.',
                    ],
                ]),
                'tour_price' => 10000.00,
                'admin_commission' => 2000.00,
                'banner_image' => 'media/tris/varanasi_pilgrimage.jpg',
                'duration' => '2 Days / 1 Night',
                'members' => 'Total 4 :- Womens: 3, Children: 1',
                'longitude' => 83.009859,
                'latitude' => 25.317644,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
