<?php

use Illuminate\Support\Facades\Route;

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