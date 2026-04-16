<?php

namespace App\Services\Review;

use App\Models\Review;
use App\Models\User;

class ReviewService
{
    public function create(User $user, array $data): Review
    {
        $customer = $user->customer;
        abort_if(!$customer, 403, 'Customer profile required');

        return Review::create([
            'rating' => $data['rating'],
            'review_body' => $data['review_body'],
            'review_date' => now(),
            'customer_id' => $customer->customer_id,
            'product_id' => $data['product_id'],
        ]);
    }
}

