<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ApifyService; // Import Service

class SettingController extends Controller
{
    public function index()
    {
        $apifyTokens = DB::table('settings')->where('key', 'apify_tokens')->value('value');
        return view('admin.settings', compact('apifyTokens'));
    }

    public function update(Request $request)
    {
        $tokens = $request->input('apify_tokens');

        // Chuẩn hóa: Tách bằng xuống dòng hoặc phẩy, xóa khoảng trắng
        $cleanTokens = implode(',', preg_split('/[\r\n,]+/', $tokens, -1, PREG_SPLIT_NO_EMPTY));

        DB::table('settings')->updateOrInsert(
            ['key' => 'apify_tokens'],
            ['value' => $cleanTokens, 'created_at' => now(), 'updated_at' => now()]
        );

        return back()->with('success', 'Đã cập nhật danh sách API Key thành công!');
    }

    public function checkKeys(Request $request, ApifyService $apifyService)
    {
        $tokensRaw = $request->input('tokens');
        if (!$tokensRaw) return response()->json([]);

        $tokens = array_values(array_filter(array_map('trim', explode(',', $tokensRaw))));
        $results = [];

        foreach ($tokens as $token) {
            if (strlen($token) < 10) continue;

            $status = $apifyService->checkKeyStatus($token);

            $results[] = [
                'token' => substr($token, 0, 8) . '...',
                'full_token' => $token,
                'status' => $status['status'],
                'message' => $status['message'],
                'email' => $status['email'] ?? '',
                'plan' => $status['plan'] ?? '$0.00000 / $5.00',
                'remaining' => $status['remaining'] ?? 0,
                'usageUsd' => $status['usageUsd'] ?? 0,
                'limitUsd' => $status['limitUsd'] ?? 5.0,
                'estimated_job_cost' => $status['estimated_job_cost'] ?? 0.25
            ];
        }

        return response()->json($results);
    }
}
