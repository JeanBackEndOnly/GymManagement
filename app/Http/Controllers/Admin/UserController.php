<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserCreateRequest;

class UserController extends Controller
{
    public function create(UserCreateRequest $request){
        try {
            
        } catch (\Throwable $e) {
            \Log::error('An error occured: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Creating user failed, please try again.'
            ]);
        }
    }
}
