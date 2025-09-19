<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use App\Traits\AuthTrait;
use App\Http\Requests\HandleWithdrawalRequest;

class AdminPaymentsController extends Controller
{
    /**
     * Display a listing of transactions (payments + withdrawals).
     */
    public function index(Request $request)
    {
        // Optional filters (type/status) from query string
        $query = Transaction::with('user');

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by latest first
        $transactions = $query->orderBy('created_at', 'desc')
                              ->paginate(20);

        // Return paginated resource
        return TransactionResource::collection($transactions);
    }



    public function updateStatus(HandleWithdrawalRequest $request, Transaction $transaction)
    {
        //  dd($transaction);

        if ($transaction->type !== 'withdrawal') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only withdrawal transactions can be updated.',
            ], 400);
        }

        // public function updateStatus(HandleWithdrawalRequest $request, Transaction $transaction)
        // {
        //     //  dd($transaction);

        //     if ($transaction->type !== 'withdrawal') {
        //         return response()->json([
        //             'status' => 'error',
        //             'message' => 'Only withdrawal transactions can be updated.',
        //         ], 400);
        //     }

        // Prevent updating if already finalized
        if (in_array($transaction->status, ['completed', 'rejected'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'This withdrawal request has already been processed.',
            ], 400);
        }

        // Check balance if approving
        if ($request->status === 'completed') {
            if ($transaction->user->balance < $transaction->amount) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient balance for this withdrawal.',
                ], 400);
            }

            $transaction->user->decrement('balance', $transaction->amount);
        }

        $transaction->status = $request->status;
        $transaction->save();

        return (new TransactionResource($transaction))
            ->additional([
                'status'  => 'success',
                'message' => "Withdrawal request has been {$request->status}.",
            ]);
    }


    public function summary()
    {
        $platformEarnings = Transaction::where('type', 'payment')
                                       ->where('status', 'completed')
                                       ->sum('amount');

        $instructorEarnings = Transaction::where('type', 'withdrawal')
                                         ->where('status', 'completed')
                                         ->sum('amount');

        $totalWithdrawals = Transaction::where('type', 'withdrawal')->count();

        $studentPayments = Transaction::where('type', 'payment')->count();

        return response()->json([
            'platform_earnings'   => $platformEarnings,
            'instructor_earnings' => $instructorEarnings,
            'total_withdrawals'   => $totalWithdrawals,
            'student_payments'    => $studentPayments,
        ]);
    }

    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction->load(['user', 'paymentMethod']));
    }





}
