<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Course, User};
use Carbon\Carbon;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'student_id' => User::factory()->state(['role' => 'student']),
            // FIX: Use Carbon to ensure correct datetime format
            'enrolled_at' => Carbon::now()->subDays(rand(1, 365)),
            'progress_percentage' => $this->faker->numberBetween(0, 100),
        ];
    }
}
