<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckSaaSPlan
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Logic 7 ngày dùng thử
        // Nếu created_at null (hiếm gặp), coi như mới tạo
        $joinDate = $user->created_at ? Carbon::parse($user->created_at) : Carbon::now();
        $trialEnds = $joinDate->copy()->addDays(7);

        // Kiểm tra cột vip_expires_at (nếu có)
        // Nếu cột này null (chưa mua gói), vipExpires sẽ là null
        $vipExpires = $user->vip_expires_at ? Carbon::parse($user->vip_expires_at) : null;

        // isTrialActive: true nếu hôm nay <= ngày hết hạn dùng thử
        $isTrialActive = Carbon::now()->lte($trialEnds);

        // isVipActive: true nếu (có ngày hết hạn VIP) VÀ (hôm nay <= ngày đó)
        // Nếu vipExpires là null -> biểu thức này trả về false -> Đúng logic (Không VIP)
        $isVipActive   = $vipExpires && Carbon::now()->lte($vipExpires);

        // Chặn nếu KHÔNG còn dùng thử VÀ KHÔNG phải VIP
        if (!$isTrialActive && !$isVipActive) {
            return response()->json([
                'error' => 'PLAN_EXPIRED',
                'message' => "Gói dùng thử 7 ngày của bạn đã hết hạn. Vui lòng nạp tiền.",
                'upgrade_url' => route('deposit')
            ], 402);
        }

        return $next($request);
    }
}
