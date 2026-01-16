<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SePayWebhookController;

// Lấy thông tin user (Mặc định của Laravel)
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// --- WEBHOOK SEPAY (PUBLIC ROUTE) ---
// Không dùng middleware auth vì SePay gọi từ server ngoài
Route::post('/sepay/webhook', [SePayWebhookController::class, 'handle']);