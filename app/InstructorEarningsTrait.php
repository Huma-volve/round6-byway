<?php

namespace App;
use App\Models\Payout;
use App\Models\InstructorEarnings;

trait InstructorEarningsTrait
{
    protected function CalculateEarnings($profile)
    {
        $totalEarnings = InstructorEarnings::where('instructor_profile_id', $profile->id)->sum('course_earnings');
        $totalWithdrawn = Payout::where('instructor_profile_id', $profile->id)
            ->where('status', 'completed')
            ->sum('amount');

        $availableBalance = $totalEarnings - $totalWithdrawn;
        $minimumWithdrawal = 10;
        return [$availableBalance, $minimumWithdrawal];
    }
    
}
