<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Catalog\CatalogService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CatalogService $catalogService)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json($this->catalogService->categories());
    }
}

