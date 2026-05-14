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
            \Log::error('User creation failed: ' . $e->getMessage());
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
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Update user failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Update user failed. Please try again.',
            ], 500);
        }
    }
    
    public function show(User $user)
    {
        try {
            $this->authorize('view', $user);

            return response()->json([
                'status' => 1,
                'message' => 'User fetched successfully.',
                'data' => $user,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('View user failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch user data. Please try again.',
            ], 500);
        }
    }

    public function index()
    {
        try {
            $this->authorize('viewAny', User::class);

            $users = User::all();

            return response()->json([
                'status' => 1,
                'message' => 'Users fetched successfully.',
                'data' => $users,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Fetch users failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch user data. Please try again.',
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->authorize('delete', $user);
            
            if ($user->profile && Storage::disk('public')->exists($user->profile)) {
                Storage::disk('public')->delete($user->profile);
            }

            if ($user->icon && Storage::disk('public')->exists($user->icon)) {
                Storage::disk('public')->delete($user->icon);
            }

            $name = $user->firstname . ' ' . $user->lastname;
            $user->delete(); 

            return response()->json([
                'status' => 1,
                'message' => 'User ' . $name . ' deleted successfully.',
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Delete user failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete user. Please try again.',
            ], 500);
        }
    }
}
