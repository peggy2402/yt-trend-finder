<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ ZENTRA Group</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS (Reusing welcome.css for consistency) -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo32.png') }}">
</head>
<body class="bg-[#0a0a0c] text-white font-outfit overflow-x-hidden selection:bg-red-500 selection:text-white">

    <!-- Background Effects -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] right-[-10%] w-[40vw] h-[40vw] bg-blue-600/10 rounded-full blur-[120px] animate-pulse-slow"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[40vw] h-[40vw] bg-red-600/10 rounded-full blur-[120px] animate-pulse-slow delay-1000"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-50 w-full px-6 py-6 flex justify-between items-center max-w-7xl mx-auto">
        <a href="{{ url('/') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10">
            <span class="text-2xl font-extrabold tracking-tight">ZENTRA<span class="text-red-500"> GROUP</span></span>
        </a>
        <div class="hidden md:flex gap-8 text-sm font-medium text-slate-400">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Trang chủ</a>
            <a href="{{ url('/') }}#ecosystem" class="hover:text-white transition-colors">Hệ sinh thái</a>
        </div>
        <a href="{{ url('/tool') }}" class="hidden md:inline-flex px-5 py-2 rounded-full bg-slate-800 hover:bg-red-600 transition-all text-sm font-bold border border-slate-700 hover:border-red-500">
            Vào Tool
        </a>
    </nav>

    <!-- Main Content -->
    <main class="relative z-10 py-16 px-4 max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="text-center mb-16 animate-fade-in-up">
            <span class="text-red-500 font-bold tracking-widest text-sm uppercase mb-2 block">Kết nối với chúng tôi</span>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Liên hệ hỗ trợ</h1>
            <p class="text-slate-400 max-w-xl mx-auto">
                Bạn có câu hỏi về công cụ, cần hợp tác hoặc báo lỗi? <br>
                Đội ngũ ZENTRA luôn sẵn sàng lắng nghe.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            <!-- Left Column: Info -->
            <div class="space-y-8 animate-fade-in-up delay-100">
                <!-- Info Card -->
                <div class="bg-[#131316] border border-slate-800 p-8 rounded-2xl relative overflow-hidden group hover:border-slate-700 transition-colors">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <i class="fa-solid fa-location-dot text-6xl text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-circle-info text-red-500"></i> Thông tin liên hệ
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-red-400 shrink-0">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-300">Email Hỗ Trợ</h4>
                                <a href="mailto:support@zentra.group" class="text-slate-400 hover:text-white transition-colors">support@zentra.group</a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-blue-400 shrink-0">
                                <i class="fa-brands fa-telegram"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-300">Telegram Support</h4>
                                <a href="" class="text-slate-400 hover:text-white transition-colors">@peggyval</a>
                                <p class="text-xs text-slate-600 mt-1">Phản hồi nhanh 24/7</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-green-400 shrink-0">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-slate-300">Trụ sở chính</h4>
                                <p class="text-slate-400">Tầng 12, Tòa nhà ZENTRA GROUP, Quận Hoàng Mai, Hà Nội</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Link -->
                <div class="p-6 rounded-2xl bg-gradient-to-r from-red-900/20 to-transparent border border-red-900/30 flex items-center justify-between group cursor-pointer hover:border-red-500/50 transition-all">
                    <div>
                        <h4 class="font-bold text-red-400 mb-1">Câu hỏi thường gặp (FAQ)</h4>
                        <p class="text-xs text-slate-400">Tìm câu trả lời nhanh cho vấn đề của bạn</p>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-red-600/20 flex items-center justify-center text-red-500 group-hover:bg-red-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </div>

            <!-- Right Column: Form -->
            <div class="bg-[#18181b] border border-slate-800 p-8 rounded-3xl shadow-2xl relative animate-fade-in-up delay-200">
                <h3 class="text-xl font-bold mb-6">Gửi tin nhắn</h3>
                <form action="#" method="POST" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase ml-1">Họ tên</label>
                            <input type="text" placeholder="Nhập tên của bạn" class="w-full bg-[#0f0f11] border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder:text-slate-600">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase ml-1">Email</label>
                            <input type="email" placeholder="example@gmail.com" class="w-full bg-[#0f0f11] border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder:text-slate-600">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase ml-1">Chủ đề</label>
                        <select class="w-full bg-[#0f0f11] border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all text-slate-300">
                            <option>Hỗ trợ kỹ thuật (Lỗi Tool, API...)</option>
                            <option>Hợp tác kinh doanh</option>
                            <option>Góp ý tính năng</option>
                            <option>Khác</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400 uppercase ml-1">Nội dung</label>
                        <textarea rows="4" placeholder="Mô tả chi tiết vấn đề của bạn..." class="w-full bg-[#0f0f11] border border-slate-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all placeholder:text-slate-600 resize-none"></textarea>
                    </div>

                    <button type="button" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-red-900/30 flex items-center justify-center gap-2 group">
                        <span>Gửi Tin Nhắn</span>
                        <i class="fa-solid fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    </button>
                    
                    <p class="text-center text-xs text-slate-500 mt-4">
                        Bằng việc gửi tin nhắn, bạn đồng ý với <a href="#" class="text-slate-400 hover:text-white underline">Điều khoản sử dụng</a> của chúng tôi.
                    </p>
                </form>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800 mt-auto bg-[#0a0a0c]">
        <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} ZENTRA Analytics.
            </div>
            <div class="flex gap-6 text-sm font-medium text-slate-400">
                <a href="#" class="hover:text-white transition-colors">Facebook</a>
                <a href="#" class="hover:text-white transition-colors">Telegram</a>
                <a href="#" class="hover:text-white transition-colors">Youtube</a>
            </div>
        </div>
    </footer>

</body>
</html>