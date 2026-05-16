<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:100', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone_num' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
        ];
    }
}

