<?php

namespace App\Services\Review;

use App\Models\Review;
use App\Models\User;
use App\Support\OracleId;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function findForUserProduct(User $user, string $productId): ?Review
    {
        $customer = $user->customer;
        if (! $customer) {
            return null;
        }

        return Review::query()
            ->where('product_id', $productId)
            ->where('customer_id', $customer->customer_id)
            ->first();
    }

    public function create(User $user, array $data): Review
    {
        $customer = $user->customer;
        abort_if(! $customer, 403, 'Only customer accounts can leave reviews.');

        $productId = $data['product_id'];

        if ($this->findForUserProduct($user, $productId)) {
            throw ValidationException::withMessages([
                'review' => 'You have already reviewed this product.',
            ]);
        }

        return Review::create([
            'review_id' => OracleId::next('REVIEW', 'review_id', 'RE'),
            'rating' => $data['rating'],
            'review_body' => $data['review_body'],
            'review_date' => now(),
            'customer_id' => $customer->customer_id,
            'product_id' => $productId,
        ]);
    }
}

