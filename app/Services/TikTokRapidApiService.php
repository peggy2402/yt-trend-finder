<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokRapidApiService
{
    protected $baseUrl;
    protected $headers;

    public function __construct()
    {
        // Cấu hình Host API Mới (Scrapper)
        $host = env('RAPID_API_TIKTOK_HOST', 'tiktok-scrapper-videos-music-challenges-downloader.p.rapidapi.com');

        $this->baseUrl = "https://{$host}";
        $this->headers = [
            'x-rapidapi-host' => $host,
            'x-rapidapi-key'  => env('RAPID_API_TIKTOK_KEY'),
        ];
    }

    /**
     * Hàm gọi API chung
     */
    public function get($endpoint, $params = [])
    {
        try {
            // Log request để debug
            Log::info("TikTok API Request: {$this->baseUrl}/{$endpoint}", $params);

            $response = Http::withHeaders($this->headers)
                ->withoutVerifying() // Bỏ qua lỗi SSL local
                ->get("{$this->baseUrl}/{$endpoint}", $params);

            // Log kết quả trả về
            if ($response->failed()) {
                Log::error("TikTok API Error: " . $response->body());
                return ['success' => false, 'message' => 'Lỗi API: ' . $response->status()];
            }

            return ['success' => true, 'data' => $response->json()];
        } catch (\Exception $e) {
            Log::error("TikTok API Exception: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()];
        }
    }

    /**
     * Lấy danh sách Trending theo Region (Thay thế cho Search Keyword)
     * Endpoint: /trending/{region}
     */
    public function getTrending($region = 'US')
    {
        // API mới dùng path parameter
        return $this->get("trending/{$region}");
    }
}
