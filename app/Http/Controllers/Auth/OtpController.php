<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // Import Session
use Illuminate\Support\Facades\Password; // Import Password Facade

class OtpController extends Controller
{
    // Hiển thị form nhập OTP
    public function create(Request $request)
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('register');
        }

        // Lấy mục đích từ session
        $intent = Session::get('otp_intent');

        // Chỉ redirect về login nếu user đã verify VÀ KHÔNG PHẢI đang reset password
        if ($user->hasVerifiedEmail() && $intent !== 'reset_password') {
            return redirect()->route('login');
        }

        // TÍNH TOÁN THỜI GIAN CÒN LẠI (Giây)
        $remainingSeconds = 0;
        if ($user->otp_expires_at) {
            $remainingSeconds = (int) \Carbon\Carbon::now()->diffInSeconds($user->otp_expires_at, false);
        }
        if ($remainingSeconds < 0) {
            $remainingSeconds = 0;
        }
        return view('auth.verify-email', compact('email', 'remainingSeconds'));
    }

    // Xử lý xác thực OTP
    public function store(Request $request)
    {
        $email = $request->user() ? $request->user()->email : $request->email;
        $request->merge(['email' => $email]); 
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $throttleKey = 'otp-try:'.$request->ip().':'.$request->email;
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['otp' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.']);
        }
        
        $user = User::where('email', $email)->first();

        // Logic kiểm tra OTP
        if (!$user || $user->otp_code !== $request->otp) {
            RateLimiter::hit($throttleKey, 900); 
            return back()->withErrors(['otp' => 'Mã OTP không chính xác.']);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng đăng ký lại hoặc yêu cầu mã mới.']);
        }
        RateLimiter::clear($throttleKey);
        
        // Xác thực thành công (Clear OTP)
        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;      
        $user->otp_expires_at = null;
        $user->save();

        // --- LOGIC ĐIỀU HƯỚNG MỚI (CẬP NHẬT) ---
        $intent = Session::get('otp_intent');

        // Case 1: Quên mật khẩu -> Chuyển sang form đặt mật khẩu mới
        if ($intent === 'reset_password') {
            // Tạo token reset password hợp lệ của Laravel
            $token = Password::createToken($user);
            
            // Xóa session intent để tránh lỗi lần sau
            Session::forget('otp_intent');

            // Chuyển hướng sang route 'password.reset' với token và email
            return redirect()->route('password.reset', ['token' => $token, 'email' => $email]);
        }

        // Case 2: Đang đăng nhập (Đổi email/profile)
        if (Auth::check()) {
            return redirect()->route('profile.edit')->with('status', 'profile-updated');
        } 
        
        // Case 3: Đăng ký mới (Mặc định)
        else {
            return redirect()->route('login')->with('status', 'Tài khoản đã kích hoạt! Vui lòng đăng nhập.');
        }
    }

    // Gửi lại mã OTP 
    public function resend(Request $request)
    {
        $email = $request->user() ? $request->user()->email : $request->email;
        if (!$email) {
            return back()->withErrors(['email' => 'Không tìm thấy địa chỉ email.']);
        }

        $user = User::where('email', $email)->first();

        if ($user->otp_expires_at && Carbon::now()->lt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Vui lòng đợi đồng hồ đếm ngược kết thúc trước khi gửi lại.']);
        }

        // 1. Tạo OTP mới
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addSeconds(60); 
        $user->save();

        // 2. Gửi Email
        Mail::to($user->email)->send(new SendOtpMail($user, $otp));

        return back()->with('status', 'Mã OTP mới đã được gửi vào email của bạn!');
    }
}