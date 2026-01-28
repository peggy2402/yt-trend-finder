<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GOOGLE_API_KEY', '');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key={$this->apiKey}";
    }

    // Hàm phân tích video (Giữ nguyên)
    public function analyzeVideos($videos, $region)
    {
        if (empty($this->apiKey)) return "Chưa cấu hình API Key.";

        $inputData = collect($videos)->take(20)->map(function ($v) {
            return sprintf("- %s (Views: %s)", $v['desc'], number_format($v['stats']['views']));
        })->implode("\n");

        $prompt = "Phân tích xu hướng TikTok tại {$region} dựa trên dữ liệu:\n{$inputData}\n\nTrả lời ngắn gọn 3 ý chính về chủ đề đang hot.";

        return $this->callGemini($prompt);
    }

    // --- NÂNG CẤP: PROMPT THÔNG MINH HƠN ---
    public function suggestKeywords($region)
    {
        if (empty($this->apiKey)) {
            return ['Lỗi: Chưa cấu hình API Key', 'Vui lòng kiểm tra .env'];
        }

        $regionNames = [
            'VN' => 'Việt Nam', 'US' => 'Mỹ', 'JP' => 'Nhật Bản', 'KR' => 'Hàn Quốc',
            'DE' => 'Đức', 'FR' => 'Pháp', 'GB' => 'Anh'
        ];
        $regionName = $regionNames[$region] ?? $region;

        // Prompt mới: Yêu cầu cả Keyword (không dấu #) và Hashtag (có dấu #)
        $prompt = "Đóng vai chuyên gia TikTok Master. Hãy phân tích thị trường {$regionName} ngay lúc này.\n"
            . "Hãy liệt kê:\n"
            . "1. 5 Từ khóa tìm kiếm (Search Keywords) đang hot nhất để đặt tiêu đề (Không có dấu #).\n"
            . "2. 10 Hashtags thịnh hành nhất để gắn thẻ (Có dấu #).\n"
            . "Yêu cầu định dạng: Trả về duy nhất một danh sách các từ khóa và hashtags ngăn cách nhau bởi dấu phẩy. Không đánh số, không giải thích dòng nào.\n"
            . "Ví dụ output mong muốn: biến hình anime, review phim hay, funny cat, #trend, #xuhuong, #fyp, #viral";

        $result = $this->callGemini($prompt);

        // Xử lý làm sạch dữ liệu trả về
        $keywords = explode(',', $result);
        return array_values(array_filter(array_map(function($k) {
            $clean = trim($k);
            // Loại bỏ các ký tự đánh số đầu dòng nếu AI lỡ thêm vào (vd: "1. trend")
            $clean = preg_replace('/^[\d\.\-\s]+/', '', $clean);
            return $clean;
        }, $keywords)));
    }

    private function callGemini($prompt)
    {
        try {
            $response = Http::post($this->endpoint, [
                'contents' => [['parts' => [['text' => $prompt]]]]
            ]);

            if ($response->successful()) {
                return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Không thể phân tích.';
            }
            Log::error("AI Error: " . $response->body());
            return "AI đang bận, thử lại sau.";
        } catch (\Exception $e) {
            Log::error("AI Exception: " . $e->getMessage());
            return "Lỗi kết nối AI.";
        }
    }
}
