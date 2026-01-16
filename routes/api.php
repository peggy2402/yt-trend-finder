<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SePayWebhookController;
use App\Http\Controllers\Api\ViaController;

// Lấy thông tin user (Mặc định Laravel)
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// --- WEBHOOK NẠP TIỀN TỰ ĐỘNG ---
// URL này sẽ là: https://domain-cua-ban.com/api/sepay/webhook
Route::post('/sepay/webhook', [SePayWebhookController::class, 'handle']);