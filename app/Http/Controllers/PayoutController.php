<?php

namespace App\Http\Controllers;

use App\Models\Payout;
use App\Models\InstructorEarnings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    use \App\InstructorEarningsTrait;
    public function withdrawPage()
    {
        $profile = Auth::user()->instructorProfile;
        list($availableBalance, $minimumWithdrawal) = $this->CalculateEarnings($profile);
        return response()->json([
            'available_balance' => $availableBalance,
            'minimum_withdrawal' => $minimumWithdrawal,
        ]);
    }


    public function withdraw(Request $request)
    {
        $profile = Auth::user()->instructorProfile;

        $validated = $request->validate([
            'amount' => 'required|integer|min:1',
        ]);
        $payoutMethod = $profile->payoutMethods()->where('is_default', true)->first();

        if (! $payoutMethod) {
            return response()->json(['error' => 'No default payout method set'], 422);
        }
        list($availableBalance, $minimumWithdrawal) = $this->CalculateEarnings($profile);




        if ($validated['amount'] < $minimumWithdrawal) {
            return response()->json(['error' => 'Amount is below minimum withdrawal'], 422);
        }

        if ($validated['amount'] > $availableBalance) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }
        


        $payout = Payout::create([
            'instructor_profile_id' => $profile->id,
            'payout_method_id' => $payoutMethod->id,
            'amount' => $validated['amount'],
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        return response()->json([
            'message' => 'Withdrawal request submitted successfully',
            'payout' => $payout,
        ]);
    }
}
