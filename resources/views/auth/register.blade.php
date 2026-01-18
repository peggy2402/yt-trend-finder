<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | ZENTRA Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-panel {
            background: rgba(20, 20, 23, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-group:focus-within i { color: #ef4444; }
        .input-group:focus-within input { border-color: #ef4444; box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1); }
    </style>
</head>
<body class="bg-[#050505] text-white min-h-screen flex items-center justify-center relative overflow-hidden">

    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-red-600/20 rounded-full blur-[128px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-600/10 rounded-full blur-[128px]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay"></div>
    </div>

    <div class="relative z-10 w-full max-w-[420px] px-6">
        
        <div class="text-center mb-10">
            <a href="/" class="inline-block mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="ZENTRA" class="w-16 h-16 mx-auto drop-shadow-2xl">
            </a>
            <h1 class="text-3xl font-bold tracking-tight">Tạo tài khoản mới</h1>
            <p class="text-slate-500 mt-2 text-sm">Tham gia hệ sinh thái ZENTRA Group ngay hôm nay.</p>
        </div>

        <div class="glass-panel p-8 rounded-3xl">
            
            @if ($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex gap-3 items-start">
                    <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                    <div class="text-sm text-red-400">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 ml-1">HỌ VÀ TÊN</label>
                    <div class="relative input-group transition-all duration-300">
                        <span class="absolute left-4 top-3.5 text-slate-600 transition-colors duration-300"><i class="fa-regular fa-user"></i></span>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full bg-[#0a0a0c] border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white placeholder-slate-700 outline-none transition-all duration-300"
                            placeholder="Nhập tên hiển thị">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 ml-1">EMAIL</label>
                    <div class="relative input-group transition-all duration-300">
                        <span class="absolute left-4 top-3.5 text-slate-600 transition-colors duration-300"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-[#0a0a0c] border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white placeholder-slate-700 outline-none transition-all duration-300"
                            placeholder="email@domain.com">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 ml-1">MẬT KHẨU</label>
                    <div class="relative input-group transition-all duration-300">
                        <span class="absolute left-4 top-3.5 text-slate-600 transition-colors duration-300"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required
                            class="w-full bg-[#0a0a0c] border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white placeholder-slate-700 outline-none transition-all duration-300"
                            placeholder="Tối thiểu 8 ký tự">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-slate-400 ml-1">XÁC NHẬN MẬT KHẨU</label>
                    <div class="relative input-group transition-all duration-300">
                        <span class="absolute left-4 top-3.5 text-slate-600 transition-colors duration-300"><i class="fa-solid fa-shield-halved"></i></span>
                        <input type="password" name="password_confirmation" required
                            class="w-full bg-[#0a0a0c] border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white placeholder-slate-700 outline-none transition-all duration-300"
                            placeholder="Nhập lại mật khẩu">
                    </div>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-red-900/20 transition-all transform hover:-translate-y-1 mt-2">
                    Đăng Ký Tài Khoản
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-slate-400">
                Đã có tài khoản? <a href="{{ route('login') }}" class="text-white font-semibold hover:text-red-500 transition-colors">Đăng nhập ngay</a>
            </div>
        </div>
    </div>
</body>
</html>