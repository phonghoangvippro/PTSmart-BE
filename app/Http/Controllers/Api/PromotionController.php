<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        $promotions = Promotion::active()
            ->with('products:id,name,slug,thumbnail,price,sale_price')
            ->get();

        return response()->json(['data' => $promotions]);
    }
}
