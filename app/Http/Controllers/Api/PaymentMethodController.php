<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $methods = $request->user()->paymentMethods()
            ->orderByDesc('is_default')
            ->get();

        return response()->json(['data' => $methods]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:card,ewallet',
            'provider' => 'required|string|max:50',
            'display_name' => 'required|string|max:100',
            'masked_number' => 'nullable|string|max:50',
            'card_holder' => 'nullable|string|max:255',
            'expiry' => 'nullable|string|max:10',
            'is_default' => 'sometimes|boolean',
            'metadata' => 'nullable|array',
        ]);

        $user = $request->user();

        if (!empty($validated['is_default'])) {
            $user->paymentMethods()->update(['is_default' => false]);
        }

        if ($user->paymentMethods()->count() === 0) {
            $validated['is_default'] = true;
        }

        $method = $user->paymentMethods()->create($validated);

        return response()->json([
            'message' => 'Thêm phương thức thanh toán thành công',
            'data' => $method,
        ], 201);
    }

    public function destroy(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $paymentMethod->delete();

        return response()->json(['message' => 'Xóa phương thức thanh toán thành công']);
    }

    public function setDefault(Request $request, PaymentMethod $paymentMethod): JsonResponse
    {
        if ($paymentMethod->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->user()->paymentMethods()->update(['is_default' => false]);
        $paymentMethod->update(['is_default' => true]);

        return response()->json([
            'message' => 'Đặt mặc định thành công',
            'data' => $paymentMethod,
        ]);
    }
}
