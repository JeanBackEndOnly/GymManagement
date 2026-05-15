<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\AuthController;
// use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\MembershipController;


// Route::post('/register', [AuthController::class, 'register'])
//     ->middleware('throttle:5,1');

// Route::post('/login', [AuthController::class, 'login'])
//     ->middleware('throttle:10,1');

// Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
//     Route::get('/user', function (Request $request) {
//         return $request->user();
//     });
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/change-password', [AuthController::class, 'changePassword']);

//     Route::middleware('admin')
//         ->group(function () {
//             Route::apiResource('users', UserController::class);
//             Route::apiResource('memberships', MembershipController::class);
//         });
// });

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MembershipController;

// ── Public Routes ──────────────────────────────
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:5,1');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1');

// ── Authenticated Routes (any logged-in user) ──
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});

// ── Admin Routes (admin role only) ─────────────
Route::middleware(['auth:sanctum', 'admin', 'throttle:60,1'])->group(function () {
    // User Management
    Route::apiResource('users', UserController::class);
    Route::patch('/users/{user}/role', [UserController::class, 'updateRole']);

    // Membership Management
    Route::apiResource('memberships', MembershipController::class);
});