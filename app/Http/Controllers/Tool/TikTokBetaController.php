<?php

namespace App\Http\Controllers\Tool;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ApifyService;
use App\Services\AIService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TikTokBetaController extends Controller
{
    protected $apifyService;
    protected $aiService;

    public function __construct(ApifyService $apifyService, AIService $aiService)
    {
        $this->apifyService = $apifyService;
        $this->aiService = $aiService;
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();

        // --- 1. KIỂM TRA QUYỀN TRUY CẬP (LOGIC CHẶT CHẼ) ---

        $planType = $user->plan_type ?? 'free';
        $limits = ['free' => 10, 'basic' => 50, 'pro' => 200, 'premium' => 1000];

        // Mặc định limit
        $dailyLimit = $limits[$planType] ?? 10;
        $expiryText = '';
        $canScan = false;

        // B1: Kiểm tra VIP (Ưu tiên cao nhất)
        if ($user->vip_expires_at) {
            $vipExpiry = Carbon::parse($user->vip_expires_at);
            if ($now->lte($vipExpiry)) {
                // Còn hạn VIP
                $canScan = true;
                $diffDays = $now->diffInDays($vipExpiry, false);
                $expiryText = 'Hạn VIP: Còn ' . ceil($diffDays) . ' ngày';
            } else {
                // Vừa hết hạn VIP -> Reset về Free
                // (Chỉ cập nhật DB 1 lần để tránh query thừa)
                if ($planType !== 'free') {
                    $user->plan_type = 'free';
                    $user->vip_expires_at = null;
                    $user->save();
                    $planType = 'free';
                }
            }
        }

        // B2: Nếu không có VIP (hoặc đã hết), kiểm tra Dùng thử 7 ngày
        if (!$canScan) {
            $createdAt = $user->created_at ? Carbon::parse($user->created_at) : $now;
            $trialEnds = $createdAt->copy()->addDays(7); // 7 ngày dùng thử

            if ($now->lte($trialEnds)) {
                // Còn trong thời gian dùng thử
                $canScan = true;
                $dailyLimit = 10; // Giới hạn cứng cho dùng thử
                $diffDays = $now->diffInDays($trialEnds, false);
                $expiryText = 'Dùng thử: Còn ' . ceil($diffDays) . ' ngày';
            } else {
                // HẾT DÙNG THỬ VÀ KHÔNG CÓ VIP -> CHẶN
                $canScan = false;
                $expiryText = 'Đã hết hạn';
            }
        }

        // B3: Chặn nếu không được phép quét
        if (!$canScan) {
            return response()->json([
                'error' => 'Tài khoản đã hết hạn dùng thử/VIP. Vui lòng nạp tiền để tiếp tục.',
                'is_expired' => true
            ], 403);
        }

        // B4: Reset Quota theo ngày
        $todayStr = $now->format('Y-m-d');
        if ($user->last_usage_date !== $todayStr) {
            $user->daily_usage_count = 0;
            $user->last_usage_date = $todayStr;
            $user->save();
        }

        // B5: Check Quota trong ngày
        if ($user->daily_usage_count >= $dailyLimit) {
            return response()->json(['error' => "Đã hết lượt quét hôm nay ($dailyLimit lượt)."], 429);
        }

        // --- 2. GỌI APIFY (Xử lý quét) ---
        $region = strtoupper($request->input('region', 'US'));
        $limit = (int)$request->input('limit', 50);

        // Gọi service
        $result = $this->apifyService->getTrending($region, $limit);

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 500);
        }

        // --- 3. MAPPING DỮ LIỆU (Bất tử) ---
        $rawVideos = $result['data'];
        $processed = collect($rawVideos)->map(function ($item) {
            try {
                $uniqueId = data_get($item, 'authorMeta.name', 'unknown');
                $nickname = data_get($item, 'authorMeta.nickName', $uniqueId);
                $rawAvatar = data_get($item, 'authorMeta.avatar') ?: data_get($item, 'authorMeta.originalAvatarUrl', '');

                $desc = data_get($item, 'text', '');
                $duration = (int)data_get($item, 'videoMeta.duration', 0);
                $rawCover = data_get($item, 'videoMeta.coverUrl') ?: data_get($item, 'videoMeta.originalCoverUrl', '');

                $views = (int)data_get($item, 'playCount', 0);
                $likes = (int)data_get($item, 'diggCount', 0);
                $shares = (int)data_get($item, 'shareCount', 0);
                $comments = (int)data_get($item, 'commentCount', 0);
                $engagement = ($views > 0) ? (($likes + $shares + $comments) / $views) * 100 : 0;

                $createTimeISO = data_get($item, 'createTimeISO');
                $createTime = $createTimeISO ? Carbon::parse($createTimeISO) : now();
                $isBeta = $duration >= 60;
                $revenueEst = $isBeta ? number_format(($views / 1000) * 0.5 * 0.5, 2) : 0;

                return [
                    'id' => data_get($item, 'id', uniqid()),
                    'desc' => $desc,
                    'cover' => $rawCover ? url('/img-proxy?url=' . urlencode($rawCover)) : '',
                    'author' => [
                        'uniqueId' => $uniqueId,
                        'avatar' => $rawAvatar ? url('/img-proxy?url=' . urlencode($rawAvatar)) : '',
                        'nickname' => $nickname
                    ],
                    'stats' => [
                        'views' => $views, 'likes' => $likes, 'shares' => $shares,
                        'efficiency' => round($engagement, 2)
                    ],
                    'duration' => $duration,
                    'create_time_human' => $createTime->diffForHumans(),
                    'date_only' => $createTime->format('d/m/Y'),
                    'timestamp' => $createTime->timestamp,
                    'is_beta' => $isBeta,
                    'revenue_est' => $revenueEst,
                    'link' => data_get($item, 'webVideoUrl', "https://www.tiktok.com/@{$uniqueId}/video/" . data_get($item, 'id'))
                ];
            } catch (\Exception $e) { return null; }
        })->filter()->values()->all();

        // 4. Update Quota
        $user->increment('daily_usage_count');

        return response()->json([
            'meta' => [
                'found' => count($processed),
                'usage' => [
                    'used' => $user->daily_usage_count,
                    'limit' => $dailyLimit,
                    'plan_name' => $planType,
                    'expiry_text' => $expiryText
                ]
            ],
            'videos' => $processed
        ]);
    }

    public function getAiKeywords(Request $request) {
        $request->validate(['region' => 'string']);
        $user = Auth::user();
        if (!in_array($user->plan_type, ['pro', 'premium'])) {
            return response()->json(['is_demo' => true]);
        }
        $keywords = $this->aiService->suggestKeywords($request->input('region'));
        return response()->json(['keywords' => $keywords, 'is_demo' => false]);
    }

    // --- MỚI: HÀM MUA GÓI ---
    public function buyPlan(Request $request) {
        $request->validate([
            'plan' => 'required|in:basic,pro,premium'
        ]);

        $plan = $request->plan;
        $prices = [
            'basic' => 20000,
            'pro' => 79000,
            'premium' => 150000
        ];
        $price = $prices[$plan];
        $user = Auth::user();

        if ($user->balance < $price) {
            return response()->json([
                'message' => 'Số dư không đủ. Vui lòng nạp thêm.',
                'missing' => $price - $user->balance
            ], 400);
        }

        // Transaction DB để đảm bảo trừ tiền và cộng gói an toàn
        DB::beginTransaction();
        try {
            // Trừ tiền
            $user->balance -= $price;

            // Logic cộng ngày VIP
            $now = Carbon::now();
            $expiresAt = $user->vip_expires_at ? Carbon::parse($user->vip_expires_at) : $now;

            // QUAN TRỌNG: Sử dụng copy() để không làm biến đổi biến $now hoặc $expiresAt
            if ($expiresAt->lt($now)) {
                // Nếu đã hết hạn -> Tính từ bây giờ
                $newExpiry = $now->copy()->addMonth();
            } else {
                // Nếu còn hạn -> Cộng dồn thêm 1 tháng
                $newExpiry = $expiresAt->copy()->addMonth();
            }

            $user->plan_type = $plan;
            $user->vip_expires_at = $newExpiry;
            $user->save();

            DB::commit();

            // Tính toán lại limit để trả về frontend
            $limits = ['basic' => 50, 'pro' => 200, 'premium' => 1000];

            // Tính số ngày còn lại. Lưu ý: Dùng Carbon::now() mới để so sánh
            $daysLeft = Carbon::now()->diffInDays($newExpiry, false);

            return response()->json([
                'success' => true,
                'message' => 'Mua gói thành công!',
                'new_balance' => $user->balance,
                'plan_name' => ucfirst($plan),
                'new_limit' => $limits[$plan],
                'new_expiry' => 'Còn ' . ceil($daysLeft) . ' ngày' // Làm tròn lên
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi giao dịch: ' . $e->getMessage()], 500);
        }
    }
}
