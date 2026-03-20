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
            'site_description' => 'nullable|string|max:500',
            'support_email' => 'nullable|email|max:255',
            'hotline' => 'nullable|string|max:50',
            'working_hours' => 'nullable|string|max:255',
            'default_shipping_fee' => 'nullable|numeric|min:0',
            'site_address' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|url|max:500',
            'social_instagram' => 'nullable|url|max:500',
            'social_youtube' => 'nullable|url|max:500',
            'site_logo' => 'nullable|image|max:2048',
        ]);

        $keys = [
            'site_name', 'site_description', 'support_email', 'hotline',
            'working_hours', 'default_shipping_fee', 'site_address',
            'social_facebook', 'social_instagram', 'social_youtube',
        ];

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
