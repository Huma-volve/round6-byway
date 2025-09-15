<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Database\Eloquent\Relations\Relation;

use App\Models\{Enrollment, Payment, Review, Course};
use App\Observers\{EnrollmentObserver, PaymentObserver, ReviewObserver, CourseObserver};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Enrollment::observe(EnrollmentObserver::class);
        Course::observe(CourseObserver::class);
        Payment::observe(PaymentObserver::class);
        Review::observe(ReviewObserver::class);
        Relation::morphMap([
            'Course' => \App\Models\Course::class,
        ]);
    }
}
