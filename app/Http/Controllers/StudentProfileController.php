<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{

    public function show(Request $request){
        $user = $request->user();

        if ($user->role !== 'student') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Only students can access this endpoint.'
            ], 403);
        }

        $profile = $user->studentProfile; // ممكن يكون null

        return response()->json([
            'status' => 'success',
            'profile' => [
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'email'        => $user->email,
                'role'         => $user->role,
                'headline'     => $profile->headline ?? null,
                'about'        => $profile->about ?? null,
                'social_links' => $profile->social_links ?? null,
                'image'        => $profile->image ? url($profile->image): null,
            ]
        ],200);
    }
    
    public function update(Request $request){
        $user = $request->user();

        if ($user->role !== 'student') {
            return response()->json([
                'status' => 'failed',
                'message' => 'Only students can update this profile.'
            ], 403);
        }

        $request->validate([
            'headline' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'social_links' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $profile = $user->studentProfile;

        if (!$profile) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Profile not found.'
            ], 404);
        }

        $image = $profile->image; // الصورة القديمة
        if ($request->hasFile('image')) {
            if ($image) {
                Storage::disk('public')->delete($image); // نحذف القديمة من public
            }
            $image = $request->file('image')->store('profiles', 'public'); // نخزن الجديدة
        }

        $profile->update([
            'headline' => $request->headline ?? $profile->headline,
            'about' => $request->about ?? $profile->about,
            'social_links' => $request->social_links ?? $profile->social_links,
            'image' => $image,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'profile' => $profile
        ], 200);


    }

}
