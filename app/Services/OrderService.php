<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCoupon;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $cartItems = $cart->items()->with(['product', 'variant'])->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            // Validate stock and calculate subtotal
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if (!$product || $product->status !== 1) {
                    throw new \Exception("Sản phẩm '{$product->name}' không còn bán");
                }

                $stock = $item->variant ? $item->variant->stock : $product->stock;
                if ($stock < $item->quantity) {
                    throw new \Exception("Sản phẩm '{$product->name}' không đủ hàng (còn {$stock})");
                }

                $price = $item->variant ? $item->variant->price : ($product->sale_price ?? $product->price);
                $subtotal += $price * $item->quantity;
            }

            // Apply coupon
            $discount = 0;
            $couponId = null;
            if (!empty($data['coupon_code'])) {
                $coupon = Coupon::where('code', $data['coupon_code'])->first();

                if (!$coupon || !$coupon->isValid()) {
                    throw new \Exception('Mã giảm giá không hợp lệ');
                }

                // Check if user already used this coupon
                $alreadyUsed = UserCoupon::where('user_id', $user->id)
                    ->where('coupon_id', $coupon->id)
                    ->where('is_used', true)
                    ->exists();

                if ($alreadyUsed) {
                    throw new \Exception('Bạn đã sử dụng mã giảm giá này');
                }

                $discount = $coupon->calculateDiscount($subtotal);
                $couponId = $coupon->id;
            }

            // Shipping fee (simplified)
            $shippingFee = $subtotal >= 2000000 ? 0 : 30000;

            // Create order
            $order = Order::create([
                'order_code' => Order::generateOrderCode(),
                'user_id' => $user->id,
                'address_id' => $data['address_id'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal - $discount + $shippingFee,
                'status' => $data['payment_method'] === 'cod' ? 'confirmed' : 'pending',
                'payment_method' => $data['payment_method'],
                'note' => $data['note'] ?? null,
                'coupon_id' => $couponId,
            ]);

            // Create order items and deduct stock
            foreach ($cartItems as $item) {
                $product = $item->product;
                $price = $item->variant ? $item->variant->price : ($product->sale_price ?? $product->price);

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $item->quantity,
                ]);

                // Deduct stock
                if ($item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                } else {
                    $product->decrement('stock', $item->quantity);
                }
                $product->increment('sold_count', $item->quantity);
            }

            // Create payment record
            $order->payment()->create([
                'method' => $data['payment_method'],
                'amount' => $order->total,
                'status' => $data['payment_method'] === 'cod' ? 'success' : 'pending',
            ]);

            // Mark coupon as used
            if ($couponId) {
                UserCoupon::updateOrCreate(
                    ['user_id' => $user->id, 'coupon_id' => $couponId],
                    ['is_used' => true, 'used_at' => now()]
                );
                Coupon::where('id', $couponId)->increment('used_count');
            }

            // Clear cart
            $cart->items()->delete();

            return $order;
        });
    }

    public function cancelOrder(Order $order): void
    {
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            throw new \Exception('Chỉ có thể hủy đơn hàng ở trạng thái chờ xác nhận hoặc đã xác nhận');
        }

        DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $item->variant?->increment('stock', $item->quantity);
                } else {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
                Product::where('id', $item->product_id)->decrement('sold_count', $item->quantity);
            }

            // Restore coupon
            if ($order->coupon_id) {
                UserCoupon::where('user_id', $order->user_id)
                    ->where('coupon_id', $order->coupon_id)
                    ->update(['is_used' => false, 'used_at' => null]);
                Coupon::where('id', $order->coupon_id)->decrement('used_count');
            }

            $order->update(['status' => 'canceled']);
            $order->payment?->update(['status' => 'failed']);
        });
    }
}
