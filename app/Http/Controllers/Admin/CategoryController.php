<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with('children')
            ->withCount('products')
            ->roots()
            ->orderBy('sort_order')
            ->get();

        return response()->json(['data' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|integer|in:0,1',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Tạo danh mục thành công',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|integer|in:0,1',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Cập nhật danh mục thành công',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->count() > 0) {
            return response()->json(['message' => 'Không thể xóa danh mục còn sản phẩm'], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Xóa danh mục thành công']);
    }
}
