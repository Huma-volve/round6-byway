<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Order, Course};

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unit = $this->faker->numberBetween(1000, 5000);
        $qty  = $this->faker->numberBetween(1, 3);

        return [
            'order_id'        => Order::factory(),
            'purchasable_type' => 'Course',
            'purchasable_id'  => Course::factory(),
            'unit_price_cents' => $unit,
            'quantity'        => $qty,
            'total_cents'     => $unit * $qty,
        ];
    }
}
