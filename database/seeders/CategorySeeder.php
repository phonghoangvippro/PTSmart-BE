<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptop', 'slug' => 'laptop', 'icon' => 'laptop', 'sort_order' => 1, 'children' => [
                ['name' => 'Laptop Gaming', 'slug' => 'laptop-gaming', 'icon' => 'sports_esports'],
                ['name' => 'Laptop Văn Phòng', 'slug' => 'laptop-van-phong', 'icon' => 'work'],
                ['name' => 'Laptop Đồ Họa', 'slug' => 'laptop-do-hoa', 'icon' => 'palette'],
            ]],
            ['name' => 'Điện Thoại', 'slug' => 'dien-thoai', 'icon' => 'smartphone', 'sort_order' => 2, 'children' => [
                ['name' => 'iPhone', 'slug' => 'iphone', 'icon' => 'phone_iphone'],
                ['name' => 'Samsung', 'slug' => 'samsung-phone', 'icon' => 'smartphone'],
                ['name' => 'Xiaomi', 'slug' => 'xiaomi-phone', 'icon' => 'smartphone'],
            ]],
            ['name' => 'Máy Tính Bảng', 'slug' => 'may-tinh-bang', 'icon' => 'tablet', 'sort_order' => 3, 'children' => [
                ['name' => 'iPad', 'slug' => 'ipad', 'icon' => 'tablet_mac'],
                ['name' => 'Samsung Galaxy Tab', 'slug' => 'samsung-tab', 'icon' => 'tablet_android'],
            ]],
            ['name' => 'Phụ Kiện', 'slug' => 'phu-kien', 'icon' => 'headphones', 'sort_order' => 4, 'children' => [
                ['name' => 'Tai Nghe', 'slug' => 'tai-nghe', 'icon' => 'headset'],
                ['name' => 'Sạc & Cáp', 'slug' => 'sac-cap', 'icon' => 'cable'],
                ['name' => 'Ốp Lưng', 'slug' => 'op-lung', 'icon' => 'phone_android'],
                ['name' => 'Bàn Phím & Chuột', 'slug' => 'ban-phim-chuot', 'icon' => 'keyboard'],
            ]],
            ['name' => 'Màn Hình', 'slug' => 'man-hinh', 'icon' => 'monitor', 'sort_order' => 5],
            ['name' => 'PC & Máy Bộ', 'slug' => 'pc-may-bo', 'icon' => 'desktop_windows', 'sort_order' => 6],
            ['name' => 'Smartwatch', 'slug' => 'smartwatch', 'icon' => 'watch', 'sort_order' => 7],
        ];

        foreach ($categories as $catData) {
            $children = $catData['children'] ?? [];
            unset($catData['children']);

            $parent = Category::create($catData);

            foreach ($children as $index => $child) {
                Category::create(array_merge($child, [
                    'parent_id' => $parent->id,
                    'sort_order' => $index + 1,
                ]));
            }
        }
    }
}
