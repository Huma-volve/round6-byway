<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Course;
use App\Models\OrderItem;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $courses = Course::inRandomOrder()->take(rand(1, 3))->get();

            foreach ($courses as $course) {
                OrderItem::factory()->create([
                    'order_id'        => $order->id,
                    'purchasable_id'  => $course->id,
                    'purchasable_type' => 'Course',
                ]);
            }
        }
    }
}
