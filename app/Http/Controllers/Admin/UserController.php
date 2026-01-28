<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    // Đã xóa __construct() để tránh lỗi middleware

    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm
        if ($search = $request->input('search')) {
            $query->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
        }

        $users = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cập nhật gói
        if ($request->has('plan_type')) {
            $user->plan_type = $request->input('plan_type');
        }

        // Cập nhật ngày hết hạn
        if ($addDays = $request->input('add_days')) {
            $currentExpiry = $user->vip_expires_at ? Carbon::parse($user->vip_expires_at) : Carbon::now();
            if ($currentExpiry->isPast()) $currentExpiry = Carbon::now();

            $user->vip_expires_at = $currentExpiry->addDays((int)$addDays);
        }

        // Reset về Free (Xóa hạn)
        if ($request->input('reset_free') == '1') {
            $user->plan_type = 'free';
            $user->vip_expires_at = null;
        }

        // Cộng trừ tiền
        if ($balanceChange = $request->input('balance_change')) {
            $user->balance += (int)$balanceChange;
        }

        $user->save();

        return back()->with('success', 'Cập nhật thành công cho ' . $user->email);
    }
}
