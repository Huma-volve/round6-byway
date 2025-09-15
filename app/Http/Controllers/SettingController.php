<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlatformSettingRequest;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //

    public function getSettings()
    {
        $setting = PlatformSetting::first();
        return response()->json(
            [
                'commission_percentage' => $setting->commission_percentage,
                'withdrawal_limit' => $setting->withdrawal_limit,
                'notifications_enabled' => $setting->notifications_enabled,
            ]
        );
    }



    public function updateSettings(PlatformSettingRequest $request,$id)
{
    $setting = PlatformSetting::findOrfail($id);
    $setting->update($request->validated());

    return response()->json([
        'message' => 'Settings updated successfully',
        'data' => $setting
    ]);
}

}
