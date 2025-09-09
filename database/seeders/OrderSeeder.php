<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Order, OrderItem, Payment, User, Course};

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // Each student makes 1–2 orders
            $orders = Order::factory(rand(1, 2))->create(['user_id' => $student->id]);

            foreach ($orders as $order) {
                // Add 1–3 courses to each order
                $courses = Course::inRandomOrder()->take(rand(1, 3))->get();

                foreach ($courses as $course) {
                    OrderItem::factory()->create([
                        'order_id'         => $order->id,
                        'purchasable_id'   => $course->id,
                        'purchasable_type' => 'Course',
                    ]);
                }

                // Add one payment per order
                Payment::factory()->create([
                    'order_id'     => $order->id,
                    'amount_cents' => $order->total_cents,
                    'currency'     => $order->currency,
                    'status'       => $order->status === 'paid' ? 'succeeded' : 'initiated',
                    'paid_at'      => $order->status === 'paid' ? now() : null,
                ]);
            }
        }
    }
}
