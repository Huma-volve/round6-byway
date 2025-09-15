<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $payments = Payment::all();
        return response()->json($payments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest  $request)
    {
        //

        $payment = Payment::create($request->validated());
        return response()->json([
            'message' => 'Payment created successfully',
            'data' => $payment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
        return response()->json($payment);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $payment->update($request->validated());
        return response()->json([
            'message' => 'Payment updated successfully',
            'data' => $payment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(['message' => 'Payment deleted successfully']);
    }
}
