<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ShopController extends Controller
{
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('transactions.history', compact('orders'));
    }

    public function deposit()
    {
        return view('transactions.deposit');
    }

    // --- NEW: HÀM MUA GÓI TỰ ĐỘNG ---
    public function upgradePlan(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro,premium'
        ]);

        $planKey = $request->plan;
        $user = Auth::user();

        // 1. Cấu hình giá (Khớp với giao diện bạn yêu cầu)
        $plans = [
            'basic'   => ['price' => 20000,  'name' => 'Cơ bản', 'days' => 30, 'limit' => 50],
            'pro'     => ['price' => 79000,  'name' => 'Chuyên nghiệp', 'days' => 30, 'limit' => 200],
            'premium' => ['price' => 150000, 'name' => 'Cao cấp', 'days' => 30, 'limit' => 1000],
        ];

        $selectedPlan = $plans[$planKey];
        $price = $selectedPlan['price'];

        // 2. Kiểm tra số dư
        if ($user->balance < $price) {
            return response()->json([
                'success' => false,
                'message' => 'Số dư không đủ. Vui lòng nạp thêm ' . number_format($price - $user->balance) . 'đ'
            ], 400);
        }

        // 3. Thực hiện giao dịch (Dùng Transaction để an toàn)
        DB::beginTransaction();
        try {
            // Trừ tiền
            $user->balance -= $price;

            // Cập nhật gói
            $user->plan_type = $planKey;

            // Tính ngày hết hạn:
            // Nếu đang còn hạn VIP thì cộng dồn, nếu không thì tính từ hôm nay
            $currentExpiry = $user->vip_expires_at ? Carbon::parse($user->vip_expires_at) : Carbon::now();
            if ($currentExpiry->isPast()) {
                $currentExpiry = Carbon::now();
            }
            $user->vip_expires_at = $currentExpiry->addDays($selectedPlan['days']);

            // Reset quota usage để khách dùng được luôn gói mới
            $user->daily_usage_count = 0;

            $user->save();

            // Lưu lịch sử giao dịch (Nếu bạn có bảng transaction/history)
            // History::create([...]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Nâng cấp gói {$selectedPlan['name']} thành công! Hạn dùng đến " . $user->vip_expires_at->format('d/m/Y')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upgrade Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau.'], 500);
        }
    }
}
