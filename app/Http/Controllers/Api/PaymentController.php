<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function createVnpay(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = $request->user()->orders()->findOrFail($validated['order_id']);

        if ($order->payment_method !== 'vnpay') {
            return response()->json(['message' => 'Đơn hàng không sử dụng VNPay'], 422);
        }

        // VNPay payment URL generation
        $vnp_Url = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_TmnCode = config('services.vnpay.tmn_code', 'DEMO');
        $vnp_HashSecret = config('services.vnpay.hash_secret', 'DEMO_SECRET');

        $vnp_TxnRef = $order->order_code;
        $vnp_OrderInfo = 'Thanh toan don hang ' . $order->order_code;
        $vnp_Amount = $order->total * 100;
        $vnp_Locale = 'vn';
        $vnp_ReturnUrl = config('services.vnpay.return_url', url('/api/payments/vnpay/callback'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = http_build_query($inputData);
        $vnpSecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url .= "?" . $query . '&vnp_SecureHash=' . $vnpSecureHash;

        return response()->json([
            'payment_url' => $vnp_Url,
        ]);
    }

    public function vnpayCallback(Request $request): JsonResponse
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret', 'DEMO_SECRET');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';

        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $orderCode = $inputData['vnp_TxnRef'] ?? '';
        $order = \App\Models\Order::where('order_code', $orderCode)->first();

        if (!$order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        if ($inputData['vnp_ResponseCode'] === '00') {
            $order->update(['status' => 'confirmed']);
            $order->payment?->update([
                'status' => 'success',
                'transaction_id' => $inputData['vnp_TransactionNo'] ?? null,
                'paid_at' => now(),
                'response_data' => $inputData,
            ]);
        } else {
            $order->payment?->update([
                'status' => 'failed',
                'response_data' => $inputData,
            ]);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    public function createMomo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = $request->user()->orders()->findOrFail($validated['order_id']);

        $endpoint = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $partnerCode = config('services.momo.partner_code', 'MOMO_DEMO');
        $accessKey = config('services.momo.access_key', 'DEMO_ACCESS');
        $secretKey = config('services.momo.secret_key', 'DEMO_SECRET');

        $orderId = $order->order_code . '_' . time();
        $orderInfo = 'Thanh toan don hang ' . $order->order_code;
        $amount = (string) $order->total;
        // Chỉnh port FE tuỳ cấu hình (ví dụ lúc test user dùng 3001)
        $redirectUrl = config('services.momo.redirect_url', url('/payment/result'));
        $ipnUrl = config('services.momo.ipn_url', config('app.url') . '/api/payments/momo/callback');
        $requestId = time() . "";
        $requestType = "payWithMethod";
        $extraData = "";

        $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $payload = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            "storeId" => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature,
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::post($endpoint, $payload);
            $result = $response->json();

            if (isset($result['payUrl'])) {
                return response()->json([
                    'payment_url' => $result['payUrl']
                ]);
            }

            return response()->json([
                'message' => 'Lỗi từ MoMo: ' . ($result['message'] ?? 'Unknown error'),
                'momo_response' => $result
            ], 400);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi kết nối đến MoMo: ' . $e->getMessage()], 500);
        }
    }

    public function momoCallback(Request $request): JsonResponse
    {
        $data = $request->all();
        $orderCode = explode('_', $data['orderId'] ?? '')[0] ?? '';
        $order = \App\Models\Order::where('order_code', $orderCode)->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (($data['resultCode'] ?? '') == 0) {
            $order->update(['status' => 'confirmed']);
            $order->payment?->update([
                'status' => 'success',
                'transaction_id' => $data['transId'] ?? null,
                'paid_at' => now(),
                'response_data' => $data,
            ]);
        } else {
            $order->payment?->update([
                'status' => 'failed',
                'response_data' => $data,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
