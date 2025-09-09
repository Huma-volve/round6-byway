<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstructorProfile>
 */
class InstructorProfileFactory extends Factory
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
            'experience' => $this->faker->sentence(10),
            'skills'     => implode(',', $this->faker->words(5)),
            'social_links' => json_encode(['linkedin' => $this->faker->url]),
            'image'      => $this->faker->imageUrl(300, 300, 'people'),
        ];
    }
}
