<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_date' => ['required', 'string', 'in:Wednesday,Thursday,Friday'],
            'slot_time' => ['required', 'string', 'in:10 AM – 1 PM,1 PM – 4 PM,4 PM – 7 PM'],
            'payment_method' => ['required', 'string', 'max:60'],
        ];
    }
}

