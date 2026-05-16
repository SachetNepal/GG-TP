<?php

namespace App\Http\Requests\Checkout;

use App\Services\Checkout\SlotCapacityService;
use Illuminate\Foundation\Http\FormRequest;

class PayPalStartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'slot_date' => ['required', 'string', 'in:'.implode(',', SlotCapacityService::VALID_DATES)],
            'slot_time' => ['required', 'string', 'in:'.implode(',', SlotCapacityService::VALID_TIMES)],
            'location' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function checkoutPayload(): array
    {
        return $this->only(['slot_date', 'slot_time', 'location']);
    }
}
