<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class ShopController extends Controller
{
    public function history()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('transactions.history', compact('orders'));
    }

    public function deposit()
    {
        return view('transactions.deposit');
    }
}
