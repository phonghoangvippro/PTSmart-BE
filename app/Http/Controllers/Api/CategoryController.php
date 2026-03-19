<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::active()
            ->roots()
            ->with(['children' => function ($q) {
                $q->active()->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function products(string $slug, Request $request): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Include child category IDs
        $categoryIds = collect([$category->id]);
        $childIds = $category->children()->pluck('id');
        $categoryIds = $categoryIds->merge($childIds);

        $query = Product::active()
            ->whereIn('category_id', $categoryIds)
            ->with(['brand']);

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

        return response()->json([
            'category' => $category,
            'products' => $products,
        ]);
    }
}
