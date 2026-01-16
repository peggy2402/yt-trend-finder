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

// 4. Load file auth.php 
require __DIR__.'/auth.php';