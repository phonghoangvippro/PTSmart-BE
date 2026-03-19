<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use Illuminate\Http\JsonResponse;

class FlashSaleController extends Controller
{
    public function active(): JsonResponse
    {
        $flashSales = FlashSale::active()
            ->with(['items.product:id,name,slug,thumbnail,price,rating_avg,sold_count'])
            ->get();

        return response()->json(['data' => $flashSales]);
    }

    public function upcoming(): JsonResponse
    {
        $flashSales = FlashSale::upcoming()
            ->with(['items.product:id,name,slug,thumbnail,price,rating_avg'])
            ->orderBy('start_at')
            ->limit(5)
            ->get();

        return response()->json(['data' => $flashSales]);
    }
}
