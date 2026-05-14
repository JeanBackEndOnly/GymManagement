<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserCreateRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

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
            Log::error('User creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Creating user failed. Please try again.',
            ], 500);
        }
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $this->authorize('update', $user);
            $validated = $request->validated();
            $validated["profile"] = $user->profile;
            $validated["icon"] = $user->icon;

            if ($request->hasFile('profile')) {
                if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                    Storage::disk('public')->delete($user->profile);
                }
                $validated["profile"] = $request->file('profile')->store('profiles', 'public');
            }

            if ($request->hasFile('icon')) {
                if ($user->icon && Storage::disk('public')->exists($user->icon)) {
                    Storage::disk('public')->delete($user->icon);
                }
                $validated["icon"] = $request->file('icon')->store('icons', 'public');
            }

            $user->update($validated);

            return response()->json([
                'status' => 1,
                'message' => $user->firstname . ' updated successfully.',
                'data' => $user->fresh(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Update user failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Update user failed. Please try again.',
            ], 500);
        }
    }
}
