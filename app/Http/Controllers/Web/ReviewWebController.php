<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Services\Review\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewWebController extends Controller
{
    public function __construct(protected ReviewService $service)
    {
    }

    public function store(StoreReviewRequest $request, string $id): RedirectResponse
    {
        $this->service->create(Auth::user(), [
            'product_id' => $id,
            'rating' => (int) $request->validated('rating'),
            'review_body' => $request->validated('review_body'),
        ]);

        return redirect()
            ->route('products.show', $id)
            ->with('status', 'Thank you — your review has been posted.');
    }
}
