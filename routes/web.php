<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ViaController;
// Trang chủ (Portal hệ sinh thái)
Route::get('/', function () {
    return view('welcome');
});

// Trang công cụ (YouTube Hunter)
Route::get('/tool', function () {
    return view('tool.yt-trends');
});

// Trang công cụ chính (YouTube Hunter)
Route::get('/contact', function () {
    return view('contact.contact');
});

// Các route này gọi từ JS -> Laravel -> External API
Route::prefix('api')->group(function () {
    Route::get('/profile', [ViaController::class, 'profile']);
    Route::get('/products', [ViaController::class, 'getProducts']);
    Route::post('/buy', [ViaController::class, 'buyProduct']);
});

// Trang shop online Thế Giới VIA
Route::get('/shop', function () {
    return view('shop.shop-tgv');
});