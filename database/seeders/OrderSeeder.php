<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create addresses for customers
        foreach ($customers as $customer) {
            if ($customer->addresses()->count() === 0) {
                Address::create([
                    'user_id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone ?? '0901234567',
                    'address' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
                    'type' => 'home',
                    'is_default' => true,
                ]);
            }
        }

        $statuses = ['pending', 'confirmed', 'shipping', 'completed', 'canceled'];
        $methods = ['cod', 'vnpay', 'momo'];

        // Create 20 sample orders spread over last 3 months
        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $address = $customer->addresses()->first();
            $status = $statuses[array_rand($statuses)];
            $daysAgo = rand(0, 90);

            // Pick 1-3 random products
            $orderProducts = $products->random(rand(1, 3));
            $subtotal = 0;
            $itemsData = [];

            foreach ($orderProducts as $product) {
                $qty = rand(1, 3);
                $price = $product->sale_price ?? $product->price;
                $subtotal += $price * $qty;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $qty,
                ];
            }

            $discount = rand(0, 1) ? rand(30000, 200000) : 0;
            $shippingFee = rand(0, 1) ? 30000 : 0;
            $total = max($subtotal - $discount + $shippingFee, 0);

            $order = Order::create([
                'order_code' => 'PTS-' . now()->subDays($daysAgo)->format('ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'user_id' => $customer->id,
                'address_id' => $address->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'status' => $status,
                'payment_method' => $methods[array_rand($methods)],
                'note' => $i % 3 === 0 ? 'Giao giờ hành chính' : null,
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays($daysAgo),
            ]);

            foreach ($itemsData as $item) {
                OrderItem::create(array_merge($item, ['order_id' => $order->id]));
            }

            // Update sold_count for completed orders
            if ($status === 'completed') {
                foreach ($itemsData as $item) {
                    Product::where('id', $item['product_id'])
                        ->increment('sold_count', $item['quantity']);
                }
            }
        }
    }
}
