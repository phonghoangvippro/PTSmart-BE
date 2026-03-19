<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category:id,name', 'brand:id,name']);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->orderByDesc('created_at')->paginate(20);

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_featured' => 'sometimes|boolean',
            'status' => 'sometimes|integer|in:0,1',
            'thumbnail' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'variants' => 'nullable|array',
            'variants.*.name' => 'required|string',
            'variants.*.sku' => 'nullable|string',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.attributes' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('products', 'public');
            $validated['thumbnail'] = '/storage/' . $path;
        }

        $product = Product::create($validated);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_url' => '/storage/' . $path,
                    'sort_order' => $index,
                ]);
            }
        }

        // Handle variants
        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $variantData) {
                $product->variants()->create($variantData);
            }
        }

        return response()->json([
            'message' => 'Tạo sản phẩm thành công',
            'data' => $product->load(['images', 'variants', 'category', 'brand']),
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'sometimes|exists:brands,id',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'is_featured' => 'sometimes|boolean',
            'status' => 'sometimes|integer|in:0,1',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->thumbnail));
            }
            $path = $request->file('thumbnail')->store('products', 'public');
            $validated['thumbnail'] = '/storage/' . $path;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Cập nhật sản phẩm thành công',
            'data' => $product->load(['images', 'variants', 'category', 'brand']),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Xóa sản phẩm thành công']);
    }

    public function uploadImages(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:2048',
        ]);

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');
            $product->images()->create([
                'image_url' => '/storage/' . $path,
                'sort_order' => $product->images()->count() + $index,
            ]);
        }

        return response()->json([
            'message' => 'Upload ảnh thành công',
            'data' => $product->images,
        ]);
    }
}
