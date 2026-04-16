<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\Trader\TraderProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TraderProductController extends Controller
{
    public function __construct(protected TraderProductService $service)
    {
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->service->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Product created',
            'product' => $product,
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updated = $this->service->update($request->user(), $product, $request->validated());

        return response()->json([
            'message' => 'Product updated',
            'product' => $updated,
        ]);
    }

    public function toggleActive(Request $request, Product $product): JsonResponse
    {
        $request->validate(['is_active' => ['required', 'boolean']]);
        $updated = $this->service->setActive($request->user(), $product, (bool) $request->boolean('is_active'));

        return response()->json([
            'message' => 'Product state updated',
            'product' => $updated,
        ]);
    }
}

