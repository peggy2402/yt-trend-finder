<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | ZENTRA Group</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Libraries -->
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
<body class="bg-[#0a0a0c] text-white min-h-screen flex items-center justify-center relative overflow-hidden selection:bg-red-500 selection:text-white py-10">

    <!-- Background Effects -->
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-[10%] right-[10%] w-[50vw] h-[50vw] bg-purple-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[10%] left-[10%] w-[50vw] h-[50vw] bg-red-600/10 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <!-- Login Container -->
    <div class="relative z-10 w-full max-w-md px-4">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-12 h-12 group-hover:scale-110 transition-transform duration-500">
                <span class="text-3xl font-extrabold tracking-tight">ZENTRA<span class="text-red-500"> GROUP</span></span>
            </a>
            <p class="text-slate-400 mt-2 text-sm">Tạo tài khoản để truy cập hệ sinh thái.</p>
        </div>

        <!-- Form Card -->
        <div class="glass-panel p-8 rounded-2xl shadow-2xl shadow-black/50">

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                    <div class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Vui lòng kiểm tra lại:</div>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Họ và Tên</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-regular fa-user"></i></span>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                            placeholder="Nguyễn Văn A">
                    </div>
                </div>

                <!-- Email Address -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-regular fa-envelope"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                            placeholder="name@example.com">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Nhập lại mật khẩu</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-shield-halved"></i></span>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white placeholder-slate-600 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-white text-black hover:bg-slate-200 font-bold py-3.5 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 active:scale-95">
                    Đăng ký tài khoản <i class="fa-solid fa-user-plus ml-2"></i>
                </button>
            </form>

            <!-- Divider -->
            <div class="mt-8 relative flex items-center justify-center">
                <div class="absolute inset-x-0 top-1/2 h-px bg-slate-800"></div>
                <span class="relative z-10 bg-[#141417] px-4 text-xs text-slate-500 uppercase tracking-widest font-bold">hoặc</span>
            </div>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-slate-400 text-sm">
                    Đã có tài khoản? 
                    <a href="{{ route('login') }}" class="text-red-500 font-bold hover:text-red-400 transition-colors">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>