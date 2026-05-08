<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\RegisterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
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
}
