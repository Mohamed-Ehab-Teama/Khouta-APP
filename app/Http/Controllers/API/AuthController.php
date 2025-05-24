<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\RegisteredUserResource;

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

        if($user)
        {
            return ApiResponse::sendResponse(201, "User Registered Successfully", $userData);
        }
        return ApiResponse::sendResponse(401, "Something Went Wrong", []);
    }




    // ====================     Login User:     ==================== //
    public function login(UserLoginRequest $request)
    {
        $loginData = $request->validated();

        if (Auth::attempt(['email' => $loginData['email'], 'password' => $loginData['password']]))
        {
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


}
