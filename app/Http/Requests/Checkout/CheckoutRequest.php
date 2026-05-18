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
            'location' => ['nullable', 'string', 'max:120'],
            'payment_method' => ['required', 'string', 'in:mock,paypal'],
        ];
    }
}

