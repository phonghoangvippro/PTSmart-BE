<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::pluck('value', 'key');
        return response()->json(['data' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'hotline' => 'nullable|string|max:50',
            'default_shipping_fee' => 'nullable|numeric|min:0',
            'site_logo' => 'nullable|image|max:2048', // 2MB
        ]);

        $keys = ['site_name', 'support_email', 'hotline', 'default_shipping_fee'];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key)]
                );
            }
        }

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('settings', 'public');
            Setting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => '/storage/' . $path]
            );
        }

        $settings = Setting::pluck('value', 'key');

        return response()->json([
            'message' => 'Cập nhật cài đặt thành công',
            'data' => $settings,
        ]);
    }
}
