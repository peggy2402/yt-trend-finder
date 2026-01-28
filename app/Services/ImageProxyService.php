<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ImageProxyService
{
    /**
     * Proxy ảnh từ TikTok và Cache lại để tránh hit limit 429
     */
    public function proxy($url)
    {
        // Tạo cache key dựa trên URL ảnh
        $cacheKey = 'img_proxy_' . md5($url);

        return Cache::remember($cacheKey, 60 * 60, function () use ($url) { // Cache 1 tiếng
            try {
                $response = Http::withHeaders([
                    // Giả lập browser để TikTok không chặn
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Referer' => 'https://www.tiktok.com/',
                ])->get($url);

                if ($response->successful()) {
                    return [
                        'content' => $response->body(),
                        'mime' => $response->header('Content-Type'),
                        'status' => 200
                    ];
                }
            } catch (\Exception $e) {
                // Log error
            }

            // Return null nếu lỗi
            return null;
        });
    }
}
