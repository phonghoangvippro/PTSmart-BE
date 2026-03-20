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
        $query = Product::active()->with(['category:id,name,slug', 'brand:id,name']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category (hỗ trợ cả id và slug)
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($categorySlug = $request->input('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $categorySlug));
        }

        // Filter by brand (hỗ trợ cả id và name)
        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        }
        if ($brandName = $request->input('brand')) {
            $query->whereHas('brand', fn($q) => $q->where('name', $brandName));
        }

        // Filter by price range
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Filter by rating (vd: rating=4 → lấy SP có rating >= 4)
        if ($rating = $request->input('rating')) {
            $query->where('rating_avg', '>=', $rating);
        }

        // Filter chỉ SP đang giảm giá
        if ($request->input('on_sale')) {
            $query->whereNotNull('sale_price')
                  ->where('sale_price', '>', 0)
                  ->where('sale_price', '<', DB::raw('price'));
        }

        // Filter SP nổi bật
        if ($request->input('is_featured')) {
            $query->where('is_featured', true);
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
            case 'rating':
                $query->orderByDesc('rating_avg');
                break;
            case 'discount':
                $query->whereNotNull('sale_price')
                      ->where('sale_price', '>', 0)
                      ->orderByRaw('((price - sale_price) / price) DESC');
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

    /**
     * Gợi ý tìm kiếm (autocomplete) — response nhẹ, nhanh
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:100',
        ]);

        $keyword = $request->input('q');

        $products = Product::active()
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                      ->orWhere('slug', 'like', "%{$keyword}%");
            })
            ->select('id', 'name', 'slug', 'thumbnail', 'price', 'sale_price', 'category_id')
            ->with('category:id,name,slug')
            ->orderByDesc('sold_count')
            ->limit(8)
            ->get();

        // Gợi ý danh mục liên quan
        $categories = \App\Models\Category::where('name', 'like', "%{$keyword}%")
            ->select('id', 'name', 'slug')
            ->limit(3)
            ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
