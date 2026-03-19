<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi'],
            ['name' => 'Dell', 'slug' => 'dell'],
            ['name' => 'HP', 'slug' => 'hp'],
            ['name' => 'Lenovo', 'slug' => 'lenovo'],
            ['name' => 'ASUS', 'slug' => 'asus'],
            ['name' => 'Acer', 'slug' => 'acer'],
            ['name' => 'MSI', 'slug' => 'msi'],
            ['name' => 'Sony', 'slug' => 'sony'],
            ['name' => 'LG', 'slug' => 'lg'],
            ['name' => 'OPPO', 'slug' => 'oppo'],
            ['name' => 'Vivo', 'slug' => 'vivo'],
            ['name' => 'Logitech', 'slug' => 'logitech'],
            ['name' => 'JBL', 'slug' => 'jbl'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
