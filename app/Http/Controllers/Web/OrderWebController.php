<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderWebController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $orders = Order::query()
            ->where('customer_id', Auth::id())
            ->orderByDesc('order_date')
            ->limit(50)
            ->get();

        return view('orders.index', ['orders' => $orders]);
    }
}
