<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    use ApiResponse;

    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return $this->sendResponse(new \App\Http\Resources\Auth\LoginResource($request->user()), 'Profile fetched successfully.');
    }

    /**
     * Setup user profile (for the first time)
     */
    public function setupProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'profile_setup' => true,
        ];

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Helper::deleteFile($user->avatar);
            }
            $path = Helper::uploadFile('avatars', $request->file('avatar'));
            $data['avatar'] = $path;
        }

        $user->update($data);

        return $this->sendResponse(new \App\Http\Resources\Auth\LoginResource($user), 'Profile setup successfully.');
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female,other'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048', 'dimensions:width=400,height=400'],
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'gender' => $request->gender,
        ];

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Helper::deleteFile($user->avatar);
            }
            $path = Helper::uploadFile('avatars', $request->file('avatar'));
            $data['avatar'] = $path;
        }

        $user->update($data);

        return $this->sendResponse(new \App\Http\Resources\Auth\LoginResource($user->refresh()), 'Profile updated successfully.');
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return $this->sendResponse([], 'Password updated successfully.');
    }
}
