<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZENTRA Group | Ecosystem</title>
    
    <!-- 1. Fix Font: Load đúng font Outfit cho giao diện hiện đại -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700&display=swap&subset=vietnamese" rel="stylesheet">
    <!-- 2. Fix Icon: Update FontAwesome 6.5.1 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/logo32.png') }}">
</head>
<body class="bg-[#0a0a0c] text-white font-outfit overflow-x-hidden selection:bg-red-500 selection:text-white">

    <!-- Background Effects -->
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] bg-red-600/10 rounded-full blur-[120px] animate-pulse-slow"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40vw] h-[40vw] bg-blue-600/10 rounded-full blur-[120px] animate-pulse-slow delay-1000"></div>
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-50 w-full px-6 py-6 flex justify-between items-center max-w-7xl mx-auto">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10">
            </a>
            <span class="text-2xl font-extrabold tracking-tight">ZENTRA<span class="text-red-500"> GROUP</span></span>
        </div>
        <div class="hidden md:flex gap-8 text-sm font-medium text-slate-400">
            <a href="#" class="hover:text-white transition-colors">Trang chủ</a>
            <a href="#ecosystem" class="hover:text-white transition-colors">Hệ sinh thái</a>
            <a href="#" class="hover:text-white transition-colors">Về chúng tôi</a>
        </div>
        <a href="{{ url('/contact') }}" class="hidden md:inline-flex px-5 py-2 rounded-full border border-slate-700 hover:border-red-500 hover:bg-red-500/10 transition-all text-sm font-bold">
            Liên hệ
        </a>
    </nav>

    <!-- Hero Section -->
    <section class="relative z-10 pt-20 pb-32 text-center px-4">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block py-1 px-3 rounded-full bg-slate-800/50 border border-slate-700 text-xs font-bold text-red-400 mb-6 animate-fade-in-up">
                ✨ PHIÊN BẢN V3.9 ĐÃ RA MẮT
            </span>
            <h1 class="text-5xl md:text-7xl font-semibold tracking-tight mb-6 leading-tight animate-fade-in-up delay-100">
                Làm chủ dữ liệu <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500">
                    Thống trị YouTube
                </span>
            </h1>
            <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200">
                Nền tảng phân tích thị trường chuyên sâu, giúp Creator và Marketer tìm ra "long mạch" nội dung và tối ưu hóa doanh thu.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up delay-300">
                <a href="#ecosystem" class="px-8 py-4 bg-red-600 hover:bg-red-700 rounded-xl font-bold shadow-xl shadow-red-900/30 transition-all hover:-translate-y-1 flex items-center justify-center gap-2">
                    Khám phá Tool
                    <i class="fa-solid fa-arrow-down"></i>
                </a>
                <a href="#" class="px-8 py-4 bg-slate-800 hover:bg-slate-700 rounded-xl font-bold transition-all hover:-translate-y-1">
                    Tìm hiểu thêm
                </a>
            </div>
        </div>
    </section>

    <!-- Tools Ecosystem Grid -->
    <section id="ecosystem" class="relative z-10 py-20 px-4 max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold mb-2">Hệ sinh thái ZENTRA</h2>
                <p class="text-slate-400">Các công cụ hỗ trợ xây dựng đế chế nội dung.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Tool 1: Market Hunter (Active) -->
            <!-- Link đến trang công cụ /tool -->
            <a href="{{ url('/tool') }}" class="group relative bg-[#131316] border border-slate-800 rounded-2xl p-1 overflow-hidden hover:border-red-500/50 transition-all duration-300 block">
                <div class="absolute inset-0 bg-gradient-to-b from-red-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                
                <div class="bg-[#18181b] rounded-xl p-6 h-full relative z-10 flex flex-col">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-red-500/20 text-red-500 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            <!-- Thay icon radar (không có free) bằng crosshairs (ống ngắm hunter) -->
                            <i class="fa-solid fa-crosshairs"></i>
                        </div>
                        <span class="px-2 py-1 bg-green-500/20 text-green-400 text-[10px] font-bold rounded uppercase border border-green-500/20">Active</span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2 group-hover:text-red-400 transition-colors">YT Trends Pro</h3>
                    <p class="text-slate-400 text-sm mb-6 flex-grow">
                        Phân tích xu hướng, soi đối thủ, tìm ngách tiềm năng (Micro-Niche) và tính toán doanh thu RPM.
                    </p>
                    
                    <div class="w-full py-3 rounded-lg border border-slate-700 group-hover:bg-red-600 group-hover:border-red-600 group-hover:text-white text-slate-300 font-bold text-center transition-all flex items-center justify-center gap-2">
                        Truy cập ngay <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </a>

            <!-- Tool 2: Coming Soon -->
            <div class="group relative bg-[#131316] border border-slate-800 rounded-2xl p-1 overflow-hidden opacity-60 hover:opacity-100 transition-all">
                <div class="bg-[#18181b] rounded-xl p-6 h-full relative z-10 flex flex-col">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-xl flex items-center justify-center text-2xl">
                            <i class="fa-brands fa-youtube"></i>
                        </div>
                        <span class="px-2 py-1 bg-slate-700 text-slate-400 text-[10px] font-bold rounded uppercase">Coming Soon</span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2">Channel Auditor</h3>
                    <p class="text-slate-400 text-sm mb-6 flex-grow">
                        Kiểm tra sức khỏe kênh, phân tích SEO video và đề xuất tối ưu hóa metadata tự động.
                    </p>
                    
                    <div class="w-full py-3 rounded-lg border border-slate-800 bg-slate-800/50 text-slate-500 font-bold text-center cursor-not-allowed">
                        Đang phát triển...
                    </div>
                </div>
            </div>

            <!-- Tool 3: Coming Soon -->
            <div class="group relative bg-[#131316] border border-slate-800 rounded-2xl p-1 overflow-hidden opacity-60 hover:opacity-100 transition-all">
                <div class="bg-[#18181b] rounded-xl p-6 h-full relative z-10 flex flex-col">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 bg-purple-500/10 text-purple-500 rounded-xl flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <span class="px-2 py-1 bg-slate-700 text-slate-400 text-[10px] font-bold rounded uppercase">Coming Soon</span>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-2">AI Title Generator</h3>
                    <p class="text-slate-400 text-sm mb-6 flex-grow">
                        Sử dụng AI để tạo tiêu đề video "clickbait" hiệu quả cao dựa trên dữ liệu lịch sử.
                    </p>
                    
                    <div class="w-full py-3 rounded-lg border border-slate-800 bg-slate-800/50 text-slate-500 font-bold text-center cursor-not-allowed">
                        Đang phát triển...
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800 mt-20 bg-[#0a0a0c]">
        <div class="max-w-7xl mx-auto px-4 py-12 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <a href="{{url('/')}}">
                    <center><img src="{{asset('images/logo.png')}}" alt="Logo" class="w-10 h-10"></center>
                    <span class="font-bold text-slate-300">ZENTRA Group</span>
                </a>
            </div>
            <div class="text-slate-500 text-sm">
                &copy; {{ date('Y') }} ZENTRA Analytics. All rights reserved.
            </div>
            <div class="flex gap-4">
                <a href="https://www.facebook.com/vtchn/" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-white hover:text-black transition-all">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="https://t.me/peggyval" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-white hover:text-black transition-all">
                    <i class="fa-brands fa-telegram"></i>
                </a>
            </div>
        </div>
    </footer>

</body>
</html>