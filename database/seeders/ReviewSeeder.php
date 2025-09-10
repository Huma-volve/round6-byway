<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $enrollments = Enrollment::all();

        foreach ($enrollments as $enrollment) {
            if (rand(0, 1)) { // ~50% chance student leaves review
                Review::factory()->create([
                    'course_id' => $enrollment->course_id,
                    'user_id'   => $enrollment->student_id,
                ]);
            }
        }
    }
}
