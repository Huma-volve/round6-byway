<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProfile>
 */
class StudentProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'headline'   => $this->faker->sentence,
            'about'      => $this->faker->paragraph,
            'social_links' => json_encode(['facebook' => $this->faker->url]),
            'image'      => $this->faker->imageUrl(300, 300, 'people'),
        ];
    }
}
