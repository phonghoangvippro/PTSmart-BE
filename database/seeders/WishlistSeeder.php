<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $productIds = Product::where('status', 1)->pluck('id')->toArray();

        foreach ($customers as $customer) {
            // Mỗi customer yêu thích 3-6 sản phẩm ngẫu nhiên
            $randomProducts = collect($productIds)->shuffle()->take(rand(3, 6));

            foreach ($randomProducts as $productId) {
                Wishlist::create([
                    'user_id' => $customer->id,
                    'product_id' => $productId,
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }
}
