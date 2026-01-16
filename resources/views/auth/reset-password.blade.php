<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu | ZENTRA Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-[#0a0a0c] text-white min-h-screen flex items-center justify-center relative overflow-hidden">
    
    <!-- Background -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute bottom-0 right-0 w-[50vw] h-[50vw] bg-blue-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="glass-panel p-8 rounded-2xl shadow-2xl">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold mb-2">Mật khẩu mới</h2>
                <p class="text-slate-400 text-sm">Thiết lập mật khẩu mới cho tài khoản của bạn.</p>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-4 text-sm text-red-400 bg-red-400/10 p-3 rounded-lg border border-red-400/20">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white focus:border-red-500 focus:outline-none transition-all" readonly>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Nhập lại mật khẩu</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-shield-halved"></i></span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none transition-all" placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full bg-white text-black hover:bg-slate-200 font-bold py-3.5 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5">
                    Cập nhật mật khẩu <i class="fa-solid fa-check ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>