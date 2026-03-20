<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Banner;
use App\Models\Branch;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin PTSmart',
            'email' => 'admin@ptsmart.vn',
            'password' => '123123',
            'phone' => '0901234567',
            'role' => 'admin',
            'status' => 1,
        ]);

        // Customer users
        User::create([
            'name' => 'Lê Hoàng Phong',
            'email' => 'customer@ptsmart.vn',
            'password' => '123123',
            'phone' => '0987654321',
            'role' => 'customer',
            'status' => 1,
        ]);

        User::create([
            'name' => 'Phong Hoàng',
            'email' => 'phonghoang@ptsmart.vn',
            'password' => '123123',
            'phone' => '0912345678',
            'role' => 'customer',
            'status' => 1,
        ]);

        // Seed categories, brands, products
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            SettingSeeder::class,
        ]);

        // Banners
        Banner::create(['title' => 'iPhone 15 Pro Max', 'subtitle' => 'Giảm đến 3.000.000đ', 'image' => '/images/banner-iphone.jpg', 'position' => 'home_hero', 'sort_order' => 1]);
        Banner::create(['title' => 'Laptop Gaming Sale', 'subtitle' => 'Deal sốc cuối tuần', 'image' => '/images/banner-laptop.jpg', 'position' => 'home_hero', 'sort_order' => 2]);
        Banner::create(['title' => 'Flash Sale Giờ Vàng', 'subtitle' => 'Giảm đến 50%', 'image' => '/images/banner-flash.jpg', 'position' => 'flash_sale', 'sort_order' => 1]);

        // Branches
        Branch::create(['name' => 'PTSmart Hà Nội', 'address' => '123 Cầu Giấy, Hà Nội', 'phone' => '024 1234 5678', 'lat' => 21.0285, 'lng' => 105.8542]);
        Branch::create(['name' => 'PTSmart TP.HCM', 'address' => '456 Nguyễn Huệ, Quận 1, TP.HCM', 'phone' => '028 8765 4321', 'lat' => 10.7769, 'lng' => 106.7009]);
        Branch::create(['name' => 'PTSmart Đà Nẵng', 'address' => '789 Nguyễn Văn Linh, Đà Nẵng', 'phone' => '0236 9876 5432', 'lat' => 16.0544, 'lng' => 108.2022]);

        // Coupons
        Coupon::create([
            'code' => 'PTSMART50', 'title' => 'Giảm 50.000đ', 'description' => 'Giảm 50.000đ cho đơn từ 500.000đ',
            'type' => 'fixed', 'discount_value' => 50000, 'min_order' => 500000,
            'usage_limit' => 100, 'expired_at' => now()->addMonths(3), 'category' => 'shopping',
        ]);
        Coupon::create([
            'code' => 'FREESHIP', 'title' => 'Miễn phí vận chuyển', 'description' => 'Miễn phí ship cho đơn từ 300.000đ',
            'type' => 'shipping', 'discount_value' => 30000, 'min_order' => 300000,
            'usage_limit' => 200, 'expired_at' => now()->addMonths(6), 'category' => 'shipping',
        ]);
        Coupon::create([
            'code' => 'SALE10', 'title' => 'Giảm 10%', 'description' => 'Giảm 10% tối đa 200.000đ',
            'type' => 'percent', 'discount_value' => 10, 'min_order' => 1000000, 'max_discount' => 200000,
            'usage_limit' => 50, 'expired_at' => now()->addMonth(), 'category' => 'shopping',
        ]);

        // Flash Sale
        $flashSale = FlashSale::create([
            'title' => 'Flash Sale Giờ Vàng',
            'start_at' => now(),
            'end_at' => now()->addHours(6),
            'status' => 1,
        ]);

        $fsProducts = Product::inRandomOrder()->limit(5)->get();
        foreach ($fsProducts as $product) {
            FlashSaleItem::create([
                'flash_sale_id' => $flashSale->id,
                'product_id' => $product->id,
                'flash_price' => $product->price * 0.7,
                'quantity' => rand(10, 50),
                'sold' => rand(0, 10),
            ]);
        }
    }
}
