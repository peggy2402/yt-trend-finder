<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ViaController;
use App\Http\Controllers\Api\ShopController;

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
Route::get('/tool', function () {
    return view('tool.yt-trends');
});
// 3. Nhóm Route yêu cầu Đăng nhập & Xác thực Email
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Vào thẳng Shop khi đăng nhập xong
    Route::get('/dashboard', function () {
        return view('shop.shop-tgv'); 
    })->name('dashboard');

    // --- CÁC TRANG SHOP MỚI (Nạp tiền, Lịch sử) ---
    Route::get('/deposit', [ShopController::class, 'deposit'])->name('deposit');
    Route::get('/history', [ShopController::class, 'history'])->name('history');

    // API xử lý mua hàng
    Route::prefix('tool')->group(function () {
        Route::get('/profile', [ViaController::class, 'profile']);      
        Route::get('/products', [ViaController::class, 'getProducts']); 
        Route::post('/buy', [ViaController::class, 'buyProduct']);      
    });

    // Quản lý Profile (Mặc định của Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        return "Connected successfully to database";
    } catch (\Exception $e) {
        return "Could not connect to the database. Error: " . $e->getMessage();
    }
});
// routes/web.php
Route::get('/test-mail', function () {
    try {
        \Log::info('Testing mail sending...');
        
        // Test 1: Check config
        dump('Mail Driver: ' . config('mail.default'));
        dump('Mail Host: ' . config('mail.mailers.smtp.host'));
        dump('Mail Username: ' . config('mail.mailers.smtp.username'));
        dump('Mail Password: ' . config('mail.mailers.smtp.password'));
        dump('Mail Port: ' . config('mail.mailers.smtp.port'));

        // Test 2: Send simple email
        \Mail::raw('Test email from production', function ($message) {
            $message->to('chien24022003@gmail.com')
                    ->subject('Test Email from Production');
        });
        
        \Log::info('Mail sent successfully');
        return 'Email sent successfully!';
        
    } catch (\Exception $e) {
        \Log::error('Mail Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return 'Error: ' . $e->getMessage() . '<br><br>' . $e->getTraceAsString();
    }
});
Route::get('/test-network', function() {
    $tests = [
        ['host' => 'smtp.gmail.com', 'port' => 587],
        ['host' => 'smtp.gmail.com', 'port' => 465],
        ['host' => 'smtp.sendgrid.net', 'port' => 587],
        ['host' => 'httpbin.org', 'port' => 80],
        ['host' => 'google.com', 'port' => 443],
    ];
    
    $results = [];
    foreach ($tests as $test) {
        $timeout = 5;
        $fp = @fsockopen($test['host'], $test['port'], $errno, $errstr, $timeout);
        
        if ($fp) {
            $results[] = "✓ {$test['host']}:{$test['port']} - Connected";
            fclose($fp);
        } else {
            $results[] = "✗ {$test['host']}:{$test['port']} - Failed: {$errstr}";
        }
    }
    
    return implode("<br>", $results);
});
// 4. Load file auth.php 
require __DIR__.'/auth.php';