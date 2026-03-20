<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'title' => 'Trả góp 0% lãi suất',
                'description' => 'Áp dụng cho iPhone 15 Series và các dòng Macbook mới nhất. Hỗ trợ trả góp qua thẻ tín dụng và công ty tài chính.',
                'image' => '/images/promotions/installment.jpg',
                'type' => 'installment',
                'start_at' => now()->subDays(5),
                'end_at' => now()->addMonths(2),
                'status' => 1,
                'product_categories' => ['iphone', 'laptop'],
            ],
            [
                'title' => 'Thu cũ đổi mới',
                'description' => 'Trợ giá lên đến 2 triệu đồng khi nâng cấp smartphone đời mới. Áp dụng cho tất cả các hãng.',
                'image' => '/images/promotions/trade-in.jpg',
                'type' => 'trade_in',
                'start_at' => now()->subDays(10),
                'end_at' => now()->addMonths(3),
                'status' => 1,
                'product_categories' => ['iphone', 'samsung-phone', 'xiaomi-phone'],
            ],
            [
                'title' => 'Bảo hành 2 năm',
                'description' => 'Yên tâm sử dụng sản phẩm với gói bảo hành mở rộng chính hãng. Bao gồm bảo hành phần cứng và phần mềm.',
                'image' => '/images/promotions/warranty.jpg',
                'type' => 'warranty',
                'start_at' => now()->subDays(30),
                'end_at' => now()->addMonths(6),
                'status' => 1,
                'product_categories' => ['laptop', 'laptop-gaming', 'laptop-van-phong', 'ipad'],
            ],
            [
                'title' => 'Combo phụ kiện giảm 30%',
                'description' => 'Mua kèm phụ kiện chính hãng giảm ngay 30%. Áp dụng cho tai nghe, ốp lưng, cường lực và sạc dự phòng.',
                'image' => '/images/promotions/combo-accessories.jpg',
                'type' => 'combo',
                'start_at' => now()->subDays(3),
                'end_at' => now()->addMonths(1),
                'status' => 1,
                'product_categories' => ['tai-nghe', 'ban-phim-chuot'],
            ],
            [
                'title' => 'Tặng Apple Care+',
                'description' => 'Tặng gói Apple Care+ 1 năm trị giá 3.990.000đ khi mua iPhone 15 Pro Max hoặc MacBook Pro M3.',
                'image' => '/images/promotions/apple-care.jpg',
                'type' => 'gift',
                'start_at' => now()->subDays(2),
                'end_at' => now()->addMonths(1),
                'status' => 1,
                'product_categories' => ['iphone', 'laptop'],
            ],
        ];

        foreach ($promotions as $promoData) {
            $categories = $promoData['product_categories'];
            unset($promoData['product_categories']);

            $promotion = Promotion::create($promoData);

            // Gắn sản phẩm thuộc các category liên quan
            $productIds = Product::whereHas('category', function ($q) use ($categories) {
                $q->whereIn('slug', $categories);
            })->inRandomOrder()->limit(rand(3, 6))->pluck('id');

            $promotion->products()->attach($productIds);
        }
    }
}
