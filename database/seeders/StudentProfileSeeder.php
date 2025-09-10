<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentProfile;

class StudentProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            StudentProfile::firstOrCreate(
                ['user_id' => $student->id],
                [
                    'headline'   => fake()->sentence,
                    'about'      => fake()->paragraph,
                    'social_links' => json_encode(['facebook' => fake()->url]),
                    'image'      => fake()->imageUrl(300, 300, 'people'),
                ]
            );
        }
    }
}
