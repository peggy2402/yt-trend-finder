<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TheGioiViaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ViaController extends Controller
{
    protected $apiService;

    public function __construct(TheGioiViaService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function profile()
    {
        $apiKey = env('THEGIOIVIA_API_KEY');

        $response = Http::get("https://thegioivia.com/api/profile.php", [
            'api_key' => $apiKey
        ]);

        return response()->json($response->json());
    }


    public function getProducts()
    {
        try {
            // Tự động gọi: .../products.php?api_key=...
            $data = $this->apiService->get('products.php');
            
            $products = [];
            if (isset($data['categories'])) {
                foreach ($data['categories'] as $cat) {
                    if (isset($cat['products'])) {
                        foreach ($cat['products'] as $prod) {
                            $prod['category_name'] = $cat['name'];
                            $products[] = $prod;
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'data' => $products]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Lấy chi tiết sản phẩm
    public function getProductDetail($id)
    {
        try {
            // Tự động gọi: .../product.php?api_key=...&product={id}
            $data = $this->apiService->get('product.php', [
                'product' => $id
            ]);

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    // Lay chi tiết đơn hàng
    public function getOrderDetail($orderId)
    {
        try {
            // Tự động gọi: .../order.php?api_key=...&order={orderId}
            $data = $this->apiService->get('order.php', [
                'order' => $orderId
            ]);

            return response()->json(['success' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mua hàng (POST buy product)
     * URL: /tool/buy
     */
    public function buyProduct(Request $request)
    {
        // Validate input đầu vào
        $request->validate([
            'product_id' => 'required|numeric',
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            // Tự động gọi: POST .../buy_product?api_key=...
            // Payload body: action, id, amount, coupon
            $payload = [
                'action' => 'buyProduct',
                'id'     => $request->input('product_id'),
                'amount' => $request->input('amount'),
            ];

            if ($request->has('coupon')) {
                $payload['coupon'] = $request->input('coupon');
            }

            $result = $this->apiService->post('buy_product', $payload);

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}