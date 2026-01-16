<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SePayWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Ghi log để debug trên Render
        Log::info('--- WEBHOOK START (FORMAT: ZT{id}CKNH) ---');
        Log::info('Data nhận được:', $request->all());

        try {
            $data = $request->all();

            $amount = $data['transferAmount'] ?? 0;
            $content = $data['content'] ?? ''; 
            $bankTransId = $data['referenceCode'] ?? null;

            // 1. Kiểm tra dữ liệu đầu vào
            if (!$bankTransId) {
                return response()->json(['success' => false, 'message' => 'Missing referenceCode']);
            }

            // 2. Kiểm tra trùng lặp (Idempotency)
            if (Transaction::where('transaction_code', $bankTransId)->exists()) {
                Log::info("Giao dịch {$bankTransId} đã xử lý rồi -> Skip.");
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            // 3. Phân tích nội dung (Regex)
            // Ưu tiên cú pháp mới: ZT + ID + CKNH (VD: ZT5CKNH)
            if (preg_match('/ZT\s*(\d+)\s*CKNH/i', $content, $matches)) {
                $userId = $matches[1];
                Log::info("Regex khớp chuẩn ZT...CKNH! Tìm thấy User ID: {$userId}");
            } 
            // Fallback cú pháp cũ: ZENTRA + ID (VD: ZENTRA 5)
            else if (preg_match('/ZENTRA\s*(\d+)/i', $content, $matches_backup)) {
                $userId = $matches_backup[1];
                Log::info("Regex khớp fallback ZENTRA! ID: {$userId}");
            } 
            else {
                Log::error("Lỗi cú pháp: Nội dung '{$content}' không khớp định dạng ZT{id}CKNH");
                return response()->json(['success' => false, 'message' => 'Syntax error']);
            }

            // 4. Tìm User
            $user = User::find($userId);
            if (!$user) {
                Log::error("Lỗi: User ID {$userId} không tồn tại trong hệ thống.");
                return response()->json(['success' => false, 'message' => 'User not found']);
            }

            // 5. Cộng tiền & Lưu lịch sử (Atomic Transaction)
            DB::transaction(function () use ($user, $amount, $bankTransId, $content) {
                // Cộng tiền an toàn
                $user->increment('balance', $amount);

                // Lưu log giao dịch
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'transaction_code' => $bankTransId,
                    'description' => $content,
                    'status' => 'success'
                ]);
            });

            Log::info("SUCCESS: Đã cộng {$amount}đ cho User {$userId}.");
            return response()->json(['success' => true, 'message' => 'Topup successful']);

        } catch (\Exception $e) {
            Log::error('WEBHOOK EXCEPTION: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}