<?php

namespace App\Mail;

use App\Models\User; // Thêm dòng này để dùng model User
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // Khai báo biến user (public để view truy cập được)
    public $otp;

    // Sửa hàm construct để nhận thêm $user
    public function __construct(User $user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function build()
    {
        // Đảm bảo tên view đúng với vị trí file verify.blade.php của bạn
        // Nếu file nằm ở resources/views/emails/verify.blade.php -> dùng 'emails.verify'
        return $this->subject('Mã xác thực OTP - ZENTRA Group')
                    ->view('emails.verify'); 
    }
}