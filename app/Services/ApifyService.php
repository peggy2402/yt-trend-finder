<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApifyService
{
    protected $tokens;
    protected $actorId;

    public function __construct()
    {
        // 1. Ưu tiên lấy Token từ Database (Bảng settings)
        $dbTokens = null;
        try {
            // Sử dụng Query Builder trực tiếp để tránh lỗi nếu chưa có Model
            $setting = DB::table('settings')->where('key', 'apify_tokens')->first();
            if ($setting) {
                $dbTokens = $setting->value;
            }
        } catch (\Exception $e) {
            // Fallback nếu chưa chạy migration
        }

        // 2. Nếu DB không có, lấy từ .env làm dự phòng
        $tokenString = $dbTokens ?? env('APIFY_TOKENS', env('APIFY_TOKEN', ''));

        // 3. Chuyển chuỗi thành mảng, lọc bỏ khoảng trắng
        $this->tokens = array_values(array_filter(array_map('trim', explode(',', $tokenString))));

        // ID chuẩn của Actor
        $this->actorId = 'clockworks~tiktok-scraper';
    }

    /**
     * Lấy token đang hoạt động (có cơ chế xoay vòng)
     */
    protected function getActiveToken()
    {
        if (empty($this->tokens)) return null;

        // Lấy index của token đang dùng từ cache (để dùng ổn định 1 token cho đến khi lỗi)
        $currentIndex = Cache::get('apify_token_index', 0);

        if (!isset($this->tokens[$currentIndex])) {
            $currentIndex = 0; // Reset nếu index vượt quá
        }

        return $this->tokens[$currentIndex];
    }

    /**
     * Chuyển sang token tiếp theo khi gặp lỗi
     */
    protected function rotateToken()
    {
        $currentIndex = Cache::get('apify_token_index', 0);
        $nextIndex = ($currentIndex + 1) % count($this->tokens);
        Cache::put('apify_token_index', $nextIndex);

        Log::warning("Apify: Switching to token index #{$nextIndex}");
        return $this->tokens[$nextIndex];
    }

    // --- HÀM KIỂM TRA KEY (QUAN TRỌNG) ---
    public function checkKeyStatus($token)
    {
        try {
            // Gọi vào endpoint nhẹ nhất của Apify để test
            $response = Http::withToken($token)->timeout(5)->get("https://api.apify.com/v2/users/me");

            if ($response->successful()) {
                $data = $response->json();

                // Lấy thông tin giới hạn (để biết còn tiền không)
                $limits = $data['data']['limits'] ?? [];
                $planName = $data['data']['subscription']['name'] ?? 'Free';

                return [
                    'status' => 'alive',
                    'message' => 'Hoạt động',
                    'email' => $data['data']['email'] ?? 'Ẩn',
                    'plan' => $planName
                ];
            }

            if ($response->status() === 401) {
                return ['status' => 'dead', 'message' => 'Sai Token'];
            }

            return ['status' => 'error', 'message' => 'Lỗi: ' . $response->status()];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Lỗi mạng'];
        }
    }

    public function getTrending($region = 'US', $limit = 50)
    {
        $token = $this->getActiveToken();

        if (!$token) {
            return ['success' => false, 'message' => 'Hệ thống chưa cấu hình API Key. Vui lòng liên hệ Admin.'];
        }

        // Logic Hashtag (Giữ nguyên)
        $proxyCountry = ($region === 'ALL') ? 'US' : strtoupper($region);
        $hashtags = ['fyp', 'trending', 'viral'];
        if ($region == 'VN') $hashtags = ['xuhuong', 'trending', 'hot', 'tiktokvn'];
        if ($region == 'JP') $hashtags = ['fyp', 'おすすめ'];
        if ($region == 'KR') $hashtags = ['fyp', '추천'];
        if ($region == 'DE') $hashtags = ['fyp', 'fürdich', 'viral'];

        $input = [
            "resultsPerPage" => (int)$limit,
            "maxItems" => (int)$limit,
            "searchSection" => "/video",
            "searchType" => "video",
            "hashtags" => $hashtags,
            "proxyCountryCode" => $proxyCountry,

            "excludePinnedPosts" => true,
            "scrapeRelatedVideos" => false,
            "shouldDownloadAvatars" => false,
            "shouldDownloadCovers" => false,
            "shouldDownloadMusicCovers" => false,
            "shouldDownloadSlideshowImages" => false,
            "shouldDownloadSubtitles" => false,
            "shouldDownloadVideos" => false,
        ];

        // Cơ chế Retry với Token Rotation
        $maxRetries = count($this->tokens); // Thử hết số token đang có
        $attempts = 0;

        while ($attempts < $maxRetries) {
            try {
                $response = Http::withToken($token)
                    ->timeout(120)
                    ->post("https://api.apify.com/v2/acts/{$this->actorId}/run-sync-get-dataset-items", $input);

                // Nếu thành công -> Trả về luôn
                if ($response->successful()) {
                    $data = $response->json();
                    if (empty($data)) return ['success' => false, 'message' => 'Apify trả về rỗng.'];
                    return ['success' => true, 'data' => $data];
                }

                // Nếu lỗi 401 (Sai token), 402 (Hết tiền), 429 (Quá tải) -> Đổi token
                if (in_array($response->status(), [401, 402, 429])) {
                    Log::error("Apify Token Failed [{$response->status()}]: " . substr($token, 0, 10) . "...");
                    $token = $this->rotateToken(); // Lấy token mới
                    $attempts++;
                    continue; // Thử lại vòng lặp
                }

                // Các lỗi khác (500, 404) -> Dừng luôn
                return ['success' => false, 'message' => 'Lỗi Apify: ' . $response->status()];

            } catch (\Exception $e) {
                Log::error("Apify Exception: " . $e->getMessage());
                $token = $this->rotateToken();
                $attempts++;
            }
        }

        return ['success' => false, 'message' => 'Tất cả API Token đều lỗi hoặc hết hạn mức.'];
    }
}
