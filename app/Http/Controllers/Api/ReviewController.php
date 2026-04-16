<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Services\Review\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $service)
    {
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $review = $this->service->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Review submitted',
            'review' => $review,
        ], 201);
    }
}

