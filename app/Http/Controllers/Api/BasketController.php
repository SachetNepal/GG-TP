<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Basket\AddBasketItemRequest;
use App\Http\Requests\Basket\UpdateBasketItemRequest;
use App\Services\Basket\BasketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BasketController extends Controller
{
    public function __construct(protected BasketService $service)
    {
    }

    public function show(Request $request): JsonResponse
    {
        $basket = $this->service->getBasket($request->user());

        return response()->json($this->service->summary($basket));
    }

    public function addItem(AddBasketItemRequest $request): JsonResponse
    {
        $basket = $this->service->addItem($request->user(), (int) $request->validated()['product_id']);

        return response()->json([
            'message' => 'Item added to basket',
            'basket' => $this->service->summary($basket),
        ], 201);
    }

    public function updateItem(UpdateBasketItemRequest $request, int $basketItemId): JsonResponse
    {
        $basket = $this->service->updateItemQuantity($request->user(), $basketItemId, (int) $request->validated()['quantity']);

        return response()->json([
            'message' => 'Basket updated',
            'basket' => $this->service->summary($basket),
        ]);
    }

    public function removeItem(Request $request, int $basketItemId): JsonResponse
    {
        $basket = $this->service->removeItem($request->user(), $basketItemId);

        return response()->json([
            'message' => 'Basket item removed',
            'basket' => $this->service->summary($basket),
        ]);
    }
}

