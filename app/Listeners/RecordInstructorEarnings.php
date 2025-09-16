<?php

namespace App\Listeners;

use App\Events\SaveOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\OrderPaid;
use App\Models\InstructorEarnings;

class RecordInstructorEarnings
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaveOrder $event): void
    {
        // dd('here');
        $order = $event->order->load('items.purchasable.instructor.instructorProfile');
        foreach ($order->items as $item) {
            if ($item->purchasable_type === 'Course') {
                // dd('here2');
                $instrucor_earnings = $item->unit_price_cents * 0.7 / 100;
                $site_earnings = $item->unit_price_cents * 0.3 / 100;
                $instructor_profile = $item->purchasable->instructor->instructorProfile ?? null;
                // dd($instructor_profile);
                if ($instructor_profile) {

                    InstructorEarnings::create([
                        'instructor_profile_id' => $instructor_profile->id,
                        'Course_earnings' => $instrucor_earnings,
                        'currency' => $order->currency,
                        'payment_id' => $event->payment_id,
                    ]);
                }
            }
        }
    }
}
