<?php

namespace App\Http\Controllers;

use App\Models\Child;
use Spatie\FlareClient\Api;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AddChildRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateChildRequest;
use App\Http\Requests\UpdateProfileRequest;

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
        if ($updateProfileRequest->filled('name')) {
            $user->name = $updateProfileRequest->name;
        }
        // Update Email if Updated
        if ($updateProfileRequest->filled('email')) {
            $user->email = $updateProfileRequest->email;
        }
        // Update Password if Updated
        if ($updateProfileRequest->filled('password')) {
            $user->password = Hash::make($updateProfileRequest->password);
        }


        // Update User Data
        $updatedUser = $user->save();

        if ($updatedUser) {
            return ApiResponse::sendResponse(200, "Updated Successfully", $user);
        }
        return ApiResponse::sendResponse(400, "Something Went Wrong", []);
    }




    // List All Children
    public function listAllCgildren(Request $request)
    {
        $user_id = $request->user()->id;
        $children = Child::where('parent_id', $user_id)->get();
        // $children = $request->user()->children()->get();
        if (count($children) > 0) {
            return ApiResponse::sendResponse(200, "Children Retrieved Successfully", $children);
        }
        return ApiResponse::sendResponse(404, "No Children For This User", []);
    }



    // Add Child
    public function addChild(Request $request, AddChildRequest $addChildRequest)
    {
        $childData = $addChildRequest->validated();

        $imagePath = null;

        if ($addChildRequest->hasFile('image')) {
            $imagePath = $addChildRequest->file('image')->store('children', 'public');
            $childData['image'] = $imagePath;
        }

        // Assign Child To Parent User
        $childData['parent_id'] = $request->user()->id;

        // Create New Child
        $child = Child::create($childData);

        $childData['image'] = $childData['image'] ? asset('storage/' . $child->image) : null;

        return ApiResponse::sendResponse(200, "Child Added Successfully", $childData);
    }



    // Edit Child
    public function updateChild(Request $request, UpdateChildRequest $updateChildRequest, $id)
    {
        $childData = $updateChildRequest->validated();

        // Get Child
        $child = $request->user()->children()->find($id);

        // Check on Child
        if (!$child) {
            return ApiResponse::sendResponse(404, "Child Not Found Or You are Unauthenticated", []);
        }

        // Update Name if Updated
        if ($updateChildRequest->filled('name')) {
            $child->name = $updateChildRequest->name;
        }
        // Update Gender if Updated
        if ($updateChildRequest->filled('gender')) {
            $child->gender = $updateChildRequest->gender;
        }
        // Update Birth_Date if Updated
        if ($updateChildRequest->filled('birth_date')) {
            $child->birth_date = $updateChildRequest->birth_date;
        }
        // Update Name if Updated
        if ($request->hasFile('image')) {
            // Delete old image
            if ($child->image && Storage::disk('public')->exists($child->image)) {
                Storage::disk('public')->delete($child->image);
            }

            // Store New Image
            $child->image = $updateChildRequest->file('image')->store('children', 'public');
        }

        $child->save();

        return ApiResponse::sendResponse(200, "Child Updated Successfully", $child);
    }
}
