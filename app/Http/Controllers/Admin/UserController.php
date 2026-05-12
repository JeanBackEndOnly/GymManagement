<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserCreateRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    
    public function store(UserCreateRequest $request)
    {
        try {
            $this->authorize('create', User::class);

            $validated = $request->validated();
            $validated["password"] = Hash::make($request->password);
            $validated["profile"] = $request->hasFile('profile')
                ? $request->file('profile')->store('profiles', 'public')
                : null;

            $validated["icon"] = $request->hasFile('icon')
                ? $request->file('icon')->store('icons', 'public')
                : null;

            $user = User::create($validated);

            return response()->json([
                'status' => 1,
                'message' => 'Member registered successfully.',
                'data' => $user,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Creating user failed. Please try again.',
            ], 500);
        }
    }

    public function update(UserUpdateRequest $request, User $user){
        try {
            $this->authorize('update', $user);
        } catch (\Throwable $e) {
            \Log::error('An error occured: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Update user failed, please try again.'
            ]);
        }
    }
}
