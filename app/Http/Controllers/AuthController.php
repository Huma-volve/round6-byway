<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\VerificationMail;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request){

        $validate = Validator::make($request->all(),[
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'role' => 'required|in:student,instructor,admin',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',

        ]);

        if($validate->fails()){
            return response()->json([
                'status' => 'failed',
                'error' => $validate->errors()
            ],422);
        }




        $user = User::create([
        'first_name' => $request['first_name'],
        'last_name'  => $request['last_name'],
        'username'  => $request['username'],
        'role'       => $request['role'],
        'email'      => $request['email'],
        'password'   => $request['password'],

        ]);
        // if ($user->role === 'student') {
        //     $user->studentProfile()->create([]);
        // } elseif ($user->role === 'instructor') {
        //     $user->instructorProfile()->create([]);
        // }


        $code = rand(100000, 999999);

        VerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'email',
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        Mail::to($user->email)->send(new VerificationMail($code));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Please verify your email.',
            'token' => $token,
            'next_step' => 'verify_email',
            'expires_at' => Carbon::now()->addMinutes(10)->format('Y-m-d H:i:s')
        ],201);


    }
}
