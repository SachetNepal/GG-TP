<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\Review\ReviewService;

$user = User::query()->whereHas('customer')->first();
if (! $user) {
    echo "No customer user found\n";
    exit(1);
}

$service = app(ReviewService::class);
$productId = App\Models\Product::query()
    ->whereNotIn('product_id', function ($q) use ($user) {
        $q->select('product_id')
            ->from('REVIEW')
            ->where('customer_id', $user->customer->customer_id);
    })
    ->value('product_id');

if (! $productId) {
    echo "No product without review for user {$user->user_id}\n";
    exit(0);
}
echo "User {$user->user_id} product {$productId}\n";

try {
    $review = $service->create($user, [
        'product_id' => $productId,
        'rating' => 4,
        'review_body' => 'Test review from script',
    ]);
    echo "Created {$review->review_id} for product {$productId}\n";
} catch (Illuminate\Validation\ValidationException $e) {
    echo "Validation: ".json_encode($e->errors())."\n";
} catch (Throwable $e) {
    echo "Error: ".$e->getMessage()."\n";
}
