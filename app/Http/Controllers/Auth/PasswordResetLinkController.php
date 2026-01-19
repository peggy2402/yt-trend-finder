<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Password; 
use Illuminate\View\View;
use App\Models\User; 
use Illuminate\Support\Facades\Session; 
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Mail; // Nếu bạn dùng Mail
use App\Mail\SendOtpMail; // Nếu bạn dùng Mail class này

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // 1. Tìm User
        $user = User::where('email', $request->email)->first();

        if (!$user) {
             return back()->withErrors(['email' => __('Không tìm thấy người dùng với email này.')]);
        }

        // 2. Logic tạo OTP
        $otp = rand(100000, 999999);
        
        // Lưu OTP vào DB
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(15);
        $user->save();
        
        // Gửi OTP qua Email
        try {
             Mail::to($user->email)->send(new SendOtpMail($user, $otp));
        } catch (\Exception $e) {
             Log::error("Lỗi gửi mail: " . $e->getMessage());
        }
        
        Log::info("OTP Reset Password cho {$user->email}: {$otp}");

        // --- SỬA ĐỔI QUAN TRỌNG ---
        // Đánh dấu vào Session: Người này đang muốn Reset Password
        Session::put('otp_intent', 'reset_password');

        // 3. Chuyển hướng sang trang nhập OTP KÈM THEO EMAIL
        return redirect()->route('otp.verify', ['email' => $user->email])
                         ->with('status', 'Mã OTP xác thực đã được gửi đến email của bạn.');
    }
}