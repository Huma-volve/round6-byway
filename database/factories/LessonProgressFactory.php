<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Enrollment, Lesson};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LessonProgress>
 */
class LessonProgressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'lesson_id'     => Lesson::factory(),
            'is_completed'  => $this->faker->boolean,
        ];
    }
}
