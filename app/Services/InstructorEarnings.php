<?php

namespace App\Services;

use App\Models\InstructorEarnings as instructor_earnings;
use App\Models\Order;
use Illuminate\Support\Facades\Log;


class InstructorEarnings
{
    public function storeEarnings($order_id, $payment_id)
    {
        $order = Order::with('items.purchasable.instructor.instructorProfile')->findorFail($order_id);
        foreach ($order->items as $item) {
            if ($item->purchasable_type == 'Course') {
                $course_price = $item->unit_price_cents / 100;
                $instructor_share = $course_price * 0.7;
                $site_share = $course_price * 0.3;
                $instructor_profile = $item->purchasable->instructor->instructorProfile ?? null;
                if ($instructor_profile) {
                    Instructor_earnings::create([
                        'instructor_profile_id' => $instructor_profile->id,
                        'Course_earnings' => $instructor_share,
                        'currency' => $order->currency,
                        'payment_id' => $payment_id,
                    ]);
                } else {
                    Log::warning("Instructor profile not found for course ID: {$item->purchasable->id}");
                }
            }
        }
    }
}
