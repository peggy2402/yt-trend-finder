<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        // --- BỔ SUNG LOGIC TÍNH TOÁN COUNTDOWN ---
        $remainingSeconds = 0;
        if ($request->user()->otp_expires_at) {
            $remainingSeconds = (int) Carbon::now()->diffInSeconds($request->user()->otp_expires_at, false);
        }
        
        if ($remainingSeconds < 0) {
            $remainingSeconds = 0;
        }

        // Truyền biến remainingSeconds sang View
        return view('auth.verify-email', compact('remainingSeconds'));
    }
}
