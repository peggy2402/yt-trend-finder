<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
// [QUAN TRỌNG] Phải có dòng này mới dùng được URL::forceScheme
use Illuminate\Support\Facades\URL; 

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
        // Ép buộc dùng HTTPS trên môi trường Production (Render)
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Custom Email Verify
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Kích hoạt tài khoản | ZENTRA Group')
                ->view('emails.verify', ['url' => $url, 'user' => $notifiable]);
        });
    }
}