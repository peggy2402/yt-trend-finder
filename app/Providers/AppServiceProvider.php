<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// 1. Import 2 thư viện này
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. Thêm đoạn code Custom Email này vào hàm boot()
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Kích hoạt tài khoản | ZENTRA Group') // Tiêu đề email
                ->view('emails.verify', ['url' => $url, 'user' => $notifiable]); // Trỏ đến file view vừa tạo
        });
    }
}