<?php

namespace App\Services\Dashboard;




use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;



class StatsService
{
    /**
     * Main method to get all dashboard stats (cached).
     */
    public function getDashboardStats()
    {
        return Cache::remember('admin_dashboard_stats', 900, function () {
            return [
                'active_learners'      => $this->getActiveLearners(),
                'active_instructors'   => $this->getActiveInstructors(),
                'published_courses'    => $this->getPublishedCourses(),
                'total_revenue'        => $this->getTotalRevenue(),
                'monthly_revenue'      => $this->getMonthlyRevenueOverview(),
                'top_rated_courses'    => $this->getTopRatedCourses(),
                'recent_payouts'       => $this->getRecentPayoutRequests(),
            ];
        });
    }

    /**
     * Count of students with at least one enrollment.
     */
    protected function getActiveLearners()
    {
        return Enrollment::distinct('student_id')->count('student_id');
    }

    /**
     * Count of instructors with at least one published course.
     */
    protected function getActiveInstructors()
    {
        return Course::where('status', 'published')->distinct('instructor_id')->count('instructor_id');
    }

    /**
     * Count of published courses.
     */
    protected function getPublishedCourses()
    {
        return Course::where('status', 'published')->count();
    }

    /**
     * Sum of all succeeded payments (in dollars).
     */
    protected function getTotalRevenue()
    {
        return round(Payment::where('status', 'succeeded')
            ->where('type', 'payment')
            ->sum('amount_cents') / 100, 2);
    }

    /**
     * Monthly revenue for the current and previous year.
     * Returns: ['current' => [...], 'previous' => [...]]
     */
    protected function getMonthlyRevenueOverview()
    {
        $now = Carbon::now();
        $currentYear = $now->year;
        $previousYear = $now->copy()->subYear()->year;

        // Helper closure for monthly sums
        $monthly = function ($year) {
            return Payment::select(
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('SUM(amount_cents) as total')
            )
                ->whereYear('paid_at', $year)
                ->where('status', 'succeeded')
                ->where('type', 'payment')
                ->groupBy(DB::raw('MONTH(paid_at)'))
                ->pluck('total', 'month')
                ->map(fn($cents) => round($cents / 100, 2))
                ->all();
        };

        // Fill months with 0 if no data
        $fillMonths = function ($data) {
            $result = [];
            for ($m = 1; $m <= 12; $m++) {
                $result[$m] = $data[$m] ?? 0.0;
            }
            return $result;
        };

        return [
            'current'  => $fillMonths($monthly($currentYear)),
            'previous' => $fillMonths($monthly($previousYear)),
        ];
    }

    /**
     * Top 5 rated published courses with instructor and student count.
     */
    protected function getTopRatedCourses()
    {
        return Course::with(['instructor:id,first_name,last_name'])
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
            ->where('status', 'published')
            ->orderByDesc('reviews_avg_rating')
            ->orderByDesc('enrollments_count')
            ->take(5)
            ->get()
            ->map(function ($course) {
                return [
                    'id'             => $course->id,
                    'title'          => $course->title,
                    'instructor'     => $course->instructor ? $course->instructor->first_name . ' ' . $course->instructor->last_name : null,
                    'rating'         => round($course->reviews_avg_rating ?? 0, 1),
                    'students_count' => $course->enrollments_count,
                ];
            })
            ->values();
    }

    /**
     * Recent 5 payout requests (type = 'payout').
     */
    protected function getRecentPayoutRequests()
    {
        return Payment::where('type', 'payout')
            ->orderByDesc('paid_at')
            ->take(5)
            ->with(['order.user:id,first_name,last_name'])
            ->get()
            ->map(function ($payment) {
                $user = $payment->order->user ?? null;
                return [
                    'customer' => $user ? $user->first_name . ' ' . $user->last_name : null,
                    'date'     => $payment->paid_at->format('M d, Y h:i A'),
                    // ? $payment->paid_at->format('M d, Y h:i A')
                    // : null,
                    //'date'     => optional($payment->paid_at)->format('m/d/Y'),
                    'type'     => ucfirst($payment->method),
                    'amount'   => '$' . number_format($payment->amount_cents / 100, 2),
                ];
            })
            ->values();
    }
}
