<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayoutMethodRequest;
use App\Http\Requests\UpdatePayoutMethodRequest;
use App\Models\PayoutMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutMethodsController extends Controller
{
    public function index()
    {
        $methods = Auth::user()->instructorProfile->payoutMethods;
        return response()->json($methods);
    }

    public function store(StorePayoutMethodRequest $request)
    {
        $profile = Auth::user()->instructorProfile;
        if (!$profile) {
            return response()->json(['error' => 'Instructor profile not found'], 404);
        }

        $method = $profile->payoutMethods()->create($request->validated());

        return response()->json(['status' => 'success', 'data' => $method], 201);
    }

    public function update(UpdatePayoutMethodRequest $request, $id)
    {
        $profile = Auth::user()->instructorProfile;
        $method = $profile->payoutMethods()->findOrFail($id);


        $method->update($request->validated());

        return response()->json($method);
    }

    public function destroy($id)
    {
        $profile = Auth::user()->instructorProfile;
        $method = $profile->payoutMethods()->findOrFail($id);
        $method->delete();

        return response()->json(['message' => 'Payout method deleted successfully']);
    }
}
