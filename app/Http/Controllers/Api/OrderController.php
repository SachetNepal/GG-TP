<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['items.product', 'payment', 'collectionSlot'])
            ->where('user_id', $request->user()->user_id)
            ->orderByDesc('order_id')
            ->paginate(20);

        return response()->json($orders);
    }

    public function cancel(Request $request, int $orderId): JsonResponse
    {
        $order = Order::query()
            ->where('order_id', $orderId)
            ->where('user_id', $request->user()->user_id)
            ->firstOrFail();

        abort_if($order->status === 'completed', 422, 'Completed orders cannot be cancelled');

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'message' => 'Order cancelled',
            'order' => $order,
        ]);
    }
}

