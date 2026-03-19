<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'birthday' => 'sometimes|date',
            'gender' => 'sometimes|in:male,female,other',
            'city' => 'sometimes|string|max:100',
        ]);

        $request->user()->update($validated);

        return response()->json([
            'message' => 'Cập nhật thông tin thành công',
            'user' => $request->user()->fresh(),
        ]);
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar) {
            $oldPath = str_replace('/storage/', '', $user->avatar);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => '/storage/' . $path]);

        return response()->json([
            'message' => 'Upload avatar thành công',
            'avatar' => $user->avatar,
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu hiện tại không đúng',
            ], 422);
        }

        $user->update(['password' => $request->password]);

        return response()->json([
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }
}
