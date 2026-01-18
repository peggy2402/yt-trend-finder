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
class OtpController extends Controller
{
    // Hiển thị form nhập OTP
    public function create(Request $request)
    {
        $email = $request->query('email');
        $user = User::where('email', $email)->first();
        // Nếu không tìm thấy user hoặc user đã verify rồi -> đá về trang phù hợp
        if (!$user) {
            return redirect()->route('register');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login');
        }

        // TÍNH TOÁN THỜI GIAN CÒN LẠI (Giây)
        // Nếu expires_at null hoặc đã qua giờ hiện tại thì còn lại 0 giây
        $remainingSeconds = 0;
        if ($user->otp_expires_at) {
            $remainingSeconds = (int) \Carbon\Carbon::now()->diffInSeconds($user->otp_expires_at, false);
        }
        // Nếu số âm (đã hết hạn), gán về 0
        if ($remainingSeconds < 0) {
            $remainingSeconds = 0;
        }
        return view('auth.verify-email', compact('email', 'remainingSeconds'));
    }

    // Xử lý xác thực OTP
    public function store(Request $request)
    {
        // 1. Nếu User đang đăng nhập, lấy email của họ luôn (tránh hacker sửa input hidden)
        $email = $request->user() ? $request->user()->email : $request->email;
        $request->merge(['email' => $email]); // Gộp lại vào request để validate
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric|digits:6',
        ]);

        // RATE LIMITING (Sử dụng cache để đếm số lần sai theo IP và Email)
        $throttleKey = 'otp-try:'.$request->ip().':'.$request->email;
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()->withErrors(['otp' => 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 15 phút.']);
        }
        
        $user = User::where('email', $email)->first();

        // Logic kiểm tra OTP
        if (!$user || $user->otp_code !== $request->otp) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 900); // Ghi nhận lỗi
            return back()->withErrors(['otp' => 'Mã OTP không chính xác.']);
        }

        // Kiểm tra thời gian hết hạn
        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng đăng ký lại hoặc yêu cầu mã mới.']);
        }
        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
        // Xác thực thành công
        $user->email_verified_at = Carbon::now();
        $user->otp_code = null;      // Xóa OTP sau khi dùng
        $user->otp_expires_at = null;
        $user->save();

        // LOGIC ĐIỀU HƯỚNG MỚI
        if (Auth::check()) {
            // Case 1: Đổi Profile -> Quay lại trang Profile kèm thông báo
            return redirect()->route('profile.edit')->with('status', 'profile-updated');
        } else {
            // Case 2: Đăng ký mới -> Chuyển sang Login
            return redirect()->route('login')->with('status', 'Tài khoản đã kích hoạt! Vui lòng đăng nhập.');
        }
    }

    // Gửi lại mã OTP 
    public function resend(Request $request)
    {
        $email = $request->user() ? $request->user()->email : $request->email;
        // Validate thủ công vì ta đã thay đổi nguồn email
        if (!$email) {
            return back()->withErrors(['email' => 'Không tìm thấy địa chỉ email.']);
        }

        // $request->validate([
        //     'email' => 'required|email|exists:users,email',
        // ]);

        $user = User::where('email', $email)->first();

        // Kiểm tra nếu người dùng spam nút gửi lại (ví dụ: bắt đợi 60s mới được gửi lại lần nữa)
        // Logic này tùy chọn, nhưng nên có để tránh spam mail
        if ($user->otp_expires_at && Carbon::now()->lt($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Vui lòng đợi đồng hồ đếm ngược kết thúc trước khi gửi lại.']);
        }

        // 1. Tạo OTP mới
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addSeconds(60); // Gia hạn thêm 60s
        $user->save();

        // 2. Gửi Email
        Mail::to($user->email)->send(new SendOtpMail($user, $otp));

        // 3. Trả về thông báo thành công
        return back()->with('status', 'Mã OTP mới đã được gửi vào email của bạn!');
    }
}