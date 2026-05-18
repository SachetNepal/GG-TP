<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_id' => ['required', 'string', 'max:20'],
            'category_id' => ['required', 'string', 'max:20'],
            'product_name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'product_in_stock' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }
}

