<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use Illuminate\Http\JsonResponse;

class FlashSaleController extends Controller
{
    /**
     * Flash sale đang diễn ra
     */
    public function active(): JsonResponse
    {
        $flashSales = FlashSale::active()
            ->with(['items.product:id,name,slug,thumbnail,price,sale_price'])
            ->get();

        $flashSales->each(function ($flashSale) {
            $flashSale->items->each(function ($item) {
                // % giảm giá
                $item->discount_percent = $item->product && $item->product->price > 0
                    ? round((($item->product->price - $item->flash_price) / $item->product->price) * 100)
                    : 0;
                // Số lượng còn lại
                $item->remaining = max(0, $item->quantity - $item->sold);
                // % đã bán (cho progress bar)
                $item->sold_percent = $item->quantity > 0
                    ? round(($item->sold / $item->quantity) * 100)
                    : 0;
            });
        });

        return response()->json(['data' => $flashSales]);
    }

    /**
     * Flash sale sắp diễn ra
     */
    public function upcoming(): JsonResponse
    {
        $flashSales = FlashSale::upcoming()
            ->with(['items.product:id,name,slug,thumbnail,price,sale_price'])
            ->orderBy('start_at')
            ->limit(5)
            ->get();

        $flashSales->each(function ($flashSale) {
            $flashSale->items->each(function ($item) {
                $item->discount_percent = $item->product && $item->product->price > 0
                    ? round((($item->product->price - $item->flash_price) / $item->product->price) * 100)
                    : 0;
                $item->remaining = max(0, $item->quantity - $item->sold);
                $item->sold_percent = $item->quantity > 0
                    ? round(($item->sold / $item->quantity) * 100)
                    : 0;
            });
        });

        return response()->json(['data' => $flashSales]);
    }

    /**
     * Trang Flash Sale: trả về tất cả (active + upcoming) trong 1 response
     */
    public function page(): JsonResponse
    {
        $transformItems = function ($flashSale) {
            $flashSale->items->each(function ($item) {
                $item->discount_percent = $item->product && $item->product->price > 0
                    ? round((($item->product->price - $item->flash_price) / $item->product->price) * 100)
                    : 0;
                $item->remaining = max(0, $item->quantity - $item->sold);
                $item->sold_percent = $item->quantity > 0
                    ? round(($item->sold / $item->quantity) * 100)
                    : 0;
            });
        };

        // Flash sale đang diễn ra
        $active = FlashSale::active()
            ->with(['items.product:id,name,slug,thumbnail,price,sale_price'])
            ->get();
        $active->each($transformItems);

        // Flash sale sắp diễn ra (các khung giờ tiếp theo)
        $upcoming = FlashSale::upcoming()
            ->with(['items.product:id,name,slug,thumbnail,price,sale_price'])
            ->orderBy('start_at')
            ->limit(5)
            ->get();
        $upcoming->each($transformItems);

        return response()->json([
            'active' => $active,
            'upcoming' => $upcoming,
        ]);
    }
}
