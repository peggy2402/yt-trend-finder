<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email | ZENTRA Group</title>
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
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-4">
        <div class="glass-panel p-8 rounded-2xl shadow-2xl text-center">
            
            <div class="w-20 h-20 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-green-500 text-3xl animate-bounce">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>

            <h2 class="text-2xl font-bold mb-4">Kiểm tra email của bạn</h2>
            
            <div class="text-slate-400 text-sm mb-6 leading-relaxed">
                Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực tài khoản bằng cách nhấn vào liên kết chúng tôi vừa gửi đến email của bạn.
                <br><br>
                <span class="text-slate-500 text-xs italic">(Nếu không thấy email, hãy kiểm tra mục Spam nhé)</span>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 font-medium text-sm text-green-400 bg-green-400/10 p-3 rounded-lg border border-green-400/20">
                    Một liên kết xác thực mới đã được gửi đến địa chỉ email của bạn.
                </div>
            @endif

            <div class="flex flex-col gap-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all">
                        Gửi lại email xác thực
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-transparent hover:bg-white/5 text-slate-400 font-medium py-3 rounded-xl border border-slate-700 transition-all">
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>