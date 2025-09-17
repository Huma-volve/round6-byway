<?php

namespace App\Observers;

use App\Models\Review;
use Illuminate\Support\Facades\Cache;


class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        Cache::forget('admin_dashboard_stats');

            $instructorId = $review->course->instructor_id;
    Cache::forget("instructor_reviews_{$instructorId}");
    }



    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        Cache::forget('admin_dashboard_stats');

            $instructorId = $review->course->instructor_id;
    Cache::forget("instructor_reviews_{$instructorId}");
    }


    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        Cache::forget('admin_dashboard_stats');
    }
    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
