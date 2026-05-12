<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if(auth()->user()->isAdmin() || auth()->user()->isCashier()){
            return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname'  => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname'   => ['required', 'string', 'max:255'],
            'suffix'     => ['nullable', 'string', 'in:jr,II,III,IV,V,VI'],
            'contact'    => ['nullable', 'string', 'max:255'],
            'address'    => ['nullable', 'string'],
            'birthday'   => ['nullable', 'date'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'qr_code'    => ['nullable', 'string'],
            'sex'        => ['nullable', 'string', 'in:male,female'],
            'height'     => ['nullable', 'numeric', 'min:0'],
            'weight'     => ['nullable', 'numeric', 'min:0'],
            'email'      => ['required', 'string', 'email', 'max:255'],
            'profile'    => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'icon'       => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],
            'password'   => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->symbols(),
            ],
            'status'     => ['nullable', 'string', 'in:active,inactive'],
            'role'       => ['nullable', 'string', 'in:member,admin,cashier,staff'],
        ];
    }
}
