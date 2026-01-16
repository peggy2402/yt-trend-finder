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
        Log::info('--- WEBHOOK START (FORMAT: ZT{id}CKNH) ---');
        Log::info('Data nhận được:', $request->all());

        try {
            $data = $request->all();

            $amount = $data['transferAmount'] ?? 0;
            $content = $data['content'] ?? ''; 
            $bankTransId = $data['referenceCode'] ?? null;

            if (!$bankTransId) {
                return response()->json(['success' => false, 'message' => 'Missing referenceCode']);
            }

            // Check trùng
            if (Transaction::where('transaction_code', $bankTransId)->exists()) {
                return response()->json(['success' => true, 'message' => 'Already processed']);
            }

            // --- PHẦN QUAN TRỌNG: SỬA REGEX ---
            // Cũ: /ZENTRA\s*(\d+)/i
            // Mới: Tìm chữ "ZT", ngay sau đó là số (ID), ngay sau đó là "CKNH"
            // Ví dụ: "ZT5CKNH" -> ID = 5
            // Ví dụ: "ZT102CKNH" -> ID = 102
            // \s* cho phép có hoặc không có khoảng trắng (đề phòng ngân hàng tự tách chữ)
            
            if (preg_match('/ZT\s*(\d+)\s*CKNH/i', $content, $matches)) {
                $userId = $matches[1]; // Số ID tìm được
                Log::info("Regex khớp! Tìm thấy User ID: {$userId}");
            } else {
                // Fallback: Nếu không khớp ZT...CKNH thì thử tìm ZENTRA cũ xem sao (Hỗ trợ song song)
                if (preg_match('/ZENTRA\s*(\d+)/i', $content, $matches_backup)) {
                    $userId = $matches_backup[1];
                    Log::info("Fallback khớp ZENTRA! ID: {$userId}");
                } else {
                    Log::error("Lỗi: Nội dung '{$content}' không đúng cú pháp ZT{id}CKNH");
                    return response()->json(['success' => false, 'message' => 'Syntax error']);
                }
            }

            $user = User::find($userId);

            if (!$user) {
                Log::error("Lỗi: User ID {$userId} không tồn tại.");
                return response()->json(['success' => false, 'message' => 'User not found']);
            }

            // Cộng tiền
            DB::transaction(function () use ($user, $amount, $bankTransId, $content) {
                $user->balance += $amount;
                $user->save();

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'transaction_code' => $bankTransId,
                    'description' => $content,
                    'status' => 'success'
                ]);
            });

            Log::info("SUCCESS: Đã cộng {$amount} cho User {$userId}.");
            return response()->json(['success' => true, 'message' => 'Topup successful']);

        } catch (\Exception $e) {
            Log::error('EXCEPTION: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}