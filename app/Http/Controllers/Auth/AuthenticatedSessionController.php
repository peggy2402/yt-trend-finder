<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // Laravel mặc định check email/pass

        $user = $request->user();

        // KIỂM TRA LOGIC VERIFY
        if (!$user->hasVerifiedEmail()) {
            // Đăng xuất ngay lập tức để không tạo session
            Auth::guard('web')->logout(); 
            
            // Kiểm tra xem OTP còn hạn không, nếu hết hạn thì tự gửi lại luôn cho tiện (Optional)
            // Hoặc chỉ đơn giản là redirect về trang nhập
            return redirect()->route('otp.verify', ['email' => $user->email])
                            ->withErrors(['email' => 'Tài khoản chưa được kích hoạt. Vui lòng nhập mã OTP.']);
        }

        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
