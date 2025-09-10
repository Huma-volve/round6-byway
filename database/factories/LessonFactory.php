<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
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
            'title'     => $this->faker->sentence(4),
            'order'     => $this->faker->numberBetween(1, 20),
            'video_url' => $this->faker->url,
            'duration_minutes' => $this->faker->numberBetween(5, 60),
        ];
    }
}
