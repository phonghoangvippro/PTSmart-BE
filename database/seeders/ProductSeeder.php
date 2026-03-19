<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Laptops
            ['name' => 'MacBook Pro 14" M3 Pro', 'category' => 'laptop', 'brand' => 'Apple', 'price' => 49990000, 'sale_price' => 47990000, 'stock' => 25, 'is_featured' => true, 'description' => 'Laptop cao cấp với chip Apple M3 Pro, màn hình Liquid Retina XDR'],
            ['name' => 'Dell XPS 15 9530', 'category' => 'laptop', 'brand' => 'Dell', 'price' => 42990000, 'stock' => 15, 'is_featured' => true, 'description' => 'Laptop premium với Intel Core i7-13700H, RAM 16GB, SSD 512GB'],
            ['name' => 'ASUS ROG Strix G16 2024', 'category' => 'laptop-gaming', 'brand' => 'ASUS', 'price' => 35990000, 'sale_price' => 33990000, 'stock' => 20, 'is_featured' => true, 'description' => 'Laptop gaming mạnh mẽ với RTX 4060, màn hình 165Hz'],
            ['name' => 'Lenovo ThinkPad X1 Carbon Gen 11', 'category' => 'laptop-van-phong', 'brand' => 'Lenovo', 'price' => 38990000, 'stock' => 12, 'description' => 'Ultrabook doanh nhân cao cấp, nhẹ 1.12kg'],
            ['name' => 'MSI Katana 15 B13V', 'category' => 'laptop-gaming', 'brand' => 'MSI', 'price' => 25990000, 'sale_price' => 23990000, 'stock' => 30, 'description' => 'Gaming laptop tầm trung với RTX 4050'],
            ['name' => 'HP Pavilion 15 2024', 'category' => 'laptop-van-phong', 'brand' => 'HP', 'price' => 15990000, 'sale_price' => 14490000, 'stock' => 40, 'description' => 'Laptop văn phòng phổ thông, Intel Core i5'],
            ['name' => 'Acer Nitro 5 AN515', 'category' => 'laptop-gaming', 'brand' => 'Acer', 'price' => 22990000, 'stock' => 25, 'description' => 'Gaming laptop giá tốt với RTX 3050'],

            // Phones
            ['name' => 'iPhone 15 Pro Max 256GB', 'category' => 'iphone', 'brand' => 'Apple', 'price' => 34990000, 'sale_price' => 33490000, 'stock' => 50, 'is_featured' => true, 'description' => 'Flagship Apple với chip A17 Pro, camera 48MP'],
            ['name' => 'iPhone 15 128GB', 'category' => 'iphone', 'brand' => 'Apple', 'price' => 22990000, 'stock' => 60, 'description' => 'iPhone phổ thông với Dynamic Island, USB-C'],
            ['name' => 'Samsung Galaxy S24 Ultra', 'category' => 'samsung-phone', 'brand' => 'Samsung', 'price' => 33990000, 'sale_price' => 31990000, 'stock' => 35, 'is_featured' => true, 'description' => 'Flagship Samsung với S Pen, camera 200MP, Galaxy AI'],
            ['name' => 'Samsung Galaxy A55 5G', 'category' => 'samsung-phone', 'brand' => 'Samsung', 'price' => 9990000, 'sale_price' => 8990000, 'stock' => 80, 'description' => 'Smartphone tầm trung 5G, pin 5000mAh'],
            ['name' => 'Xiaomi 14 Ultra', 'category' => 'xiaomi-phone', 'brand' => 'Xiaomi', 'price' => 23990000, 'stock' => 20, 'is_featured' => true, 'description' => 'Camera Leica, Snapdragon 8 Gen 3'],
            ['name' => 'Xiaomi Redmi Note 13 Pro', 'category' => 'xiaomi-phone', 'brand' => 'Xiaomi', 'price' => 7990000, 'sale_price' => 6990000, 'stock' => 100, 'description' => 'Smartphone tầm trung, camera 200MP'],

            // Tablets
            ['name' => 'iPad Air M2 2024', 'category' => 'ipad', 'brand' => 'Apple', 'price' => 18990000, 'stock' => 30, 'is_featured' => true, 'description' => 'iPad Air mới với chip M2, màn hình 11 inch'],
            ['name' => 'iPad Pro 12.9" M4', 'category' => 'ipad', 'brand' => 'Apple', 'price' => 39990000, 'stock' => 15, 'description' => 'iPad Pro cao cấp nhất với chip M4'],
            ['name' => 'Samsung Galaxy Tab S9+', 'category' => 'samsung-tab', 'brand' => 'Samsung', 'price' => 24990000, 'sale_price' => 22990000, 'stock' => 20, 'description' => 'Tablet Android cao cấp với S Pen'],

            // Accessories
            ['name' => 'AirPods Pro 2 USB-C', 'category' => 'tai-nghe', 'brand' => 'Apple', 'price' => 6790000, 'sale_price' => 5990000, 'stock' => 100, 'is_featured' => true, 'description' => 'Tai nghe true wireless với ANC'],
            ['name' => 'Logitech MX Master 3S', 'category' => 'ban-phim-chuot', 'brand' => 'Logitech', 'price' => 2490000, 'stock' => 50, 'description' => 'Chuột không dây cao cấp'],
            ['name' => 'JBL Tune 770NC', 'category' => 'tai-nghe', 'brand' => 'JBL', 'price' => 2490000, 'sale_price' => 1990000, 'stock' => 60, 'description' => 'Tai nghe chụp tai chống ồn'],
        ];

        foreach ($products as $pData) {
            $category = Category::where('slug', $pData['category'])->first();
            $brand = Brand::where('name', $pData['brand'])->first();

            if (!$category || !$brand) continue;

            $product = Product::create([
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']),
                'description' => $pData['description'],
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'price' => $pData['price'],
                'sale_price' => $pData['sale_price'] ?? null,
                'stock' => $pData['stock'],
                'is_featured' => $pData['is_featured'] ?? false,
                'sold_count' => rand(10, 500),
                'rating_avg' => rand(35, 50) / 10,
                'review_count' => rand(5, 200),
                'status' => 1,
            ]);

            // Add variants for phones
            if (in_array($pData['category'], ['iphone', 'samsung-phone', 'xiaomi-phone'])) {
                $colors = ['Đen', 'Trắng', 'Xanh'];
                foreach ($colors as $color) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $product->name . ' - ' . $color,
                        'sku' => strtoupper(Str::slug($product->name . '-' . $color)),
                        'price' => $product->price,
                        'stock' => rand(5, 20),
                        'attributes' => ['color' => $color],
                    ]);
                }
            }
        }
    }
}
