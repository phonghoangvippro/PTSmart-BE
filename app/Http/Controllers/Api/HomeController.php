<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = Banner::active()
            ->where('position', 'home_hero')
            ->orderBy('sort_order')
            ->get();

        $categories = Category::active()
            ->roots()
            ->with(['children' => fn($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $flashSale = FlashSale::active()
            ->with(['items.product:id,name,slug,thumbnail,price,rating_avg,sold_count'])
            ->first();

        $featuredProducts = Product::active()
            ->featured()
            ->with(['category:id,name', 'brand:id,name'])
            ->limit(12)
            ->get();

        $newProducts = Product::active()
            ->with(['category:id,name', 'brand:id,name'])
            ->orderByDesc('created_at')
            ->limit(12)
            ->get();

        $bestSellers = Product::active()
            ->with(['category:id,name', 'brand:id,name'])
            ->orderByDesc('sold_count')
            ->limit(12)
            ->get();

        return response()->json([
            'banners' => $banners,
            'categories' => $categories,
            'flash_sale' => $flashSale,
            'featured_products' => $featuredProducts,
            'new_products' => $newProducts,
            'best_sellers' => $bestSellers,
        ]);
    }
}
