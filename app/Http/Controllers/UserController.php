<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allUsers = User::get();
        // dd($allUsers[0]);
        if ($allUsers) {
            return ApiResponse::sendResponse(200, "Users Retrieved Successfully", UsersResource::collection($allUsers));
        }
        return ApiResponse::sendResponse(400, "Something Went Wrong", []);
    }




    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if ($user)
        {
            return ApiResponse::sendResponse(200, "User Retrieved Successfully", new UsersResource($user));
        }
        return ApiResponse::sendResponse(404, "User Not Found", []);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $UpdatingUserData = $request->validated();
        $user = User::find($id);
        $update = $user->update( $UpdatingUserData );
        if ($update)
        {
            return ApiResponse::sendResponse(201, "User Updated Successfully", []);
        }
        return ApiResponse::sendResponse(400, "Somthing Went Wrong", []);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $del = $user->delete();
        if ($del)
        {
            return ApiResponse::sendResponse(201, "Deleted Successfully", []);
        }
        return ApiResponse::sendResponse(400, "Something Went Wrong", []);
    }
}
