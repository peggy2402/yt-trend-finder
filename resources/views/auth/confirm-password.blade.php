<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận bảo mật | ZENTRA Group</title>
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
    
    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-[30%] left-[50%] w-[30vw] h-[30vw] bg-orange-600/10 rounded-full blur-[100px]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="glass-panel p-8 rounded-2xl shadow-2xl border-t-4 border-t-orange-500">
            <div class="text-center mb-6">
                <div class="w-14 h-14 bg-orange-500/10 rounded-full flex items-center justify-center mx-auto mb-4 text-orange-500 text-xl">
                    <i class="fa-solid fa-shield-cat"></i>
                </div>
                <h2 class="text-xl font-bold">Khu vực bảo mật</h2>
                <p class="text-slate-400 text-sm mt-2">
                    Đây là khu vực an toàn. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-4 text-sm text-red-400 bg-red-400/10 p-3 rounded-lg border border-red-400/20">
                    Sai mật khẩu. Vui lòng thử lại.
                </div>
            @endif

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-100 mb-2">Mật khẩu hiện tại</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-500"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required autocomplete="current-password" autofocus
                            class="w-full bg-[#131316] border border-slate-700 rounded-xl py-3 pl-10 pr-4 text-white focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-orange-900/20 transition-all">
                    Xác nhận <i class="fa-solid fa-arrow-right ml-2"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>