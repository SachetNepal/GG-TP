<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\AssignDiscountRequest;
use App\Services\Trader\DiscountService;
use Illuminate\Http\JsonResponse;

class DiscountController extends Controller
{
    public function __construct(protected DiscountService $service)
    {
    }

    public function assign(AssignDiscountRequest $request): JsonResponse
    {
        $result = $this->service->assign($request->user(), $request->validated());

        return response()->json([
            'message' => 'Discount assigned to product',
            'discount' => $result['discount'],
            'link' => $result['link'],
        ], 201);
    }
}

