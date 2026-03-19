<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());

        $cart->load(['items.product', 'items.variant']);

        return response()->json(['data' => $cart]);
    }

    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'sometimes|integer|min:1',
        ]);

        $cart = $this->getOrCreateCart($request->user());
        $product = Product::findOrFail($validated['product_id']);

        // Check stock
        $quantity = $validated['quantity'] ?? 1;
        if ($product->stock < $quantity) {
            return response()->json(['message' => 'Sản phẩm không đủ hàng'], 422);
        }

        // Check if item already in cart
        $existingItem = $cart->items()
            ->where('product_id', $validated['product_id'])
            ->where('variant_id', $validated['variant_id'] ?? null)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'] ?? null,
                'quantity' => $quantity,
            ]);
        }

        $cart->load(['items.product', 'items.variant']);

        return response()->json([
            'message' => 'Thêm vào giỏ hàng thành công',
            'data' => $cart,
        ]);
    }

    public function updateItem(Request $request, CartItem $item): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());

        if ($item->cart_id !== $cart->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check stock
        if ($item->product->stock < $validated['quantity']) {
            return response()->json(['message' => 'Sản phẩm không đủ hàng'], 422);
        }

        $item->update(['quantity' => $validated['quantity']]);

        $cart->load(['items.product', 'items.variant']);

        return response()->json([
            'message' => 'Cập nhật giỏ hàng thành công',
            'data' => $cart,
        ]);
    }

    public function removeItem(Request $request, CartItem $item): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());

        if ($item->cart_id !== $cart->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'Xóa sản phẩm khỏi giỏ hàng thành công']);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request->user());
        $cart->items()->delete();

        return response()->json(['message' => 'Xóa toàn bộ giỏ hàng thành công']);
    }

    private function getOrCreateCart($user): Cart
    {
        return Cart::firstOrCreate(['user_id' => $user->id]);
    }
}
