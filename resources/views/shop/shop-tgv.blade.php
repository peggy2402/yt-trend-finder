<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cửa Hàng Nguyên Liệu | ZENTRA Group</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="shortcut icon" href="{{asset('images/logo.png')}}" type="image/x-icon">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#dc2626',
                        'primary-light': '#fef2f2',
                        'secondary': '#1e293b',
                        'accent': '#3b82f6',
                    },
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slight': 'bounceSlight 2s infinite',
                        'pop-in': 'popIn 0.3s cubic-bezier(0.16, 1, 0.3, 1)',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        bounceSlight: {
                            '0%, 100%': { transform: 'translateY(-5%)' },
                            '50%': { transform: 'translateY(0)' },
                        },
                        popIn: {
                            '0%': { opacity: '0', transform: 'scale(0.95)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        /* Custom Styles */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .category-pill {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }
        
        .product-card {
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -4px rgba(0, 0, 0, 0.1);
        }
        
        .mobile-bottom-sheet {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @media (max-width: 1023px) {
            .mobile-bottom-sheet.closed {
                transform: translateY(100%);
            }
            .mobile-bottom-sheet.open {
                transform: translateY(0);
            }
        }
        
        .stock-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
        }
        
        .skeleton-gradient {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Success Modal Animation */
        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }
        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: opacity 300ms, transform 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal-exit {
            opacity: 1;
            transform: scale(1);
        }
        .modal-exit-active {
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 200ms, transform 200ms;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 text-secondary font-outfit min-h-screen">

    <!-- Mobile Bottom Sheet Overlay -->
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    <!-- MAIN LAYOUT -->
    <div class="min-h-screen flex flex-col">
        
        <!-- HEADER - Mobile First -->
        <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-slate-200 shadow-sm">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-lg hover:bg-slate-100">
                        <i class="fas fa-bars text-lg text-slate-700"></i>
                    </button>
                    
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <a href="/" class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gradient-to-br from-primary to-red-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-white text-sm"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-slate-800 leading-tight">SHOP<span class="text-primary">ZENTRA</span></h1>
                                <p class="text-[10px] text-slate-500 font-medium">Auto Distribution</p>
                            </div>
                        </a>
                    </div>
                    
                    <!-- User Actions - Mobile -->
                    <div class="flex items-center gap-3">
                        <!-- Balance - Mobile -->
                        <div class="lg:hidden">
                            <div class="px-3 py-1 bg-primary-light rounded-full border border-red-200">
                                <span id="mobileUserBalance" class="text-xs font-bold text-primary">
                                    {{ number_format(Auth::user()->balance, 0, ',', '.') }}đ
                                </span>
                            </div>
                        </div>
                        
                        <!-- User Avatar with Dropdown -->
                        <div class="relative group">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=dc2626&color=fff&bold=true&size=128" 
                                 alt="Avatar" 
                                 class="w-10 h-10 rounded-full border-2 border-white shadow cursor-pointer"
                                 id="userAvatar">
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 top-full pt-2 w-48 hidden group-hover:block z-50">
                                <div class="bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden">
                                    <div class="p-3 border-b border-slate-100 text-xs text-slate-500">
                                        Đăng nhập với: <br>
                                        <strong class="text-slate-800">{{ Auth::user()->email }}</strong>
                                    </div>
                                    <a href="{{ route('history') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-red-500 transition">
                                        <i class="fa-solid fa-clock-rotate-left mr-2"></i> Lịch sử mua
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-red-500 transition">
                                        <i class="fa-solid fa-user-gear mr-2"></i> Cài đặt
                                    </a>
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
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 container mx-auto px-4 py-4 max-w-7xl">
            <div class="flex flex-col lg:flex-row gap-6">
                
                <!-- LEFT SIDEBAR - Desktop Only -->
                <aside class="hidden lg:flex flex-col w-full lg:w-72 flex-shrink-0 gap-6">
                    
                    <!-- User Profile Card -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=dc2626&color=fff&bold=true&size=128" 
                                 alt="Avatar" 
                                 class="w-14 h-14 rounded-xl border-2 border-white shadow">
                            <div class="flex-1">
                                <h3 id="userName" class="font-bold text-slate-800 truncate">{{ Auth::user()->name }}</h3>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                <div class="mt-2">
                                    <div id="desktopUserBalance" class="text-sm font-bold text-primary bg-primary-light px-3 py-1 rounded-lg inline-block border border-red-200">
                                        {{ number_format(Auth::user()->balance, 0, ',', '.') }}đ
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="mt-5 grid grid-cols-2 gap-2">
                            <a href="{{ route('deposit') }}" class="flex items-center justify-center gap-2 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 px-3 py-2 rounded-xl border border-green-200 font-semibold text-sm hover:bg-green-100 transition-all">
                                <i class="fas fa-wallet"></i> Nạp tiền
                            </a>
                            <a href="{{ route('history') }}" class="flex items-center justify-center gap-2 bg-slate-50 text-slate-700 px-3 py-2 rounded-xl border border-slate-200 font-semibold text-sm hover:bg-slate-100 transition-all">
                                <i class="fas fa-history"></i> Lịch sử
                            </a>
                        </div>
                    </div>
                    
                    <!-- Categories Sidebar -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                                <span class="w-2 h-5 bg-primary rounded-full"></span>
                                Danh mục
                            </h3>
                            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                <span id="categoryCount">0</span> danh mục
                            </span>
                        </div>
                        
                        <!-- Categories List - Vertical Scroll -->
                        <div id="categoryList" class="max-h-80 overflow-y-auto scrollbar-hide space-y-2 pr-2">
                            <!-- Categories will be loaded here -->
                            <div class="space-y-2">
                                <div class="skeleton-gradient h-10 rounded-lg"></div>
                                <div class="skeleton-gradient h-10 rounded-lg"></div>
                                <div class="skeleton-gradient h-10 rounded-lg"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Support Card -->
                    <div class="bg-gradient-to-br from-accent to-blue-600 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i class="fas fa-headset text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Hỗ trợ 24/7</h3>
                                <p class="text-blue-100 text-xs">Giải đáp mọi thắc mắc</p>
                            </div>
                        </div>
                        <a href="https://t.me/peggyval" target="_blank" 
                           class="block text-center bg-white text-accent font-bold py-3 rounded-xl text-sm hover:bg-blue-50 transition transform hover:scale-[1.02] active:scale-95">
                            <i class="fab fa-telegram"></i> Chat Telegram
                        </a>
                    </div>
                </aside>

                <!-- MAIN CONTENT AREA -->
                <div class="flex-1 flex flex-col">
                    
                    <!-- Categories Horizontal Scroll - Mobile Only -->
                    <div class="lg:hidden mb-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                                <span class="w-1.5 h-5 bg-primary rounded-full"></span>
                                Danh mục
                            </h3>
                            <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-1 rounded" id="mobileCategoryCount">0 danh mục</span>
                        </div>
                        <div id="mobileCategoryList" class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide -mx-2 px-2">
                            <!-- Categories will be loaded here as pills -->
                            <div class="flex gap-2">
                                <div class="skeleton-gradient h-9 w-24 rounded-full"></div>
                                <div class="skeleton-gradient h-9 w-24 rounded-full"></div>
                                <div class="skeleton-gradient h-9 w-24 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Products Section -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex-1 flex flex-col">
                        <!-- Products Header -->
                        <div class="glass-card border-b border-slate-100 p-4 sticky top-0 z-20">
                            <div class="flex flex-col gap-3">
                                <!-- Title & Actions Row -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-primary-light rounded-xl flex items-center justify-center">
                                            <i class="fas fa-boxes text-primary"></i>
                                        </div>
                                        <div>
                                            <h2 class="font-bold text-slate-800 text-lg">Kho tài nguyên</h2>
                                            <p class="text-xs text-slate-500">Chọn sản phẩm để mua</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Live Status -->
                                    <div class="hidden sm:flex items-center gap-2 text-xs font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-200">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </span>
                                        Cập nhật thời gian thực
                                    </div>
                                </div>

                                <!-- Search & Filter Row -->
                                <div class="flex items-center gap-2">
                                    <!-- Search Input with Suggestions -->
                                    <div class="flex-1 relative">
                                        <input type="text" 
                                               id="searchInput" 
                                               placeholder="Tìm kiếm sản phẩm, danh mục..." 
                                               autocomplete="off"
                                               class="w-full pl-10 pr-10 py-2 text-sm border border-slate-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-all">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                                        
                                        <!-- Clear button -->
                                        <button id="clearSearch" 
                                                class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                        
                                        <!-- Search Suggestions Dropdown -->
                                        <div id="searchSuggestions" 
                                             class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-80 overflow-y-auto">
                                            <!-- Suggestions will be populated here -->
                                        </div>
                                    </div>
                                    
                                    <!-- Stock Filter Toggle -->
                                    <button id="toggleStockFilter" 
                                            class="flex items-center gap-2 text-xs font-semibold px-4 py-2 rounded-xl border transition-all bg-primary-light text-primary border-primary whitespace-nowrap">
                                        <i class="fas fa-eye-slash"></i>
                                        <span class="hidden sm:inline">Ẩn hết hàng</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Products Grid -->
                        <div id="productList" class="p-4 space-y-4 overflow-y-auto max-h-[600px] scrollbar-hide">
                            <!-- Products will be loaded here -->
                            <!-- Skeleton Loading -->
                            <div class="space-y-4">
                                <div class="skeleton-gradient h-32 rounded-xl"></div>
                                <div class="skeleton-gradient h-32 rounded-xl"></div>
                                <div class="skeleton-gradient h-32 rounded-xl"></div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div id="paginationContainer" class="hidden p-4 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-slate-600">
                                    Hiển thị <span id="pageStart">1</span>-<span id="pageEnd">20</span> / <span id="totalProducts">0</span> sản phẩm
                                </div>
                                <div class="flex items-center gap-2">
                                    <button id="prevPage" 
                                            class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                        <i class="fas fa-chevron-left mr-1"></i> Trước
                                    </button>
                                    <div class="flex items-center gap-1" id="pageNumbers">
                                        <!-- Page numbers will be inserted here -->
                                    </div>
                                    <button id="nextPage" 
                                            class="px-4 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                        Sau <i class="fas fa-chevron-right ml-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Empty State -->
                        <div id="emptyProducts" class="hidden p-8 text-center">
                            <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box-open text-3xl text-slate-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-700 mb-2">Không có sản phẩm</h3>
                            <p class="text-slate-500 mb-4">Tất cả sản phẩm đang hết hàng hoặc đang cập nhật</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR - Buy Form (Desktop) -->
                <aside class="hidden lg:block w-full lg:w-96 flex-shrink-0">
                    <div class="sticky top-6">
                        <!-- Buy Form Card -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                            <!-- Form Header -->
                            <div class="bg-gradient-to-r from-primary to-red-600 p-5 text-white">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-bold text-lg flex items-center gap-2">
                                        <i class="fas fa-shopping-cart"></i>
                                        Đơn hàng
                                    </h3>
                                    <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded-full">
                                        <span id="cartItemCount">0</span> sản phẩm
                                    </span>
                                </div>
                                <p class="text-sm text-red-100 opacity-90">Nhập thông tin và thanh toán</p>
                            </div>
                            
                            <!-- Form Content -->
                            <div class="p-5 space-y-5">
                                <!-- Selected Product -->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                                        <i class="fas fa-cube text-slate-400 mr-2"></i>
                                        Sản phẩm đã chọn
                                    </label>
                                    <div id="selectedProductDisplay" class="bg-slate-50 border border-slate-200 rounded-xl p-4 min-h-[80px] flex flex-col justify-center">
                                        <div class="text-center text-slate-400">
                                            <i class="fas fa-box-open text-2xl mb-2"></i>
                                            <p class="text-sm">Chưa chọn sản phẩm</p>
                                        </div>
                                    </div>
                                    <input type="hidden" id="selectedProductId">
                                    <input type="hidden" id="selectedProductPrice">
                                </div>
                                
                                <!-- Quantity & Coupon -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-hashtag text-slate-400 mr-2"></i>
                                            Số lượng
                                        </label>
                                        <div class="flex items-center border-2 border-slate-200 rounded-xl overflow-hidden bg-white">
                                            <button id="decreaseQty" 
                                                    type="button"
                                                    class="w-12 h-12 bg-slate-50 hover:bg-red-500 hover:text-white text-slate-700 flex items-center justify-center transition-all font-black text-2xl select-none active:scale-95">
                                                <span class="leading-none">−</span>
                                            </button>
                                            <input type="number" 
                                                   id="amount" 
                                                   class="flex-1 h-12 text-center font-bold text-slate-800 focus:outline-none border-0"
                                                   value="1" 
                                                   min="1">
                                            <button id="increaseQty" 
                                                    type="button"
                                                    class="w-12 h-12 bg-slate-50 hover:bg-red-500 hover:text-white text-slate-700 flex items-center justify-center transition-all font-black text-2xl select-none active:scale-95">
                                                <span class="leading-none">+</span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            <i class="fas fa-tag text-slate-400 mr-2"></i>
                                            Mã giảm giá
                                        </label>
                                        <input type="text" 
                                               id="coupon" 
                                               class="w-full h-12 border border-slate-200 rounded-xl px-4 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition"
                                               placeholder="Nhập mã...">
                                    </div>
                                </div>
                                
                                <!-- Price Breakdown -->
                                <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Đơn giá</span>
                                        <span id="unitPrice" class="font-semibold text-slate-800">0đ</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-slate-600">Số lượng</span>
                                        <span id="displayQty" class="font-semibold text-slate-800">1</span>
                                    </div>
                                    <div class="flex justify-between items-center text-green-600">
                                        <span>Giảm giá</span>
                                        <span id="discountAmount" class="font-semibold">0đ</span>
                                    </div>
                                    <div class="border-t border-slate-200 pt-3">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold text-slate-800">Tổng cộng</span>
                                            <span id="totalPrice" class="text-2xl font-black text-primary">0đ</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="space-y-3">
                                    <button id="btnBuy" 
                                            class="w-full bg-gradient-to-r from-primary to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-red-500/30 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-3">
                                        <i class="fas fa-bolt"></i>
                                        THANH TOÁN NGAY
                                    </button>
                                    
                                    <button id="btnReset" 
                                            class="w-full border-2 border-slate-200 text-slate-600 font-semibold py-3 rounded-xl hover:bg-slate-50 transition">
                                        <i class="fas fa-redo mr-2"></i>
                                        Chọn lại sản phẩm
                                    </button>
                                </div>
                                
                                <!-- Result Display (Legacy, replaced by modal) -->
                                <div id="resultArea" class="hidden"></div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="mt-4 bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
                            <h4 class="font-bold text-slate-800 mb-3">Thống kê nhanh</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-blue-50 p-3 rounded-xl">
                                    <div class="text-xs text-blue-600 font-semibold">Sản phẩm có sẵn</div>
                                    <div id="availableProducts" class="text-lg font-bold text-blue-800">0</div>
                                </div>
                                <div class="bg-emerald-50 p-3 rounded-xl">
                                    <div class="text-xs text-emerald-600 font-semibold">Đã mua hôm nay</div>
                                    <div id="todayOrders" class="text-lg font-bold text-emerald-800">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </div>

    <!-- MOBILE BOTTOM SHEET - Buy Form (Mobile) -->
    <div id="mobileBottomSheet" class="mobile-bottom-sheet closed lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white rounded-t-2xl shadow-2xl border-t border-slate-200 max-h-[85vh] overflow-y-auto">
        <!-- Sheet Handle -->
        <div class="flex justify-center pt-3">
            <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
        </div>
        
        <!-- Sheet Content -->
        <div class="p-5">
            <!-- Sheet Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-primary"></i>
                    Đơn hàng
                </h3>
                <button id="closeSheet" class="p-2 hover:bg-slate-100 rounded-lg">
                    <i class="fas fa-times text-slate-500"></i>
                </button>
            </div>
            
            <!-- Selected Product (Mobile) -->
            <div id="mobileSelectedProduct" class="mb-4">
                <!-- Content will be populated by JS -->
            </div>
            
            <!-- Quantity & Coupon (Mobile) -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Số lượng</label>
                    <div class="flex items-center border-2 border-slate-200 rounded-xl overflow-hidden bg-white">
                        <button id="mobileDecreaseQty" 
                                type="button"
                                class="w-10 h-10 bg-slate-50 hover:bg-red-500 hover:text-white text-slate-700 flex items-center justify-center transition-all font-black text-xl select-none active:scale-95">
                            <span class="leading-none">−</span>
                        </button>
                        <input type="number" 
                               id="mobileAmount" 
                               class="flex-1 h-10 text-center font-bold text-slate-800 focus:outline-none border-0"
                               value="1" 
                               min="1">
                        <button id="mobileIncreaseQty" 
                                type="button"
                                class="w-10 h-10 bg-slate-50 hover:bg-red-500 hover:text-white text-slate-700 flex items-center justify-center transition-all font-black text-xl select-none active:scale-95">
                            <span class="leading-none">+</span>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mã giảm giá</label>
                    <input type="text" 
                           id="mobileCoupon" 
                           class="w-full h-10 border border-slate-200 rounded-xl px-3 text-sm focus:border-primary focus:outline-none"
                           placeholder="Nhập mã...">
                </div>
            </div>
            
            <!-- Total Price (Mobile) -->
            <div class="bg-gradient-to-r from-primary-light to-red-50 p-4 rounded-xl border border-red-100 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-slate-700 font-semibold">Tổng tiền</span>
                    <span id="mobileTotalPrice" class="text-2xl font-black text-primary">0đ</span>
                </div>
            </div>
            
            <!-- Action Buttons (Mobile) -->
            <div class="space-y-3">
                <button id="mobileBtnBuy" 
                        class="w-full bg-gradient-to-r from-primary to-red-600 text-white font-bold py-3.5 rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <i class="fas fa-bolt"></i>
                    THANH TOÁN
                </button>
                
                <button id="mobileBtnReset" 
                        class="w-full border border-slate-300 text-slate-600 font-semibold py-3 rounded-xl">
                    <i class="fas fa-redo mr-2"></i>
                    Chọn lại
                </button>
            </div>
        </div>
    </div>

    <!-- NEW SUCCESS MODAL (QUICKVIEW) -->
    <div id="successModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity opacity-0" id="successModalBackdrop"></div>
        
        <!-- Modal Content Container -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <!-- Modal Panel -->
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg scale-95 opacity-0" id="successModalContent">
                    
                    <!-- Header -->
                    <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-6 text-center relative">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white/20 backdrop-blur-md mb-4 animate-bounce-slight shadow-lg">
                            <i class="fa-solid fa-check text-3xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold leading-6 text-white" id="modal-title">Thanh toán thành công!</h3>
                        <div class="mt-2">
                            <p class="text-sm text-emerald-50">Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi.</p>
                        </div>
                        
                        <!-- Close Button -->
                        <button id="closeSuccessModalBtn" class="absolute top-4 right-4 text-white/70 hover:text-white transition rounded-lg p-1 hover:bg-white/10">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Body -->
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-2 tracking-wider flex items-center justify-between">
                                    <span>Thông tin đơn hàng</span>
                                    <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded text-[10px] font-extrabold">ĐÃ THANH TOÁN</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-0 bg-slate-50 rounded-xl border border-slate-200"></div>
                                    <textarea id="purchasedData" 
                                            class="relative block w-full h-40 bg-transparent border-0 focus:ring-0 text-sm font-mono text-slate-700 resize-none p-4 leading-relaxed" 
                                            readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Footer Actions -->
                    <div class="bg-slate-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3 border-t border-slate-100">
                        <a href="{{ route('history') }}" class="inline-flex w-full justify-center rounded-xl bg-emerald-600 px-3 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-200 hover:bg-emerald-500 sm:w-auto sm:flex-1 gap-2 items-center transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            Lịch sử mua hàng
                        </a>
                        <button type="button" id="copyDataBtn" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto sm:flex-1 gap-2 items-center transition-all">
                            <i class="fa-regular fa-copy"></i>
                            Sao chép
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast nhỏ -->
    <div id="toast-float" class="fixed top-5 right-5 z-50"></div>

    <!-- Confirm modal - ĐÃ SỬA: Thêm Flexbox để căn giữa -->
    <div id="toast-overlay" class="fixed inset-0 z-[60] hidden flex items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300">
        <div id="toast-modal" class="w-full flex justify-center p-4"></div>
    </div>

    </div>
    <!-- Load JS -->
    <script src="{{ asset('js/shop-online/dashboard.js') }}"></script>
    
    <!-- Enhanced UI Script -->
    <script>
        // Mobile Bottom Sheet Control
        const mobileSheet = document.getElementById('mobileBottomSheet');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const closeSheetBtn = document.getElementById('closeSheet');
        
        // Open mobile bottom sheet when product is selected
        function openMobileSheet() {
            mobileSheet.classList.remove('closed');
            mobileSheet.classList.add('open');
            mobileOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        // Close mobile bottom sheet
        function closeMobileSheet() {
            mobileSheet.classList.remove('open');
            mobileSheet.classList.add('closed');
            mobileOverlay.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Event Listeners
        closeSheetBtn?.addEventListener('click', closeMobileSheet);
        mobileOverlay?.addEventListener('click', closeMobileSheet);
        
        // Prevent sheet close when clicking inside
        mobileSheet?.addEventListener('click', (e) => {
            e.stopPropagation();
        });
        
        // Quantity controls
        function setupQuantityControls() {
            // Desktop controls
            document.getElementById('decreaseQty')?.addEventListener('click', () => {
                const input = document.getElementById('amount');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updatePrice();
                }
            });
            
            document.getElementById('increaseQty')?.addEventListener('click', () => {
                const input = document.getElementById('amount');
                input.value = parseInt(input.value) + 1;
                updatePrice();
            });
            
            // Mobile controls
            document.getElementById('mobileDecreaseQty')?.addEventListener('click', () => {
                const input = document.getElementById('mobileAmount');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateMobilePrice();
                }
            });
            
            document.getElementById('mobileIncreaseQty')?.addEventListener('click', () => {
                const input = document.getElementById('mobileAmount');
                input.value = parseInt(input.value) + 1;
                updateMobilePrice();
            });
        }
        
        // Stock Filter Toggle
        document.getElementById('toggleStockFilter')?.addEventListener('click', function() {
            const filterText = this.querySelector('span');
            const filterIcon = this.querySelector('i');
            const isCurrentlyHiding = this.classList.contains('bg-primary-light');
            
            if (isCurrentlyHiding) {
                // Switch to show all
                filterText.textContent = 'Hiện tất cả';
                filterIcon.className = 'fas fa-eye';
                this.classList.remove('bg-primary-light', 'text-primary', 'border-primary');
                this.classList.add('bg-slate-100', 'text-slate-600', 'border-slate-200');
            } else {
                // Switch to hide out of stock
                filterText.textContent = 'Ẩn hết hàng';
                filterIcon.className = 'fas fa-eye-slash';
                this.classList.remove('bg-slate-100', 'text-slate-600', 'border-slate-200');
                this.classList.add('bg-primary-light', 'text-primary', 'border-primary');
            }
            
            // Trigger filter function
            if (typeof filterOutOfStockProducts === 'function') {
                filterOutOfStockProducts(!isCurrentlyHiding);
            }
        });
        
        // Product selection handler (example)
        function selectProduct(product) {
            // Update desktop view
            document.getElementById('selectedProductDisplay').innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-primary"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800">${product.name}</h4>
                        <p class="text-sm text-slate-500">${product.category}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-primary">${formatCurrency(product.price)}</div>
                        <div class="text-xs text-slate-500">còn ${product.stock} cái</div>
                    </div>
                </div>
            `;
            
            // Update mobile view
            document.getElementById('mobileSelectedProduct').innerHTML = `
                <div class="bg-slate-50 p-3 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-bold text-slate-800">${product.name}</h4>
                            <p class="text-xs text-slate-500">${product.category}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-primary">${formatCurrency(product.price)}</div>
                            <div class="text-xs text-slate-500">còn ${product.stock} cái</div>
                        </div>
                    </div>
                </div>
            `;
            
            // Set hidden values
            document.getElementById('selectedProductId').value = product.id;
            document.getElementById('selectedProductPrice').value = product.price;
            
            // Open mobile sheet if on mobile
            if (window.innerWidth < 1024) {
                openMobileSheet();
            }
            
            // Update prices
            updatePrice();
            updateMobilePrice();
        }
        
        // Price calculation functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }
        
        function updatePrice() {
            const price = parseFloat(document.getElementById('selectedProductPrice').value) || 0;
            const quantity = parseInt(document.getElementById('amount').value) || 1;
            const total = price * quantity;
            
            document.getElementById('unitPrice').textContent = formatCurrency(price);
            document.getElementById('displayQty').textContent = quantity;
            document.getElementById('totalPrice').textContent = formatCurrency(total);
        }
        
        function updateMobilePrice() {
            const price = parseFloat(document.getElementById('selectedProductPrice').value) || 0;
            const quantity = parseInt(document.getElementById('mobileAmount').value) || 1;
            const total = price * quantity;
            
            document.getElementById('mobileTotalPrice').textContent = formatCurrency(total);
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setupQuantityControls();
            
            // Update category count (example)
            setTimeout(() => {
                document.getElementById('categoryCount').textContent = '8';
                document.getElementById('mobileCategoryCount').textContent = '8 danh mục';
                document.getElementById('availableProducts').textContent = '24';
                document.getElementById('todayOrders').textContent = '156';
            }, 1000);
        });
        
        // Close sheet on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMobileSheet();
            }
        });
        
        // Example product data structure
        const exampleProduct = {
            id: 1,
            name: 'Facebook Ads Account',
            category: 'Facebook',
            price: 150000,
            stock: 10,
            status: 'available'
        };
    </script>
</body>
</html>