<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);

        Contact::create($validated);

        return response()->json([
            'message' => 'Gửi liên hệ thành công. Chúng tôi sẽ phản hồi sớm nhất.',
        ], 201);
    }

    public function branches(): JsonResponse
    {
        $branches = Branch::all();

        return response()->json(['data' => $branches]);
    }
}
