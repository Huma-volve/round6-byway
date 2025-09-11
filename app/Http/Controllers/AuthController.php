<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Mail\VerificationMail;
use App\Mail\ResetPasswordMail;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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


        // $code = rand(100000, 999999);
        $code = "000000";

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

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'User not found.'
            ], 404);
        }

        $verification = VerificationCode::where('user_id', $user->id)
            ->where('code', $request->otp)
            ->where('type', 'email')
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid or expired code.'
            ], 400);
        }

        // Mark email as verified
        $user->update([
            'email_verified_at' => now(),
        ]);

        // Mark code as used
        $verification->update([
            'is_used' => true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Email verified successfully.',
            'user' => $user,
        ], 200);
    }

public function login(Request $request)
{
    $validate = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    if ($validate->fails()) {
        return response()->json([
            'status' => 'failed',
            'error' => $validate->errors()
        ], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Invalid email or password'
        ], 401);
    }

    if (is_null($user->email_verified_at)) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Please verify your email before logging in.'
        ], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'status' => 'success',
        'message' => 'Login successful.',
        'user' => $user,
        'token' => $token,
    ], 200);
    }

    public function logout(Request $request){
        // مسح التوكين الحالي
    $request->user()->tokens()
    ->where('id', $request->user()->currentAccessToken()->id)
    ->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.'
        ], 200);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        

       
        $token = Str::random(64);

        
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => hash('sha256', $token), // تخزين مشفر
            'created_at' => Carbon::now(),
        ]);

      
        $resetLink = url("/reset-password?token=$token&email=" . urlencode($request->email));
        Mail::to($request->email)->send(new ResetPasswordMail($resetLink));

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset link sent to your email.',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid token or email.'], 400);
        }

        $tokenValid = hash('sha256', $request->token) === $record->token;
        $tokenExpired = Carbon::parse($record->created_at)->addHour() < Carbon::now();

        if (!$tokenValid || $tokenExpired) {
            return response()->json(['message' => 'Token is invalid or expired.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully.'], 200);
    }


}
