<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        try {
            $validated = $request->validated();
            $validated["password"] = Hash::make($validated["password"]);

            $user = User::create($validated);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registered Successfully.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 201);
        } catch (\Throwable  $e) {
            Log::error('Register failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Register failed, please try again.'
            ], 500);
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials. Please try again.',
                ], 401);
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Logged in successfully.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed. Please try again.',
            ], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect.',
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Password change failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Password change failed. Please try again.',
            ], 500);
        }
    }
}
