<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ViaController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Tool\TikTokBetaController;
use App\Http\Middleware\CheckSaaSPlan;

// --- QUAN TRỌNG: Import các class cần thiết ---
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Middleware\CheckAdmin;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Trang chủ
Route::get('/', function () {
    return view('welcome');
});

// 2. Trang liên hệ
Route::get('/contact', function () {
    return view('contact.contact');
});

// 3. Nhóm Route yêu cầu Đăng nhập (Bảo mật Tool)
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD & SHOP ---
    Route::get('/dashboard', function () {
        return view('shop.shop-tgv');
    })->name('dashboard');

    Route::get('/deposit', [ShopController::class, 'deposit'])->name('deposit');
    Route::get('/history', [ShopController::class, 'history'])->name('history');

    // --- CÔNG CỤ ANALYTICS ---
    Route::get('/tool/yt-trends', function () {
        return view('tool.yt-trends');
    })->name('tool.yt-trends');

    // --- TIKTOK BETA HUNTER ---
    Route::get('/tool/tiktok-beta', function () {
        return view('tool.tiktok-beta');
    })->name('tool.tiktok-beta');

    Route::get('/tool/tiktok-beta/search', [TikTokBetaController::class, 'search'])->name('tool.tiktok-beta.search');
    Route::get('/tool/tiktok-beta/ai-keywords', [TikTokBetaController::class, 'getAiKeywords'])->name('tool.tiktok-beta.ai');

    // UPGRADE PLAN
    Route::post('/tool/tiktok-beta/buy-plan', [TikTokBetaController::class, 'buyPlan'])->name('tool.tiktok-beta.buy-plan')->middleware('auth');

    // --- API MUA HÀNG ---
    Route::prefix('tool')->group(function () {
        Route::get('/profile', [ViaController::class, 'profile']);
        Route::get('/products', [ViaController::class, 'getProducts']);
        Route::post('/buy', [ViaController::class, 'buyProduct']);
    });

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// --- ADMIN ROUTES ---
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckAdmin::class])
    ->prefix('admin')
    ->group(function () {
        // Quản lý Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users');
        Route::post('/users/{id}/update', [UserController::class, 'update'])->name('admin.users.update');

        // Quản lý Cấu hình (API Key Apify)
        Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');

        // --- QUAN TRỌNG: Route này dùng cho AJAX Check Key ---
        Route::post('/settings/check', [SettingController::class, 'checkKeys'])->name('admin.settings.check');
    });


// --- SERVICE ROUTE: IMAGE PROXY (FIX LỖI BINARY DATA & CACHE) ---
Route::get('/img-proxy', function (Request $request) {
    $url = $request->input('url');

    // Ảnh placeholder 1x1 pixel trong suốt (base64)
    $fallbackImage = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

    if (!$url) {
        return response($fallbackImage)->header('Content-Type', 'image/gif');
    }

    // Decode URL nếu cần
    $url = urldecode($url);

    // Tạo cache key đơn giản
    $cacheKey = 'img_proxy_' . md5($url);

    // Đặt headers mặc định
    $headers = [
        'Content-Type' => 'image/jpeg',
        'Cache-Control' => 'public, max-age=86400',
    ];

    try {
        // Kiểm tra cache đơn giản hơn - chỉ lưu URL đã xử lý
        if (Cache::has($cacheKey)) {
            $cachedUrl = Cache::get($cacheKey);
            // Chỉ lưu URL vào cache, không lưu binary data
            $url = $cachedUrl;
        }

        // Tải ảnh trực tiếp không qua cache binary
        $response = Http::withoutVerifying()
            ->timeout(15)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://www.tiktok.com/',
                'Accept' => 'image/*',
            ])
            ->get($url);

        if ($response->successful()) {
            // Lấy content type từ response
            $contentType = $response->header('Content-Type');

            // Xử lý đặc biệt cho HEIC images
            if (str_contains($contentType, 'heic') || str_contains($url, '.heic')) {
                $headers['Content-Type'] = 'image/jpeg'; // Chuyển về JPEG để browser hỗ trợ tốt hơn
            } elseif ($contentType) {
                $headers['Content-Type'] = $contentType;
            }

            // Cache URL (không cache binary data) trong 12 giờ
            Cache::put($cacheKey, $url, 43200);

            // Trả về response trực tiếp
            return response($response->body())
                ->header('Content-Type', $headers['Content-Type'])
                ->header('Cache-Control', $headers['Cache-Control']);
        }

    } catch (\Exception $e) {
        // Log lỗi ngắn gọn, không include binary data
        Log::error('ImgProxy failed for URL (truncated): ' . substr($url, 0, 100));
    }

    // Fallback: trả về ảnh trắng 1x1 pixel
    return response($fallbackImage)
        ->header('Content-Type', 'image/gif')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');

})->name('img-proxy');

// Test Mail Route (Giữ nguyên của bạn)
Route::get('/test-mail', function () {
    try {
        Log::info('Testing mail sending...');
        \Mail::raw('Test email from production', function ($message) {
            $message->to('chien24022003@gmail.com')->subject('Test Email');
        });
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

require __DIR__.'/auth.php';
