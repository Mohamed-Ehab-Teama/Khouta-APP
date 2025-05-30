<?php

namespace App\Http\Controllers\API;

use App\Mail\OtpMail;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\RegisteredUserResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    // ====================     Register a new User:    ==================== //
    public function register(UserRegisterRequest $request)
    {
        $userData = $request->validated();

        $user = User::create($userData);

        // Generate Token
        $userData['token'] = $user->createToken('RegisteredUserToken')->plainTextToken;

        unset($userData['password']);

        if ($user) {
            return ApiResponse::sendResponse(201, "User Registered Successfully", $userData);
        }
        return ApiResponse::sendResponse(401, "Something Went Wrong", []);
    }



    // ====================     Login User:     ==================== //
    public function login(UserLoginRequest $request)
    {
        $loginData = $request->validated();

        if (Auth::attempt(['email' => $loginData['email'], 'password' => $loginData['password']])) {
            $user = Auth::user();
            $loginData['token'] = $user->createToken('LoginUserToken')->plainTextToken;
            unset($loginData['password']);

            return ApiResponse::sendResponse(200, "Loggined Successfully", $loginData);
        }
        return ApiResponse::sendResponse(401, "Incorrect Credentials", []);
    }



    // ====================     LogOut User:     ==================== //
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse(200, "Logout Successfully", []);
    }





    // ====================     Forget-Passowrd User:     ==================== //
    public function sendOTP(Request $request)
    {
        // Validate the Email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Get User
        $user = User::where('email', $request->email)->first();

        // Generate OTP
        $otp = random_int(1000, 9999);

        // Store OTP in DB
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15),
        ]);

        // Return OTP
        return response()->json([
            'status' => true,
            'message' => 'OTP code sent Successfully',
            'otp' => $otp,          
        ], 200);
    }



    // ====================     Verify_OTP:     ==================== //
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email'     => 'required|email',
            'otp'       => 'required|digits:4',
        ]);

        // Get User
        $user = User::where('email', $request->email)->first();

        // Check on OTP
        if (!$user ||  $user->otp_code !== $request->otp)
        {
            return ApiResponse::sendResponse(200, 'Invalid OTP', []);
        }

        // Check of OTP Expired
        if ( Carbon::now()->gt($user->otp_expires_at) )
        {
            return ApiResponse::sendResponse(200, "OTP has expired", []);
        }


        // Success OTP
        return response()->json([
            'status' => true,
            'message' => 'OTP Verified Successfully',
            'otp' => $request->otp,
        ], 200);

    }



    // ====================    Set New Password     ==================== //
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:4',
            'password'   => 'required|min:8|confirmed',
        ]);

        // Get User
        $user = User::where('email', $request->email)->first();

        // Check on OTP
        if (!$user ||  $user->otp_code !== $request->otp)
        {
            return ApiResponse::sendResponse(200, 'Invalid OTP', []);
        }

        // Check of OTP Expired
        if ( Carbon::now()->gt($user->otp_expires_at) )
        {
            return ApiResponse::sendResponse(200, "OTP has expired", []);
        }

        $user->update([
            'password'  => Hash::make($request->password),
            'otp_code'  => null,
            'otp_expires_at'  => null,
        ]);


        return response()->json([
            'status' => true, 
            'message' => 'Password reset successfully'
        ], 200);
    }
}
