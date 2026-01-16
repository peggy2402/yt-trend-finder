<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cửa Hàng Nguyên Liệu | ZENTRA Group</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="shortcut icon" href="{{asset('images/logo.png')}}" type="image/x-icon">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="bg-[#f3f4f6] text-slate-800">

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        
        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-5 rounded-2xl shadow-sm border border-slate-200 gap-4">
            
            <!-- Logo & Title -->
            <div class="flex items-center gap-4 w-full md:w-auto">
                <a href="/" class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-all">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-800 tracking-tight">SHOP <span class="text-red-600">ZENTRA</span></h1>
                    <p class="text-xs text-slate-500 font-medium">Hệ thống phân phối tài nguyên tự động</p>
                </div>
            </div>

            <!-- User Actions -->
            <div class="flex items-center gap-6 w-full md:w-auto justify-end">
                
                <!-- Nút nạp tiền (Demo UI) -->
                <a href="{{ route('deposit') }}" class="hidden md:flex items-center gap-2 bg-green-50 text-green-700 px-4 py-2 rounded-xl border border-green-200 font-bold hover:bg-green-100 transition-all">
                    <i class="fa-solid fa-wallet"></i> Nạp tiền
                </a>

                <!-- User Info Profile -->
                <div class="flex items-center gap-3 pl-6 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <!-- Hiển thị tên từ DB ngay lập tức -->
                        <div class="text-sm font-bold text-slate-700" id="userName">
                            {{ Auth::user()->name }}
                        </div>
                        <!-- Hiển thị số dư từ DB ngay lập tức -->
                        <div class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded ml-auto w-fit border border-red-100" id="userBalanceDisplay">
                            {{ number_format(Auth::user()->balance, 0, ',', '.') }}đ
                        </div>
                        <!-- Thẻ ẩn giữ giá trị raw để JS tính toán nếu cần -->
                        <span id="userBalance" class="hidden">{{ Auth::user()->balance }}</span>
                    </div>
                    
                    <!-- Avatar & Dropdown Logout -->
                    <div class="relative group">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ef4444&color=fff" 
                             alt="Avatar" 
                             class="w-10 h-10 rounded-full border-2 border-white shadow-md cursor-pointer">
                        
                        <!-- Dropdown Menu -->
                        <!-- FIX: Sử dụng top-full và pt-2 để tạo cầu nối vô hình giữa Avatar và Menu -->
                        <div class="absolute right-0 top-full pt-2 w-48 hidden group-hover:block z-50">
                            <!-- Wrapper chứa nội dung menu thực tế -->
                            <div class="bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
                                <div class="p-3 border-b border-slate-100 text-xs text-slate-500">
                                    Đăng nhập với: <br>
                                    <strong class="text-slate-800">{{ Auth::user()->email }}</strong>
                                </div>
                                <a href="{{ route('history') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-red-500">
                                    <i class="fa-solid fa-clock-rotate-left mr-2"></i> Lịch sử mua
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-red-500">
                                    <i class="fa-solid fa-user-gear mr-2"></i> Cài đặt
                                </a>
                                
                                <!-- Form Đăng Xuất -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 font-bold hover:bg-red-50 transition-colors">
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- LEFT COLUMN: CATEGORIES & FILTER (3 Cols) -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Sidebar Category -->
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                    <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-red-500 rounded-full"></span>
                        DANH MỤC
                    </h3>
                    <ul id="categoryList" class="space-y-1 text-sm">
                        <!-- JS sẽ render category vào đây -->
                        <li class="animate-pulse h-8 bg-slate-100 rounded-lg mb-2"></li>
                        <li class="animate-pulse h-8 bg-slate-100 rounded-lg mb-2"></li>
                        <li class="animate-pulse h-8 bg-slate-100 rounded-lg"></li>
                    </ul>
                </div>

                <!-- Support Widget -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
                    <h3 class="font-bold text-lg mb-1">Cần hỗ trợ?</h3>
                    <p class="text-blue-100 text-xs mb-4">Liên hệ Admin nếu gặp lỗi nạp tiền hoặc bảo hành.</p>
                    <a href="https://t.me/peggyval" target="_blank" class="block text-center bg-white text-blue-700 font-bold py-2 rounded-lg text-sm hover:bg-blue-50 transition">
                        <i class="fa-brands fa-telegram"></i> Chat Telegram
                    </a>
                </div>
            </div>

            <!-- CENTER COLUMN: PRODUCTS (6 Cols) -->
            <div class="lg:col-span-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[600px] flex flex-col">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center backdrop-blur-sm sticky top-0 z-10">
                        <div class="flex items-center gap-2">
                            <span class="p-2 bg-red-100 text-red-600 rounded-lg text-sm"><i class="fa-solid fa-layer-group"></i></span>
                            <h3 class="font-bold text-slate-700 text-sm md:text-base">KHO TÀI NGUYÊN</h3>
                        </div>
                        <span class="text-[10px] font-bold text-green-600 bg-green-100 px-2 py-1 rounded border border-green-200 flex items-center gap-1">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Live Update
                        </span>
                    </div>
                    
                    <!-- Product List Container -->
                    <div id="productList" class="p-4 space-y-3 overflow-y-auto flex-1 scrollbar-hide">
                        <div class="text-center py-20 text-slate-400">
                            <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 text-slate-300"></i>
                            <p>Đang tải dữ liệu...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: BUY FORM (3 Cols) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm sticky top-6" id="buyForm">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-slate-700 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-slate-400"></i> ĐƠN HÀNG
                        </h3>
                    </div>
                    
                    <div class="p-5 space-y-5">
                        <!-- Selected Product Info -->
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Sản phẩm đang chọn</label>
                            <div class="relative">
                                <input type="text" id="selectedProductName" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all" readonly placeholder="Vui lòng chọn sản phẩm...">
                                <input type="hidden" id="selectedProductId">
                                <div class="absolute right-3 top-3 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-box-open"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Amount & Coupon -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Số lượng</label>
                                <input type="number" id="amount" class="w-full border border-slate-200 rounded-xl p-3 text-sm font-bold text-center focus:border-red-500 focus:outline-none transition-all" value="1" min="1">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-1.5">Giảm giá</label>
                                <input type="text" id="coupon" class="w-full border border-slate-200 rounded-xl p-3 text-sm focus:border-red-500 focus:outline-none transition-all" placeholder="Mã...">
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-500 uppercase">Tổng tiền</span>
                            <span id="totalPrice" class="text-xl font-black text-red-600">0đ</span>
                        </div>

                        <!-- Button -->
                        <button id="btnBuy" class="w-full bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-red-500/30 disabled:opacity-50 disabled:cursor-not-allowed transform active:scale-95">
                            THANH TOÁN NGAY <i class="fa-solid fa-angles-right ml-1"></i>
                        </button>

                        <!-- Result Area -->
                        <div id="resultArea" class="hidden text-sm animate-fade-in-up"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Load JS -->
    <script src="{{ asset('js/shop-online/dashboard.js') }}"></script>
    
    <!-- Script nhỏ để update UI realtime nếu JS tính toán lại số dư -->
    <script>
        // Khi JS update số dư, ta sẽ update lại giao diện format tiền tệ
        const balanceObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === "childList" || mutation.type === "characterData") {
                   // Logic format tiền nếu cần thiết khi JS update raw number
                }
            });
        });
    </script>
</body>
</html>