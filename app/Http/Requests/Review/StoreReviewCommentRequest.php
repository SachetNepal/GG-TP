<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->customer !== null;
    }

    public function rules(): array
    {
        return [
            'comment_body' => ['required', 'string', 'max:500'],
        ];
    }
}
