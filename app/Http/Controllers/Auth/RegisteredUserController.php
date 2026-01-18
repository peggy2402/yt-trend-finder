<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\SendOtpMail; // Import Mail Class
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validate cơ bản
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Bỏ 'unique:users' ở đây để xử lý thủ công bên dưới
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'], 
        ]);

        // Kiểm tra user tồn tại
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            // CASE 1: Đã verify rồi -> Báo lỗi hoặc chuyển sang login
            if ($existingUser->hasVerifiedEmail()) {
                return back()->withErrors(['email' => 'Email này đã được đăng ký và kích hoạt. Vui lòng đăng nhập.']);
            }

            // CASE 2: Chưa verify -> Coi như đăng ký lại (Cập nhật pass mới và gửi lại OTP)
            $existingUser->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
            ]);
            
            $this->sendOtp($existingUser); // Gửi OTP mới -> Mã cũ vô hiệu hóa
            
            return redirect()->route('otp.verify', ['email' => $existingUser->email]);
        }

        // CASE 3: User mới tinh
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'balance' => 0,
        ]);

        $this->sendOtp($user);

        return redirect()->route('otp.verify', ['email' => $user->email]);
    }
    
    // Hàm tách riêng để tái sử dụng (ví dụ nút gửi lại mã)
    protected function sendOtp($user) {
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = Carbon::now()->addSeconds(60); // Hết hạn sau 60s
        $user->save();

        // Gửi Mail
        Mail::to($user->email)->send(new SendOtpMail($user, $otp));
    }
}