<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Hiển thị trang đăng ký.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Xử lý khi bấm nút Đăng ký.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Tạo User vào Database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'balance' => 0, // Mặc định số dư là 0
        ]);

        // 2. Gửi Email xác thực (Laravel tự làm việc này nhờ dòng implements MustVerifyEmail ở Model)
        event(new Registered($user));

        // 3. Tự động đăng nhập
        Auth::login($user);

        // 4. Chuyển hướng
        // Ở đây ta chuyển hướng về 'dashboard'
        // NHƯNG vì dashboard có middleware 'verified' -> User sẽ bị đá sang trang Verify Email
        return redirect(route('dashboard', absolute: false));
    }
}