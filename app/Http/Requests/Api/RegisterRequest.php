<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'middlename' => 'string|max:255',
            'lastname' => 'required|string|max:255',
            'suffix' => 'nullable|string|in:jr,II,III,IV,V,VI',
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|string|email|unique:users,email|max:255', 
            'role' => 'required|string|in:member,admin,cashier,staff',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()   
                    ->symbols(),      
            ],
        ]; 
    }
}