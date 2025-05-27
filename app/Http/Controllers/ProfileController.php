<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    
    // Get Profile Data
    public function profile(Request $request)
    {
        return ApiResponse::sendResponse(200, 'Profile Data Retrieved Successfully', $request->user());
    }



    // Update Profile Data
    public function updateProfile(Request $request, UpdateProfileRequest $updateProfileRequest)
    {
        $userData = $updateProfileRequest->validated();

        $user = $request->user();

        // Update Name if Updated
        if ($updateProfileRequest->filled('name'))
        {
            $user->name = $updateProfileRequest->name;
        }
        // Update Email if Updated
        if ($updateProfileRequest->filled('email'))
        {
            $user->email = $updateProfileRequest->email;
        }
        // Update Password if Updated
        if ($updateProfileRequest->filled('password'))
        {
            $user->password = Hash::make($updateProfileRequest->password);
        }


        // Update User Data
        $updatedUser = $user->save();

        if ($updatedUser)
        {
            return ApiResponse::sendResponse(200, "Updated Successfully", $user);
        }
        return ApiResponse::sendResponse(400, "Something Went Wrong",[]);
        
    }


}
