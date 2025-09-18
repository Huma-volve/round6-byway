<?php

namespace App\Http\Controllers;

use App\Models\InstructorEarnings;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class InstructorRevenueController extends Controller
{
    public function index(Request $request)
    {
        $profile = Auth::user()->instructorProfile;
        $total_profits = InstructorEarnings::where('instructor_profile_id', $profile->id)->sum('Course_earnings');
        $total_withDrawn = Payout::where('instructor_profile_id', $profile->id)
            ->where('status', 'completed')->sum('amount');
        $available_balance = $total_profits - $total_withDrawn;
        $last_transaction = optional(
            Payout::where('instructor_profile_id', $profile->id)->latest()->first()
        )->amount ?? 0;
        $monthly_revenue = InstructorEarnings::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(Course_earnings) as total')
        )->where('instructor_profile_id', $profile->id)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => Carbon::create($item->year, $item->month)->format('F Y'),
                    'total' => $item->total,
                ];
            });
        return response()->json([
            'total_profits' => $total_profits,
            'available_balance' => $available_balance,
            'last_transaction' => $last_transaction,
            'monthly_revenue' => $monthly_revenue,
        ]);
    }
}
