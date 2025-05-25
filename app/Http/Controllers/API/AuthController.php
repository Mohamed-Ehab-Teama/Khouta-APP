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



    // ====================     Login User:     ==================== //
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
            'email' => 'required|email|unique:password_reset_otps,email',
        ]);

        // Generate OTP
        $otp = rand(1000, 9999);

        // Store OTP in DB
        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'created_at' => Carbon::now(),
            ],
        );

        // Send OTP to your Email
        Mail::to($request->email)->send(new OtpMail($otp));


        return response()->json([
            'status' => true,
            'message' => 'OTP code sent Successfully',
            'otp' => $otp,          // For Test
        ], 200);
    }




    // ====================     Verify_OTP:     ==================== //
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:password_reset_otps,email',
            'otp'       => 'required|digits:4',
        ]);


        $record = DB::table('password_reset_otps')
            ->where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        
        if (!$record)
        {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP',
            ], 404);
        }


        // Check if OTP is Expired
        if ( Carbon::parse($record->created_at)->addMinutes(10)->isPast() )
        {
            return response()->json([
                'status' => false,
                'message' => 'Expired OTP',
            ], 400);
        }


        // Success OTP
        return response()->json([
            'status' => true,
            'message' => 'OTP Verified Successfully',
        ], 200);

    }
}
