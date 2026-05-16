<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Basket\AddBasketItemRequest;
use App\Services\Basket\BasketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class CartWebController extends Controller
{
    public function __construct(protected BasketService $basketService)
    {
    }

    public function index(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $basket = $this->basketService->getBasket(Auth::user());
            $summary = $this->basketService->summary($basket);
        } catch (Throwable $e) {
            return redirect()->route('login')->with('status', $e->getMessage());
        }

        return view('cart.index', [
            'cartItems' => $summary['items'],
            'total' => $summary['total'],
        ]);
    }

    public function addItem(AddBasketItemRequest $request): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $productId = (string) $request->validated('product_id');
            $qty = max(1, (int) $request->input('quantity', 1));
            for ($i = 0; $i < $qty; $i++) {
                $this->basketService->addItem(Auth::user(), $productId);
            }
        } catch (Throwable $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->route('cart')->with('status', 'Added to basket.');
    }
}
