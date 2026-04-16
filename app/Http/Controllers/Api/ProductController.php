<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductFilterRequest;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(protected CatalogService $catalogService)
    {
    }

    public function index(ProductFilterRequest $request): JsonResponse
    {
        return response()->json($this->catalogService->products($request->validated()));
    }

    public function show(int $productId): JsonResponse
    {
        return response()->json($this->catalogService->productDetail($productId));
    }
}

