<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
                'instructor_id' => User::factory(), 
            'category_id' => Category::factory(), 
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(6),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'compare_price' => $this->faker->optional()->randomFloat(2, 50, 300),
            'image' => $this->faker->imageUrl(640, 480, 'course', true),
            'lessons_count' => $this->faker->numberBetween(5, 50),
            'level' => $this->faker->randomElement(['beginner', 'intermediate', 'advanced']),
            'duration_hours' => $this->faker->numberBetween(1, 40),
            'total_minutes' => $this->faker->numberBetween(30, 2400),
            'video_provider' => $this->faker->randomElement(['youtube', 'vimeo', 'local']),
            'status' => $this->faker->randomElement(['draft', 'published']),
        ];
    }
}
