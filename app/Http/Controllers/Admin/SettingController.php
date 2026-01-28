<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ApifyService;

class SettingController extends Controller
{
    public function index()
    {
        // Lấy cấu hình hiện tại
        $apifyTokens = DB::table('settings')->where('key', 'apify_tokens')->value('value');

        return view('admin.settings', compact('apifyTokens'));
    }

    public function update(Request $request)
    {
        $tokens = $request->input('apify_tokens');

        // Lưu vào DB (Create or Update)
        DB::table('settings')->updateOrInsert(
            ['key' => 'apify_tokens'],
            [
                'value' => $tokens,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return back()->with('success', 'Đã cập nhật danh sách API Key thành công!');
    }

    // API check trạng thái key (Sửa lỗi 500)
    public function checkKeys(Request $request, ApifyService $apifyService)
    {
        $tokensRaw = $request->input('tokens');
        if (!$tokensRaw) return response()->json([]);

        // Tách chuỗi thành mảng token
        $tokens = array_values(array_filter(array_map('trim', explode(',', $tokensRaw))));
        $results = [];

        foreach ($tokens as $token) {
            // Bỏ qua token quá ngắn
            if (strlen($token) < 10) continue;

            // Gọi Service
            $status = $apifyService->checkKeyStatus($token);

            $results[] = [
                'token' => substr($token, 0, 8) . '...', // Chỉ hiện 1 phần key
                'full_token' => $token,
                'status' => $status['status'],
                'message' => $status['message'],
                'email' => $status['email'] ?? '',
                'plan' => $status['plan'] ?? ''
            ];
        }

        return response()->json($results);
    }
}
