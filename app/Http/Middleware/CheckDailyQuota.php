<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckDailyQuota
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 1. Reset bộ đếm nếu sang ngày mới
        $today = Carbon::now()->format('Y-m-d');
        if ($user->last_usage_date !== $today) {
            $user->daily_usage_count = 0;
            $user->last_usage_date = $today;
            $user->save();
        }

        // 2. Lấy cấu hình gói
        // Logic cũ của bạn dùng vip_expires_at để check VIP, ta sẽ merge logic đó vào đây
        $isVip = $user->vip_expires_at && Carbon::now()->lte($user->vip_expires_at);
        $planType = $isVip ? ($user->plan_type === 'free' ? 'basic' : $user->plan_type) : 'free';

        $planConfig = config("saas.plans.{$planType}") ?? config('saas.plans.free');
        $limit = $planConfig['daily_scans'];

        // 3. Kiểm tra giới hạn
        if ($user->daily_usage_count >= $limit) {
            return response()->json([
                'error' => 'QUOTA_EXCEEDED',
                'message' => "Bạn đã hết lượt quét trong ngày ($limit lượt). Vui lòng nâng cấp gói hoặc quay lại vào ngày mai.",
                'current_plan' => $planConfig['name']
            ], 429);
        }

        // 4. Nếu request thành công (được xử lý ở Controller), ta sẽ tăng count sau.
        // Tuy nhiên để đơn giản, ta tăng trước (hoặc dùng terminate middleware).
        // Ở đây tôi tăng luôn, chấp nhận request lỗi cũng tính 1 lượt để tránh spam.
        $user->increment('daily_usage_count');

        return $next($request);
    }
}
