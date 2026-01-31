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
        // 1. Ưu tiên lấy Token từ Database
        $dbTokens = null;
        try {
            $setting = DB::table('settings')->where('key', 'apify_tokens')->first();
            if ($setting) $dbTokens = $setting->value;
        } catch (\Exception $e) {}

        // 2. Fallback .env
        $tokenString = $dbTokens ?? env('APIFY_TOKENS', env('APIFY_TOKEN', ''));
        // Tách chuỗi bằng cả dấu phẩy và xuống dòng
        $this->tokens = preg_split('/[\r\n,]+/', $tokenString, -1, PREG_SPLIT_NO_EMPTY);
        $this->tokens = array_map('trim', $this->tokens);

        $this->actorId = 'clockworks~tiktok-scraper';
    }

    protected function getActiveToken()
    {
        if (empty($this->tokens)) return null;
        $currentIndex = Cache::get('apify_token_index', 0);
        if (!isset($this->tokens[$currentIndex])) $currentIndex = 0;
        return $this->tokens[$currentIndex];
    }

    protected function rotateToken()
    {
        $currentIndex = Cache::get('apify_token_index', 0);
        $nextIndex = ($currentIndex + 1) % count($this->tokens);
        Cache::put('apify_token_index', $nextIndex);
        Log::warning("Apify: Rotating to token index #{$nextIndex}");
        return $this->tokens[$nextIndex];
    }

    // --- LOGIC CHECK TIỀN TRIỆT ĐỂ ---
    public function checkKeyStatus($token)
    {
        try {
            // 1. Gọi API lấy thông tin user (để lấy email)
            $userResponse = Http::withToken($token)->timeout(10)->get("https://api.apify.com/v2/users/me");

            if (!$userResponse->successful()) {
                if ($userResponse->status() === 401) {
                    return [
                        'status' => 'dead',
                        'message' => 'Token sai/hết hạn',
                        'plan' => '$0.00 / $5.00',
                        'remaining' => 0,
                        'usageUsd' => 0,
                        'limitUsd' => 5.0
                    ];
                }
                return [
                    'status' => 'error',
                    'message' => 'Lỗi API user: ' . $userResponse->status(),
                    'plan' => 'Error',
                    'remaining' => 0,
                    'usageUsd' => 0,
                    'limitUsd' => 5.0
                ];
            }

            $userData = $userResponse->json();
            $user = $userData['data'] ?? [];
            $email = $user['email'] ?? 'N/A';

            // 2. Gọi API LIMITS để lấy giới hạn và số tiền đã dùng CHÍNH XÁC
            $limitsResponse = Http::withToken($token)->timeout(10)->get("https://api.apify.com/v2/users/me/limits");

            $usageUsd = 0.0;
            $limitUsd = 5.0; // Mặc định cho free plan

            if ($limitsResponse->successful()) {
                $limitsData = $limitsResponse->json();
                $limits = $limitsData['data'] ?? [];

                \Log::info('APIFY LIMITS RESPONSE FOR ' . substr($token, 0, 8), $limits);

                // Lấy giới hạn từ data.limits.maxMonthlyUsageUsd
                if (isset($limits['limits']['maxMonthlyUsageUsd'])) {
                    $limitUsd = (float)$limits['limits']['maxMonthlyUsageUsd'];
                }

                // Lấy số tiền ĐÃ DÙNG từ data.current.monthlyUsageUsd
                if (isset($limits['current']['monthlyUsageUsd'])) {
                    $usageUsd = (float)$limits['current']['monthlyUsageUsd'];
                }
            }

            // 3. Nếu không lấy được từ limits, thử từ USAGE endpoint
            if ($usageUsd === 0.0) {
                $usageResponse = Http::withToken($token)->timeout(10)->get("https://api.apify.com/v2/users/me/usage/monthly");

                if ($usageResponse->successful()) {
                    $usageData = $usageResponse->json();
                    $usageInfo = $usageData['data'] ?? [];

                    // Lấy số tiền đã dùng từ totalUsageCreditsUsdAfterVolumeDiscount
                    if (isset($usageInfo['totalUsageCreditsUsdAfterVolumeDiscount'])) {
                        $usageUsd = (float)$usageInfo['totalUsageCreditsUsdAfterVolumeDiscount'];
                    }
                    // Hoặc từ totalUsageCreditsUsdBeforeVolumeDiscount
                    elseif (isset($usageInfo['totalUsageCreditsUsdBeforeVolumeDiscount'])) {
                        $usageUsd = (float)$usageInfo['totalUsageCreditsUsdBeforeVolumeDiscount'];
                    }
                }
            }

            // 4. Tính toán số dư CHÍNH XÁC với 6 chữ số thập phân
            $remaining = $limitUsd - $usageUsd;

            // 5. Ước tính chi phí job dựa trên lỗi bạn gặp
            // Từ các lỗi: $0.172109, $0.201765, $0.205308
            // Lấy mức cao nhất: $0.21 + thêm buffer $0.04 = $0.25
            $estimatedJobCost = 0.25;

            // 6. XÁC ĐỊNH TRẠNG THÁI CHÍNH XÁC
            $status = 'alive';
            $message = 'Hoạt động';

            // Key chỉ SỐNG nếu còn đủ $estimatedJobCost để chạy job
            if ($remaining < $estimatedJobCost) {
                $status = 'dead';
                $message = 'Không đủ tiền (Còn $' . number_format($remaining, 6) . ')';
            }
            // Cảnh báo vàng nếu sắp hết (dưới $1)
            elseif ($remaining < 1.0) {
                $message = 'Còn $' . number_format($remaining, 3);
            }

            // 7. Format hiển thị với 5 chữ số thập phân cho usage
            $planDisplay = '$' . number_format($usageUsd, 5) . ' / $' . number_format($limitUsd, 2);

            // DEBUG LOG
            \Log::info("TOKEN STATUS RESULT", [
                'token' => substr($token, 0, 8) . '...',
                'email' => $email,
                'usageUsd' => $usageUsd,
                'limitUsd' => $limitUsd,
                'remaining' => $remaining,
                'status' => $status,
                'estimated_cost' => $estimatedJobCost
            ]);

            return [
                'status' => $status,
                'message' => $message,
                'email' => $email,
                'plan' => $planDisplay,
                'remaining' => $remaining,
                'usageUsd' => $usageUsd,
                'limitUsd' => $limitUsd,
                'estimated_job_cost' => $estimatedJobCost
            ];

        } catch (\Exception $e) {
            \Log::error('Apify checkKeyStatus Exception: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Lỗi kết nối mạng',
                'plan' => 'Error',
                'remaining' => 0,
                'usageUsd' => 0,
                'limitUsd' => 5.0
            ];
        }
    }

    public function getTrending($region = 'US', $limit = 50)
    {
        $token = $this->getActiveToken();
        if (!$token) return ['success' => false, 'message' => 'Hệ thống chưa cấu hình API Key.'];

        $proxyCountry = ($region === 'ALL') ? 'US' : strtoupper($region);

        $hashtags = ['fyp', 'trending', 'viral'];
        if ($region == 'VN') $hashtags = ['xuhuong', 'tiktokvn', 'hot'];
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

        $maxRetries = count($this->tokens) + 1; // Thử hết vòng + 1
        $attempts = 0;

        while ($attempts < $maxRetries) {
            try {
                $response = Http::withToken($token)
                    ->timeout(120)
                    ->post("https://api.apify.com/v2/acts/{$this->actorId}/run-sync-get-dataset-items", $input);

                // Case 1: Thành công
                if ($response->successful()) {
                    $data = $response->json();
                    if (empty($data)) return ['success' => false, 'message' => 'Apify trả về rỗng.'];
                    return ['success' => true, 'data' => $data];
                }

                // Case 2: Lỗi cần đổi Key
                $status = $response->status();
                $body = $response->body();
                $shouldRotate = false;

                // 401: Unauthorized
                // 402: Payment Required (Hết tiền)
                // 429: Too Many Requests (Rate limit)
                // 422: Unprocessable Entity (Thường chứa lỗi "exceed usage")
                if (in_array($status, [401, 402, 429])) {
                    $shouldRotate = true;
                }
                elseif ($status === 422 && str_contains($body, 'exceed')) {
                    // Bắt chính xác lỗi "exceed your remaining usage"
                    $shouldRotate = true;
                }

                if ($shouldRotate) {
                    Log::warning("Apify Key Failed [$status] - Rotating...");
                    $token = $this->rotateToken();
                    $attempts++;
                    continue; // Thử lại ngay với key mới
                }

                // Case 3: Lỗi khác (Server Error, v.v.)
                return ['success' => false, 'message' => 'Lỗi Apify: ' . $status . ' - ' . ($response->json()['error']['message'] ?? '')];

            } catch (\Exception $e) {
                // Lỗi mạng -> Đổi key thử vận may
                $token = $this->rotateToken();
                $attempts++;
            }
        }

        return ['success' => false, 'message' => 'Xin lỗi bạn nhé! Hệ thống đang tự động sửa lỗi. Vui lòng thử lại sau ít phút.'];
    }
}
