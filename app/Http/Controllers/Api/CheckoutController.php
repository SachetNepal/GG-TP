<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    public function __construct(protected CheckoutService $checkoutService)
    {
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $result = $this->checkoutService->checkout($request->user(), $request->validated());

        return response()->json([
            'message' => 'Order placed successfully',
            'data' => $result,
        ], 201);
    }
}

