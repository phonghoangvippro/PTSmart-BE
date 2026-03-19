<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $wishlists = $request->user()->wishlists()
            ->with('product:id,name,slug,thumbnail,price,sale_price,rating_avg')
            ->get();

        return response()->json(['data' => $wishlists]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Sản phẩm đã có trong danh sách yêu thích'], 422);
        }

        Wishlist::create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Thêm vào yêu thích thành công'], 201);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        return response()->json(['message' => 'Xóa khỏi yêu thích thành công']);
    }
}
