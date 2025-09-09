<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\InstructorProfile;

class InstructorProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructors = User::where('role', 'instructor')->get();

        foreach ($instructors as $instructor) {
            InstructorProfile::firstOrCreate(
                ['user_id' => $instructor->id], // unique column check
                [
                    'headline'   => fake()->sentence,
                    'about'      => fake()->paragraph,
                    'experience' => fake()->sentence(10),
                    'skills'     => implode(',', fake()->words(5)),
                    'social_links' => json_encode(['linkedin' => fake()->url]),
                    'image'      => fake()->imageUrl(300, 300, 'people'),
                ]
            );
        }
    }
}
