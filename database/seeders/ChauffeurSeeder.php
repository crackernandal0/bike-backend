<?php

namespace Database\Seeders;

use App\Models\Chauffeur\Chauffeur as ChauffeurChauffeur;
use App\Models\ChauffeurRating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChauffeurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed chauffeur data
        $chauffeur = ChauffeurChauffeur::create([
            'driver_id' => 2,
            'tagline' => 'Professional and Experienced Chauffeur',
            'description' => 'With over 10 years of driving experience, I provide safe, reliable, and punctual services.',
            'image' => 'https://thumbs.dreamstime.com/b/chauffeur-limousine-lincoln-driver-luxury-car-such-as-standing-dressed-black-suit-tuxedo-dress-shirt-tie-black-leather-94959818.jpg', // Example image file name
            'skills_certifications' => ['Defensive Driving', 'First Aid Certified'],
            'additional_services' => ['Airport Transfers', 'Wedding Chauffeur', 'City Tours'],
            'availability' => 'Available for full-time and part-time shifts',
        ]);

         // Seed chauffeur rating data
         ChauffeurRating::create([
            'chauffeur_id' => $chauffeur->id,
            'user_id' => 3,
            'rating' => 4,
            'review' => 'Great driving experience, very professional!',
        ]);

        ChauffeurRating::create([
            'chauffeur_id' => $chauffeur->id,
            'user_id' => 3,
            'rating' => 5,
            'review' => 'Excellent service, highly recommended!',
        ]);

        // Output a success message
        $this->command->info('Chauffeur data seeded successfully.');
    }
}
