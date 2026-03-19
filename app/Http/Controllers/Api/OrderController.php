<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cod,vnpay,momo',
            'coupon_code' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        try {
            $order = $this->orderService->createOrder(
                $request->user(),
                $validated
            );

            return response()->json([
                'message' => 'Đặt hàng thành công',
                'data' => $order->load(['items', 'payment', 'address']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()
            ->with(['items.product', 'payment'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $order = $request->user()->orders()
            ->with(['items.product', 'payment', 'address', 'coupon'])
            ->findOrFail($id);

        return response()->json(['data' => $order]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $order = $request->user()->orders()->findOrFail($id);

        try {
            $this->orderService->cancelOrder($order);

            return response()->json([
                'message' => 'Hủy đơn hàng thành công',
                'data' => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
