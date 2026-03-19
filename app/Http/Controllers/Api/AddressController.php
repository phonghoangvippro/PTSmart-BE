<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();

        return response()->json(['data' => $addresses]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'type' => 'sometimes|in:home,office,other',
            'is_default' => 'sometimes|boolean',
        ]);

        $user = $request->user();

        // If setting as default, unset others
        if (!empty($validated['is_default'])) {
            $user->addresses()->update(['is_default' => false]);
        }

        // If first address, auto-set as default
        if ($user->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = $user->addresses()->create($validated);

        return response()->json([
            'message' => 'Thêm địa chỉ thành công',
            'data' => $address,
        ], 201);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string',
            'type' => 'sometimes|in:home,office,other',
        ]);

        $address->update($validated);

        return response()->json([
            'message' => 'Cập nhật địa chỉ thành công',
            'data' => $address,
        ]);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $address->delete();

        return response()->json(['message' => 'Xóa địa chỉ thành công']);
    }

    public function setDefault(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'Đặt địa chỉ mặc định thành công',
            'data' => $address,
        ]);
    }
}
