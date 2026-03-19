<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\UserCoupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function myCoupons(Request $request): JsonResponse
    {
        $coupons = UserCoupon::where('user_id', $request->user()->id)
            ->with('coupon')
            ->where('is_used', false)
            ->get()
            ->map(function ($uc) {
                return $uc->coupon;
            })
            ->filter(fn($coupon) => $coupon && $coupon->isValid());

        return response()->json(['data' => $coupons->values()]);
    }

    public function apply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $validated['code'])->first();

        if (!$coupon) {
            return response()->json(['message' => 'Mã giảm giá không tồn tại'], 404);
        }

        if (!$coupon->isValid()) {
            return response()->json(['message' => 'Mã giảm giá đã hết hạn hoặc hết lượt sử dụng'], 422);
        }

        if ($validated['subtotal'] < $coupon->min_order) {
            return response()->json([
                'message' => 'Đơn hàng tối thiểu ' . number_format($coupon->min_order) . 'đ',
            ], 422);
        }

        // Check if used
        $used = UserCoupon::where('user_id', $request->user()->id)
            ->where('coupon_id', $coupon->id)
            ->where('is_used', true)
            ->exists();

        if ($used) {
            return response()->json(['message' => 'Bạn đã sử dụng mã giảm giá này'], 422);
        }

        $discount = $coupon->calculateDiscount($validated['subtotal']);

        return response()->json([
            'message' => 'Áp dụng mã giảm giá thành công',
            'data' => [
                'coupon' => $coupon,
                'discount' => $discount,
            ],
        ]);
    }
}
