<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'string', 'max:20'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review_body' => ['required', 'string', 'max:1000'],
        ];
    }
}

