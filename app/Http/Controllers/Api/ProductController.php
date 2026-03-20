<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::active()->with(['category', 'brand']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by brand
        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        // Filter by price range
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sort
        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'best_selling':
                $query->orderByDesc('sold_count');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $perPage = min($request->input('per_page', 20), 50);
        $products = $query->paginate($perPage);

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'brand', 'images', 'variants']);

        // Get related products
        $related = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(8)
            ->get();

        return response()->json([
            'data' => $product,
            'related' => $related,
        ]);
    }

    public function reviews(Product $product): JsonResponse
    {
        $reviews = $product->reviews()
            ->with('user:id,name,avatar')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($reviews);
    }

    public function featured(): JsonResponse
    {
        $products = Product::active()
            ->featured()
            ->with(['category', 'brand'])
            ->limit(12)
            ->get();

        return response()->json(['data' => $products]);
    }

    /**
     * Sản phẩm giảm giá sâu — dùng cho trang Khuyến mãi
     */
    public function discounted(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 10), 50);

        $products = Product::active()
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->where('sale_price', '<', DB::raw('price'))
            ->with(['category:id,name', 'brand:id,name'])
            ->orderByRaw('((price - sale_price) / price) DESC')
            ->paginate($perPage);

        // Thêm discount_percent vào mỗi sản phẩm
        $products->getCollection()->transform(function ($product) {
            $product->discount_percent = $product->price > 0
                ? round((($product->price - $product->sale_price) / $product->price) * 100)
                : 0;
            return $product;
        });

        return response()->json($products);
    }
}
