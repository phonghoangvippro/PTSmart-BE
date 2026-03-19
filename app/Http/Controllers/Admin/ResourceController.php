<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Order;
use App\Models\User;
use App\Models\Coupon;
use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Article;
use App\Models\Banner;
use App\Models\Promotion;
use App\Models\Contact;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    // ============= BRANDS ==============
    public function brandIndex(): JsonResponse
    {
        return response()->json(['data' => Brand::withCount('products')->orderBy('name')->get()]);
    }

    public function brandStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:1024',
        ]);
        $validated['slug'] = Str::slug($validated['name']);
        if ($request->hasFile('logo')) {
            $validated['logo'] = '/storage/' . $request->file('logo')->store('brands', 'public');
        }
        return response()->json(['message' => 'Tạo thương hiệu thành công', 'data' => Brand::create($validated)], 201);
    }

    public function brandUpdate(Request $request, Brand $brand): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'logo' => 'nullable|image|max:1024',
        ]);
        if (isset($validated['name'])) $validated['slug'] = Str::slug($validated['name']);
        if ($request->hasFile('logo')) {
            if ($brand->logo) Storage::disk('public')->delete(str_replace('/storage/', '', $brand->logo));
            $validated['logo'] = '/storage/' . $request->file('logo')->store('brands', 'public');
        }
        $brand->update($validated);
        return response()->json(['message' => 'Cập nhật thương hiệu thành công', 'data' => $brand]);
    }

    public function brandDestroy(Brand $brand): JsonResponse
    {
        $brand->delete();
        return response()->json(['message' => 'Xóa thương hiệu thành công']);
    }

    // ============= ORDERS ==============
    public function orderIndex(Request $request): JsonResponse
    {
        $query = Order::with(['user:id,name,email', 'items']);
        if ($status = $request->input('status')) $query->where('status', $status);
        return response()->json($query->orderByDesc('created_at')->paginate(20));
    }

    public function orderShow(Order $order): JsonResponse
    {
        return response()->json(['data' => $order->load(['user', 'items.product', 'payment', 'address'])]);
    }

    public function orderUpdateStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate(['status' => 'required|in:pending,confirmed,shipping,completed,canceled']);

        if ($validated['status'] === 'canceled' && in_array($order->status, ['pending', 'confirmed'])) {
            app(\App\Services\OrderService::class)->cancelOrder($order);
        } else {
            $order->update(['status' => $validated['status']]);
        }

        return response()->json(['message' => 'Cập nhật trạng thái thành công', 'data' => $order->fresh()]);
    }

    // ============= USERS ==============
    public function userIndex(Request $request): JsonResponse
    {
        $query = User::query();
        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }
        return response()->json($query->orderByDesc('created_at')->paginate(20));
    }

    public function userUpdate(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|integer|in:0,1',
            'role' => 'sometimes|in:admin,customer',
        ]);
        $user->update($validated);
        return response()->json(['message' => 'Cập nhật user thành công', 'data' => $user]);
    }

    // ============= COUPONS ==============
    public function couponIndex(): JsonResponse
    {
        return response()->json(['data' => Coupon::orderByDesc('created_at')->get()]);
    }

    public function couponStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:fixed,percent,shipping',
            'discount_value' => 'required|numeric|min:0',
            'min_order' => 'sometimes|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expired_at' => 'required|date|after:now',
            'category' => 'sometimes|string|max:50',
            'status' => 'sometimes|integer|in:0,1',
        ]);
        return response()->json(['message' => 'Tạo coupon thành công', 'data' => Coupon::create($validated)], 201);
    }

    public function couponUpdate(Request $request, Coupon $coupon): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|max:50|unique:coupons,code,' . $coupon->id,
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:fixed,percent,shipping',
            'discount_value' => 'sometimes|numeric|min:0',
            'min_order' => 'sometimes|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expired_at' => 'sometimes|date',
            'category' => 'sometimes|string|max:50',
            'status' => 'sometimes|integer|in:0,1',
        ]);
        $coupon->update($validated);
        return response()->json(['message' => 'Cập nhật coupon thành công', 'data' => $coupon]);
    }

    public function couponDestroy(Coupon $coupon): JsonResponse
    {
        $coupon->delete();
        return response()->json(['message' => 'Xóa coupon thành công']);
    }

    // ============= FLASH SALES ==============
    public function flashSaleIndex(): JsonResponse
    {
        return response()->json(['data' => FlashSale::with('items.product:id,name,thumbnail,price')->orderByDesc('created_at')->get()]);
    }

    public function flashSaleStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'status' => 'sometimes|integer|in:0,1',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.flash_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        $flashSale = FlashSale::create($validated);
        foreach ($validated['items'] as $item) {
            $flashSale->items()->create($item);
        }
        return response()->json(['message' => 'Tạo flash sale thành công', 'data' => $flashSale->load('items.product')], 201);
    }

    public function flashSaleUpdate(Request $request, FlashSale $flashSale): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'start_at' => 'sometimes|date',
            'end_at' => 'sometimes|date',
            'status' => 'sometimes|integer|in:0,1',
        ]);
        $flashSale->update($validated);
        return response()->json(['message' => 'Cập nhật flash sale thành công', 'data' => $flashSale]);
    }

    public function flashSaleDestroy(FlashSale $flashSale): JsonResponse
    {
        $flashSale->delete();
        return response()->json(['message' => 'Xóa flash sale thành công']);
    }

    // ============= ARTICLES ==============
    public function articleIndex(Request $request): JsonResponse
    {
        $query = Article::with('author:id,name');
        if ($category = $request->input('category')) $query->where('category', $category);
        return response()->json($query->orderByDesc('created_at')->paginate(20));
    }

    public function articleStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'category' => 'sometimes|string|max:50',
            'read_time' => 'nullable|string|max:20',
            'is_featured' => 'sometimes|boolean',
            'status' => 'sometimes|integer|in:0,1',
            'published_at' => 'nullable|date',
        ]);
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['author_id'] = $request->user()->id;
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = '/storage/' . $request->file('thumbnail')->store('articles', 'public');
        }
        return response()->json(['message' => 'Tạo bài viết thành công', 'data' => Article::create($validated)], 201);
    }

    public function articleUpdate(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'category' => 'sometimes|string|max:50',
            'read_time' => 'nullable|string|max:20',
            'is_featured' => 'sometimes|boolean',
            'status' => 'sometimes|integer|in:0,1',
            'published_at' => 'nullable|date',
        ]);
        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) Storage::disk('public')->delete(str_replace('/storage/', '', $article->thumbnail));
            $validated['thumbnail'] = '/storage/' . $request->file('thumbnail')->store('articles', 'public');
        }
        $article->update($validated);
        return response()->json(['message' => 'Cập nhật bài viết thành công', 'data' => $article]);
    }

    public function articleDestroy(Article $article): JsonResponse
    {
        $article->delete();
        return response()->json(['message' => 'Xóa bài viết thành công']);
    }

    // ============= BANNERS ==============
    public function bannerIndex(): JsonResponse
    {
        return response()->json(['data' => Banner::orderBy('sort_order')->get()]);
    }

    public function bannerStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|string|max:500',
            'position' => 'sometimes|string|max:50',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|integer|in:0,1',
        ]);
        $validated['image'] = '/storage/' . $request->file('image')->store('banners', 'public');
        return response()->json(['message' => 'Tạo banner thành công', 'data' => Banner::create($validated)], 201);
    }

    public function bannerUpdate(Request $request, Banner $banner): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|string|max:500',
            'position' => 'sometimes|string|max:50',
            'sort_order' => 'sometimes|integer',
            'status' => 'sometimes|integer|in:0,1',
        ]);
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $banner->image));
            $validated['image'] = '/storage/' . $request->file('image')->store('banners', 'public');
        }
        $banner->update($validated);
        return response()->json(['message' => 'Cập nhật banner thành công', 'data' => $banner]);
    }

    public function bannerDestroy(Banner $banner): JsonResponse
    {
        $banner->delete();
        return response()->json(['message' => 'Xóa banner thành công']);
    }

    // ============= PROMOTIONS ==============
    public function promotionIndex(): JsonResponse
    {
        return response()->json(['data' => Promotion::with('products:id,name')->orderByDesc('created_at')->get()]);
    }

    public function promotionStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'nullable|string|max:50',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'status' => 'sometimes|integer|in:0,1',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        if ($request->hasFile('image')) {
            $validated['image'] = '/storage/' . $request->file('image')->store('promotions', 'public');
        }
        $promotion = Promotion::create($validated);
        if (!empty($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }
        return response()->json(['message' => 'Tạo khuyến mãi thành công', 'data' => $promotion->load('products')], 201);
    }

    public function promotionUpdate(Request $request, Promotion $promotion): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'type' => 'nullable|string|max:50',
            'start_at' => 'sometimes|date',
            'end_at' => 'sometimes|date',
            'status' => 'sometimes|integer|in:0,1',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        if ($request->hasFile('image')) {
            if ($promotion->image) Storage::disk('public')->delete(str_replace('/storage/', '', $promotion->image));
            $validated['image'] = '/storage/' . $request->file('image')->store('promotions', 'public');
        }
        $promotion->update($validated);
        if (isset($validated['product_ids'])) {
            $promotion->products()->sync($validated['product_ids']);
        }
        return response()->json(['message' => 'Cập nhật khuyến mãi thành công', 'data' => $promotion->load('products')]);
    }

    public function promotionDestroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();
        return response()->json(['message' => 'Xóa khuyến mãi thành công']);
    }

    // ============= CONTACTS ==============
    public function contactIndex(): JsonResponse
    {
        return response()->json(['data' => Contact::orderByDesc('created_at')->paginate(20)]);
    }

    public function contactMarkRead(Contact $contact): JsonResponse
    {
        $contact->update(['is_read' => true]);
        return response()->json(['message' => 'Đã đánh dấu đã đọc', 'data' => $contact]);
    }

    // ============= BRANCHES ==============
    public function branchIndex(): JsonResponse
    {
        return response()->json(['data' => Branch::all()]);
    }

    public function branchStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);
        return response()->json(['message' => 'Tạo chi nhánh thành công', 'data' => Branch::create($validated)], 201);
    }

    public function branchUpdate(Request $request, Branch $branch): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string',
            'phone' => 'nullable|string|max:20',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);
        $branch->update($validated);
        return response()->json(['message' => 'Cập nhật chi nhánh thành công', 'data' => $branch]);
    }

    public function branchDestroy(Branch $branch): JsonResponse
    {
        $branch->delete();
        return response()->json(['message' => 'Xóa chi nhánh thành công']);
    }
}
