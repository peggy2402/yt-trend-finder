<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu | ZENTRA Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="bg-[#0a0a0c] text-white min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Background Effects -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-[20%] left-[20%] w-[40vw] h-[40vw] bg-red-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 group-hover:scale-110 transition-transform">
                <span class="text-2xl font-extrabold tracking-tight">ZENTRA<span class="text-red-500"> GROUP</span></span>
            </a>
        </div>

        <div class="glass-panel p-8 rounded-2xl shadow-2xl">
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4 text-red-500 text-2xl">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h2 class="text-xl font-bold">Khôi phục mật khẩu</h2>
                <p class="text-slate-400 text-sm mt-2">
                    Đừng lo lắng! Nhập email của bạn và chúng tôi sẽ gửi link đặt lại mật khẩu ngay lập tức.
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-400 bg-green-400/10 p-3 rounded-lg border border-green-400/20 flex items-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email đăng ký</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-regular fa-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white focus:border-red-500 focus:ring-1 focus:ring-red-500 focus:outline-none transition-all"
                            placeholder="name@example.com">
                    </div>
                </div>

                <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-900/20 transition-all transform hover:-translate-y-0.5">
                    Gửi link khôi phục <i class="fa-regular fa-paper-plane ml-2"></i>
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
</body>
</html>