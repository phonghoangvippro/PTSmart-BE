<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = $request->user();

        // Validate order belongs to user and is completed
        $order = Order::where('id', $validated['order_id'])
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->firstOrFail();

        // Check product is in this order
        $inOrder = $order->items()->where('product_id', $validated['product_id'])->exists();
        if (!$inOrder) {
            return response()->json(['message' => 'Sản phẩm không thuộc đơn hàng này'], 422);
        }

        // Check if already reviewed
        $alreadyReviewed = Review::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->where('order_id', $validated['order_id'])
            ->exists();

        if ($alreadyReviewed) {
            return response()->json(['message' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng này'], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
            'order_id' => $validated['order_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        // Update product rating
        $product = Product::find($validated['product_id']);
        $product->update([
            'rating_avg' => $product->reviews()->avg('rating'),
            'review_count' => $product->reviews()->count(),
        ]);

        return response()->json([
            'message' => 'Đánh giá thành công',
            'data' => $review->load('user:id,name,avatar'),
        ], 201);
    }
}
