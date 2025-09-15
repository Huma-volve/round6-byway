<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    //

    public function userGrowth(Request $request)
    {
        // Monthly statistics of newly registered users

        $users = User::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'year' => Carbon::now()->year,
            'data' => $users
        ]);
    }



    //Report on course revenues


    public function courseRevenue(Request $request)
    {
        $courses = Course::withSum('payments', 'amount_cents')->get();
        return response()->json([
            'success' => true,
            'data' => $courses->map(function ($course) {
                return [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'total_revenue' => $course->payments_sum_amount_cents / 100, // convert cents to dollars
                ];
            }),
        ]);
    }


    // * Report on instructor performance


    public function instructorPerformance(Request $request)
    {
        $instructors = User::where('role', 'instructor')
            ->with('courses.payments')
            ->get(['id', 'first_name', 'last_name', 'email'])
            ->map(function ($instructor) {
                $totalRevenue = $instructor->courses->sum(function ($course) {
                    return $course->payments->sum('amount_cents');
                });

                return [
                    'id' => $instructor->id,
                    'name' => $instructor->name,
                    'email' => $instructor->email,
                    'courses_count' => $instructor->courses->count(),
                    'total_revenue' => $totalRevenue / 100, // تحويل من سنت لدولار
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $instructors
        ]);
    }


    public function exportPdf(Request $request)
{
    
    $query = Payment::query();
    

   if ($request->filled('status')) {
    $query->where('status', $request->status);
}

if ($request->filled('from_date') && $request->filled('to_date')) {
    $query->whereBetween('created_at', [$request->from_date, $request->to_date]);
}


    $payments = $query->get();
    // dd($payments);

    // توليد PDF من Blade view
    $pdf = Pdf::loadView('reports.payments', compact('payments'));

    // تنزيل PDF مباشرة
    return $pdf->download('payments_report.pdf');
}

}
