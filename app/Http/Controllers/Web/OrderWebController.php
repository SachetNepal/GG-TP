<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderWebController extends Controller
{
    public function __construct(protected OrderService $orderService)
    {
    }

    public function index(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $orders = Order::query()
            ->with('collectionSlot')
            ->where('customer_id', Auth::id())
            ->orderByDesc('order_date')
            ->limit(50)
            ->get();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(string $orderId): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $order = $this->orderService->findForCustomer(Auth::user(), $orderId);

        return view('orders.show', ['order' => $order]);
    }

    public function cancel(string $orderId): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $this->orderService->cancelForCustomer(Auth::user(), $orderId);

        return redirect()
            ->route('orders.show', $orderId)
            ->with('status', 'Order cancelled.');
    }
}
