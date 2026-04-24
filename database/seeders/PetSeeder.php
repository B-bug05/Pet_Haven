<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pet;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $pets = [
            [
                'name' => 'Max',
                'type' => 'Dog',
                'breed' => 'Golden Retriever',
                'age' => '2 Years',
                'description' => 'A gentle and energetic companion looking for an active family.',
                'image' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?q=80&w=400',
                'status' => 'Ready for Adoption',
                'health_summary' => 'Fully vaccinated, neutered, and microchipped.'
            ],
            [
                'name' => 'Luna',
                'type' => 'Cat',
                'breed' => 'Domestic Shorthair',
                'age' => '4 Months',
                'description' => 'A sweet little kitten who loves to cuddle and play with yarn.',
                'image' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?q=80&w=400',
                'status' => 'Under Review',
                'health_summary' => 'De-wormed and first round of kitten shots completed.'
            ],
            [
                'name' => 'Oliver',
                'type' => 'Cat',
                'breed' => 'Maine Coon',
                'age' => '3 Years',
                'description' => 'A large, fluffy king of the house. Very calm and vocal.',
                'image' => 'https://images.unsplash.com/photo-1533738363-b7f9aef128ce?q=80&w=400',
                'status' => 'Ready for Adoption',
                'health_summary' => 'Heart-worm negative. Requires regular grooming.'
            ],
            [
                'name' => 'Bella',
                'type' => 'Dog',
                'breed' => 'French Bulldog',
                'age' => '1 Year',
                'description' => 'Small in size but big in personality. Loves short walks and naps.',
                'image' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?q=80&w=400',
                'status' => 'Ready for Adoption',
                'health_summary' => 'Up to date on all shots.'
            ],
            [
                'name' => 'Charlie',
                'type' => 'Dog',
                'breed' => 'Beagle',
                'age' => '5 Years',
                'description' => 'An expert sniffer who enjoys outdoor adventures.',
                'image' => 'https://images.unsplash.com/photo-1537151608828-ea2b11777ee8?q=80&w=400',
                'status' => 'Found a Home',
                'health_summary' => 'No known health issues.'
            ],
            [
                'name' => 'Simba',
                'type' => 'Cat',
                'breed' => 'Persian',
                'age' => '2 Years',
                'description' => 'A majestic cat who enjoys a quiet environment.',
                'image' => 'https://images.unsplash.com/photo-1513245543132-31f507417b26?q=80&w=400',
                'status' => 'Ready for Adoption',
                'health_summary' => 'Regular dental check-up recommended.'
            ],
        ];

        foreach ($pets as $pet) {
            Pet::create($pet);
        }
    }
}