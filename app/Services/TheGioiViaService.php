<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class TheGioiViaService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        // Lấy cấu hình từ .env hoặc config/services.php
        // Đảm bảo URL luôn có dấu / ở cuối để nối chuỗi chính xác
        $this->baseUrl = rtrim(config('services.thegioivia.base_url', 'https://thegioivia.com/api/'), '/') . '/';
        $this->apiKey = config('services.thegioivia.api_key', '865edd2ddeaef4da15975b3cd5aa6af4nMHsa5uKSRFeAQtyq06TOmfobc8V4JxU');
    }

    /**
     * Gọi API phương thức GET
     * * @param string $endpoint Tên file endpoint (vd: products.php, product.php)
     * @param array $params Tham số query string bổ sung (vd: ['product' => 3])
     * @return array Dữ liệu JSON trả về từ API
     * @throws Exception Khi gọi API thất bại hoặc response lỗi
     */
    public function get(string $endpoint, array $params = [])
    {
        return $this->sendRequest('GET', $endpoint, $params);
    }

    /**
     * Gọi API phương thức POST (vd: Mua hàng)
     * * @param string $endpoint Tên endpoint (vd: buy_product)
     * @param array $data Dữ liệu form-data gửi đi
     * @return array Dữ liệu JSON trả về từ API
     * @throws Exception Khi gọi API thất bại hoặc response lỗi
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->sendRequest('POST', $endpoint, $data);
    }

    /**
     * Hàm xử lý trung tâm (Core Request Handler)
     * Tự động ghép URL, thêm API Key và xử lý lỗi
     */
    protected function sendRequest(string $method, string $endpoint, array $payload = [])
    {
        // 1. Xây dựng URL đầy đủ
        // Loại bỏ dấu / ở đầu endpoint để tránh lỗi (vd: /products.php -> products.php)
        $url = $this->baseUrl . ltrim($endpoint, '/');

        // 2. Chuẩn bị params cơ bản (API Key luôn cần thiết)
        $queryParams = ['api_key' => $this->apiKey];

        // 3. Cấu hình HTTP Client cơ bản
        $http = Http::timeout(30)       // Timeout sau 30s
                    ->retry(2, 100)     // Thử lại 2 lần nếu lỗi mạng
                    ->withHeaders([
                        'User-Agent' => 'Laravel-Client/1.0',
                        'Accept'     => 'application/json',
                    ]);

        try {
            $response = null;

            if (strtoupper($method) === 'GET') {
                // GET: Merge api_key và payload vào query string
                // Laravel Http::get tự động chuyển mảng params thành query string
                $finalParams = array_merge($queryParams, $payload);
                $response = $http->get($url, $finalParams);
            } else {
                // POST: API Key nằm trên URL (query string), Data nằm trong Body (form-data)
                // Theo tài liệu: POST https://thegioivia.com/api/buy_product?api_key=...
                // Hoặc API Key có thể nằm trong body tùy API, nhưng để chắc chắn ta để cả 2 hoặc theo query string như GET
                
                // Cách 1: Nối API Key vào URL query string
                $urlWithKey = $url . '?' . http_build_query($queryParams);
                
                // Cách 2: Nếu API yêu cầu api_key trong form-data body, uncomment dòng dưới:
                // $payload['api_key'] = $this->apiKey; 

                // Gửi POST request dạng form-data (asForm)
                $response = $http->asForm()->post($urlWithKey, $payload);
            }

            // 4. Xử lý Lỗi HTTP (404, 500...)
            if ($response->failed()) {
                throw new Exception("HTTP Error: " . $response->status() . " - " . $response->body());
            }

            // 5. Parse JSON
            $data = $response->json();

            // 6. Kiểm tra lỗi Logic từ API (Status success/error trong body response)
            // Ví dụ response: { "status": "error", "msg": "Hết hàng" }
            if (isset($data['status']) && $data['status'] !== 'success') {
                throw new Exception("API Logic Error: " . ($data['msg'] ?? 'Unknown Error'));
            }

            return $data;

        } catch (Exception $e) {
            // Log lỗi để debug
            \Log::error("TheGioiVia API Error [{$endpoint}]: " . $e->getMessage());
            throw $e;
        }
    }
}