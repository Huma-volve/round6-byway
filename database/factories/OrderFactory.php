<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(2000, 50000);
        $discount = $this->faker->numberBetween(0, 1000);
        $tax      = $this->faker->numberBetween(0, 5000);
        $total    = $subtotal - $discount + $tax;

        return [
            'user_id'   => User::factory(),
            'status'    => $this->faker->randomElement(['draft', 'pending', 'paid', 'failed', 'canceled', 'refunded']),
            'currency'  => 'USD',
            'subtotal_cents' => $subtotal,
            'discount_cents' => $discount,
            'tax_cents' => $tax,
            'total_cents' => $total,
            'placed_at'  => $this->faker->dateTimeThisYear,
        ];
    }
}
