<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // DANH SÁCH EMAIL ADMIN - Cập nhật email của bạn vào đây
        $adminEmails = [
            'tranvanchien24022003@gmail.com',
            'haruki24022003@gmail.com'
        ];

        // Nếu chưa đăng nhập hoặc Email không nằm trong danh sách Admin
        if (!Auth::check() || !in_array(Auth::user()->email, $adminEmails)) {
            // Chặn lại và báo lỗi 403
            abort(403, 'Bạn không có quyền truy cập Admin Dashboard.');
        }

        return $next($request);
    }
}
