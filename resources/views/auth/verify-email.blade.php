<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP | ZENTRA Group</title>
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
        /* Ẩn nút tăng giảm input number */
        input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
        
        /* Hiệu ứng mờ cho nút gửi lại khi chưa kích hoạt */
        .disabled-link { pointer-events: none; opacity: 0.5; }
    </style>
</head>
<body class="bg-[#050505] text-white min-h-screen flex items-center justify-center relative overflow-hidden">

    <div class="fixed inset-0 pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-600/10 rounded-full blur-[150px]"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-overlay"></div>
    </div>

    <div class="relative z-10 w-full max-w-md px-6">
        <div class="glass-panel p-8 rounded-3xl text-center">
            
            <div class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6 text-red-500 text-2xl border border-red-500/20">
                <i class="fa-solid fa-shield-cat"></i>
            </div>

            <h2 class="text-2xl font-bold mb-2">Xác thực bảo mật</h2>
            <p class="text-slate-400 text-sm mb-6 px-4">
                Mã OTP đã gửi đến: <span class="text-white font-medium">{{ request('email') }}</span>
            </p>

            @if ($errors->any())
                <div class="mb-6 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 p-3 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.store') }}">
                @csrf
                <input type="hidden" name="email" value="{{ Auth::user() ? Auth::user()->email : request('email') }}">

                <div class="mb-6">
                    <input type="text" name="otp" maxlength="6" autofocus required
                        class="w-full bg-[#0a0a0c] border border-slate-700 rounded-xl py-4 text-center text-3xl tracking-[10px] font-bold text-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 outline-none transition-all placeholder-slate-700"
                        placeholder="000000">
                </div>

                <button type="submit" class="w-full bg-white text-black font-bold py-3.5 rounded-xl hover:bg-slate-200 transition-colors mb-6 shadow-lg shadow-white/10">
                    Xác nhận OTP
                </button>
            </form>

            <div class="text-sm text-slate-500">
                <span id="countdown-wrapper">
                    Mã hết hạn sau <span id="countdown" class="text-red-500 font-bold">60</span> giây.
                </span>

                <form id="resend-form" method="POST" action="{{ route('otp.resend') }}" class="hidden mt-2">
                    @csrf
                    <input type="hidden" name="email" value="{{ Auth::user() ? Auth::user()->email : request('email') }}">
                    <p class="mb-2">Bạn chưa nhận được mã?</p>
                    <button type="submit" class="text-white underline decoration-red-500 underline-offset-4 hover:text-red-500 transition-colors font-semibold">
                        Gửi lại mã mới
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Lấy thời gian còn lại thực tế từ Server
        let timeLeft = {{ $remainingSeconds }}; 
        
        const countdownEl = document.getElementById('countdown');
        const countdownWrapper = document.getElementById('countdown-wrapper');
        const resendForm = document.getElementById('resend-form');

        // Hàm update UI
        function updateUI() {
            if (timeLeft <= 0) {
                // Hết giờ: Ẩn đếm ngược, hiện nút gửi lại
                countdownWrapper.classList.add('hidden');
                resendForm.classList.remove('hidden');
            } else {
                // Còn giờ: Hiện đếm ngược, ẩn nút gửi lại
                countdownWrapper.classList.remove('hidden');
                resendForm.classList.add('hidden');
                countdownEl.innerText = timeLeft;
            }
        }

        // Chạy ngay lần đầu để set trạng thái đúng (trường hợp load lại trang mà đã hết hạn)
        updateUI();

        const timer = setInterval(() => {
            if (timeLeft <= 0) {
                clearInterval(timer);
                updateUI();
            } else {
                timeLeft -= 1;
                countdownEl.innerText = timeLeft;
            }
        }, 1000);
    </script>
</body>
</html>