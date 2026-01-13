<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZTGroup Analytics | Ultimate Hunter V3.8</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons (Fixed CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <!-- Tailwind Config cho Dark Mode -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        dark: {
                            bg: '#0f172a', // Slate 900
                            card: '#1e293b', // Slate 800
                            border: '#334155', // Slate 700
                            text: '#f1f5f9' // Slate 100
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="shortcut icon" href="{{asset('images/logo32.png')}}" type="image/x-icon">
</head>

<body class="bg-slate-50 text-slate-800 font-sans transition-colors duration-300 dark:bg-dark-bg dark:text-dark-text">

    <div id="toast-container" class="flex flex-col gap-3"></div>

    <!-- HEADER -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm backdrop-blur-md bg-white/95 dark:bg-dark-card/95 dark:border-dark-border transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">

            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600 transition-colors" title="Về trang chủ">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center shadow-md">
                        <i class="fa-brands fa-youtube text-white text-lg sm:text-xl"></i>
                    </div>
                    <div class="leading-tight hidden sm:block">
                        <h1 class="font-extrabold text-base sm:text-lg tracking-tight text-slate-900 dark:text-white">
                            ZTGroup<span class="text-red-500"> Analytics Beta v0.1.0</span>
                        </h1>
                        <p class="text-[9px] sm:text-[10px] text-red-500 font-bold tracking-wide">ULTIMATE HUNTER V0.1.0</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Dark Mode Toggle -->
                <button onclick="toggleDarkMode()" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-yellow-400 dark:hover:bg-slate-600 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-moon dark:hidden"></i>
                    <i class="fa-solid fa-sun hidden dark:block"></i>
                </button>

                <button onclick="openSettings()" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 px-3 py-1.5 rounded-full transition shadow-sm group dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-700">
                    <div class="relative">
                        <i class="fa-solid fa-key group-hover:text-red-500 transition-colors text-sm"></i>
                        <span id="apiKeyDot" class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-gray-300 rounded-full border border-white dark:border-slate-800"></span>
                    </div>
                    <span class="text-xs font-bold hidden sm:inline" id="apiKeyBtnText">Nhập Key</span>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 flex-grow">
        
        <!-- SEARCH & FILTER BOX -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-6 mb-6 sm:mb-8 dark:bg-dark-card dark:border-dark-border transition-colors duration-300">
            <!-- Search Row -->
            <div class="flex flex-col md:flex-row gap-4 items-end mb-4">
                <div class="flex-grow w-full">
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider dark:text-slate-400">Từ khóa Ngách (Seed Keyword)</label>
                    <div class="relative group">
                        <input type="text" id="keyword" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:bg-white outline-none transition-all font-medium text-slate-700 shadow-sm placeholder:text-slate-400" placeholder="VD: Street Food, Review Xe...">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 group-focus-within:text-red-500 transition-colors dark:text-slate-500"></i>
                    </div>
                </div>
                
                <div class="w-full md:w-auto">
                    <button onclick="analyzeKeywords()" id="analyzeBtn" class="w-full md:w-48 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 transform active:scale-95 shadow-red-600/30">
                        <span>PHÂN TÍCH</span>
                        <i class="fa-solid fa-radar"></i>
                    </button>
                </div>
            </div>

            <!-- Advanced Filters Toggle & Deep Scan -->
            <div class="border-t border-slate-100 pt-4 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-slate-700">
                <button onclick="toggleFilters()" class="flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-red-600 transition w-full sm:w-auto justify-center sm:justify-start dark:text-slate-300 dark:hover:text-red-400">
                    <i class="fa-solid fa-sliders"></i> Tùy chỉnh Bộ lọc
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" id="filterArrow"></i>
                </button>
                
                <!-- NEW SMOOTH DEEP SCAN TOGGLE (FIXED LAYOUT) -->
                <label class="inline-flex items-center cursor-pointer group bg-slate-50 sm:bg-transparent px-3 py-2 sm:p-0 rounded-lg w-full sm:w-auto justify-center sm:justify-start dark:bg-slate-800 sm:dark:bg-transparent">
                    <div class="relative">
                        <input type="checkbox" id="deepScanToggle" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600 shadow-inner dark:bg-slate-600 dark:after:border-slate-500"></div>
                    </div>
                    <span class="ml-3 text-xs font-bold text-slate-600 uppercase group-hover:text-red-600 transition-colors select-none dark:text-slate-300 dark:group-hover:text-red-400">
                        <i class="fa-solid fa-layer-group mr-1"></i> Quét Sâu (Max 250)
                    </span>
                </label>
            </div>

            <!-- Hidden Filters -->
            <div id="advancedFilters" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 animate-fade-in mt-4 pt-4 border-t border-dashed border-slate-200 dark:border-slate-700">
                <!-- Time -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase dark:text-slate-400">Khoảng Thời Gian</label>
                    <select id="filterTime" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-red-500 outline-none h-10 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        <option value="any">Toàn thời gian</option>
                        <option value="hour">1 Giờ qua</option>
                        <option value="today">Hôm nay (24h)</option>
                        <option value="week">Tuần này</option>
                        <option value="month" selected>Tháng này</option>
                        <option value="year">Năm nay</option>
                    </select>
                </div>
                
                <!-- Region -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase dark:text-slate-400">Thị trường / Quốc gia</label>
                    <select id="filterRegion" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-red-500 outline-none h-10 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        <option value="GLOBAL">🌍 Global (Toàn cầu)</option>
                        
                        <optgroup label="💰 Top Tier 1 (RPM Cao)">
                            <option value="US">🇺🇸 Hoa Kỳ (USA)</option>
                            <option value="GB">🇬🇧 Anh Quốc (UK)</option>
                            <option value="CA">🇨🇦 Canada</option>
                            <option value="AU">🇦🇺 Úc (Australia)</option>
                            <option value="DE">🇩🇪 Đức (Germany)</option>
                            <option value="CH">🇨🇭 Thụy Sĩ</option>
                            <option value="NO">🇳🇴 Na Uy</option>
                        </optgroup>

                        <optgroup label="🌏 Châu Á (Asia)">
                            <option value="VN" selected>🇻🇳 Việt Nam</option>
                            <option value="JP">🇯🇵 Nhật Bản</option>
                            <option value="KR">🇰🇷 Hàn Quốc</option>
                            <option value="IN">🇮🇳 Ấn Độ</option>
                            <option value="ID">🇮🇩 Indonesia</option>
                            <option value="TH">🇹🇭 Thái Lan</option>
                            <option value="SG">🇸🇬 Singapore</option>
                            <option value="PH">🇵🇭 Philippines</option>
                            <option value="MY">🇲🇾 Malaysia</option>
                            <option value="TW">🇹🇼 Đài Loan</option>
                            <option value="HK">🇭🇰 Hồng Kông</option>
                        </optgroup>

                        <optgroup label="🏰 Châu Âu (Europe)">
                            <option value="FR">🇫🇷 Pháp (France)</option>
                            <option value="ES">🇪🇸 Tây Ban Nha</option>
                            <option value="IT">🇮🇹 Ý (Italy)</option>
                            <option value="RU">🇷🇺 Nga (Russia)</option>
                            <option value="NL">🇳🇱 Hà Lan</option>
                            <option value="SE">🇸🇪 Thụy Điển</option>
                            <option value="PL">🇵🇱 Ba Lan</option>
                            <option value="UA">🇺🇦 Ukraine</option>
                            <option value="TR">🇹🇷 Thổ Nhĩ Kỳ</option>
                        </optgroup>

                        <optgroup label="🌎 Châu Mỹ (Americas)">
                            <option value="BR">🇧🇷 Brazil</option>
                            <option value="MX">🇲🇽 Mexico</option>
                            <option value="AR">🇦🇷 Argentina</option>
                            <option value="CO">🇨🇴 Colombia</option>
                            <option value="CL">🇨🇱 Chile</option>
                            <option value="PE">🇵🇪 Peru</option>
                        </optgroup>

                        <optgroup label="🦁 Châu Phi (Africa)">
                            <option value="ZA">🇿🇦 Nam Phi (South Africa)</option>
                            <option value="EG">🇪🇬 Ai Cập (Egypt)</option>
                            <option value="NG">🇳🇬 Nigeria</option>
                            <option value="KE">🇰🇪 Kenya</option>
                            <option value="MA">🇲🇦 Ma-rốc</option>
                        </optgroup>

                        <optgroup label="🦘 Châu Đại Dương (Oceania)">
                            <option value="NZ">🇳🇿 New Zealand</option>
                            <option value="FJ">🇫🇯 Fiji</option>
                        </optgroup>

                        <optgroup label="❄️ Châu Nam Cực & Khác">
                            <option value="AQ">🇦🇶 Nam Cực (Antarctica)</option>
                            <option value="IL">🇮🇱 Israel</option>
                            <option value="SA">🇸🇦 Ả Rập Xê Út</option>
                            <option value="AE">🇦🇪 UAE</option>
                        </optgroup>
                    </select>
                </div>
                
                <!-- Format -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase dark:text-slate-400">Định dạng</label>
                    <select id="filterFormat" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-red-500 outline-none h-10 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        <option value="any" selected>Tất cả</option>
                        <option value="video">Video Dài (Long Form)</option>
                        <option value="short">Shorts (< 60s)</option>
                    </select>
                </div>

                <!-- Max Results -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase dark:text-slate-400">Số lượng quét</label>
                    <div class="relative">
                        <input type="number" id="maxResults" value="50" min="1" max="50" class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:border-red-500 outline-none font-bold text-slate-700 h-10 dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="50">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400">Vid</span>
                    </div>
                </div>

               <!-- Post-Filters -->
                <div class="col-span-1 sm:col-span-2 md:col-span-4 flex gap-4 pt-2">
                    <div class="w-1/2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase dark:text-slate-400">Lọc Min Views</label>
                        <input type="number" id="minViews" value="0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none placeholder:text-slate-400 h-10 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:placeholder:text-slate-600" placeholder="VD: 10000">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1 uppercase dark:text-slate-400">Lọc Min Subs</label>
                        <input type="number" id="minSubs" value="0" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none placeholder:text-slate-400 h-10 dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:placeholder:text-slate-600" placeholder="VD: 1000">
                    </div>
                </div>
            </div>
        </div>

        <!-- LOADING -->
        <div id="loading" class="hidden py-12 text-center animate-fade-in">
            <div class="inline-block relative">
                <div class="w-16 h-16 border-4 border-red-200 border-t-red-600 rounded-full animate-spin dark:border-slate-700 dark:border-t-red-500"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-red-600 text-lg">
                    <i class="fa-brands fa-youtube"></i>
                </div>
            </div>
            <h3 class="text-slate-800 font-bold text-xl mt-4 dark:text-white" id="loadingTitle">Đang vận hành Radar...</h3>
            <p class="text-slate-500 text-sm mt-1 font-mono dark:text-slate-400" id="loadingText">Đang khởi tạo kết nối...</p>
        </div>

        <!-- RESULTS DASHBOARD -->
        <div id="resultsArea" class="hidden space-y-6 animate-fade-in">
            
            <!-- SECTION 1: VERDICT - KING KEYWORD - RPM -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- 1. Verdict (Đã làm mới UI) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col relative dark:bg-dark-card dark:border-dark-border">
                    <!-- Header -->
                    <div class="p-6 pb-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest dark:text-slate-500">KẾT LUẬN CHIẾN LƯỢC</span>
                        </div>
                        <h2 class="text-2xl font-black text-slate-800 dark:text-white" id="verdictText">--</h2>
                        <p class="text-slate-500 text-sm leading-relaxed dark:text-slate-400 mt-1" id="verdictDesc">Chưa có dữ liệu phân tích.</p>
                    </div>

                    <!-- Gauge Chart Area -->
                    <div class="flex-1 flex flex-col items-center justify-center p-6" id="verdictGaugeArea">
                        <!-- Placeholder khi chưa chạy -->
                        <div class="text-slate-300 text-sm animate-pulse">Đang chờ dữ liệu...</div>
                    </div>

                    <!-- Footer Stats -->
                    <div class="grid grid-cols-3 gap-2 p-4 border-t border-slate-100 bg-slate-50/50 dark:bg-slate-800/50 dark:border-slate-700">
                        <div class="text-center">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Avg Views</span>
                            <strong id="statVolume" class="text-sm text-slate-700 dark:text-slate-200">--</strong>
                        </div>
                        <div class="text-center border-l border-slate-200 dark:border-slate-700">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Đối thủ</span>
                            <strong id="statComp" class="text-sm text-slate-700 dark:text-slate-200">--</strong>
                        </div>
                        <div class="text-center border-l border-slate-200 dark:border-slate-700">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Điểm</span>
                            <strong id="statScoreBottom" class="text-sm text-slate-700 dark:text-slate-200">--</strong>
                        </div>
                    </div>
                </div>

                <!-- 2. KING KEYWORD -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl shadow-sm border border-orange-100 p-6 relative overflow-hidden flex flex-col dark:from-yellow-900/20 dark:to-orange-900/20 dark:border-orange-900/50">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-yellow-400 rounded-full opacity-10 blur-xl dark:opacity-5"></div>
                    <div class="flex justify-between items-start mb-4 relative z-10">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-bold text-orange-600 uppercase tracking-widest dark:text-orange-400">TỪ KHÓA HOT</span>
                                <i class="fa-solid fa-crown text-yellow-500 animate-bounce"></i>
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-900 capitalize break-words line-clamp-1 dark:text-white" id="kingKeyword">---</h3>
                            <p class="text-xs text-slate-500 mt-1 dark:text-slate-400">Xuất hiện <span id="kingCount" class="font-bold text-orange-600 dark:text-orange-400">0</span> lần</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg shadow-sm text-yellow-600 dark:bg-slate-800 dark:text-yellow-500"><i class="fa-solid fa-tags"></i></div>
                    </div>
                    <div class="relative z-10 border-t border-orange-200/50 pt-3 flex-grow dark:border-orange-800/30">
                        <h4 class="text-[10px] font-bold text-slate-400 uppercase mb-2">Các từ khóa phổ biến khác:</h4>
                        <div id="topKeywordsList" class="space-y-2 text-sm"></div>
                    </div>
                </div>

                <!-- 3. RPM -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200 flex flex-col justify-center relative dark:bg-dark-card dark:border-dark-border">
                    <h3 class="font-bold text-slate-800 mb-1 text-xs uppercase tracking-wide flex items-center gap-2 dark:text-slate-200">
                        <i class="fa-solid fa-hand-holding-dollar text-green-600"></i> Điều chỉnh RPM
                    </h3>
                    <div class="text-center my-2">
                        <span class="text-3xl font-black text-slate-800 dark:text-white" id="rpmDisplay">$0.3</span>
                        <span class="text-xs text-slate-400">/ 1k views</span>
                    </div>
                    <input type="range" id="rpmSlider" min="0.1" max="10.0" step="0.1" value="0.3" class="w-full cursor-pointer" oninput="updateRpm(this.value)">
                </div>
            </div>

            <!-- ROW 2: MICRO-NICHES -->
            <div id="microNicheContainer" class="hidden">
                <div class="flex items-center gap-2 mb-4 mt-2">
                    <div class="bg-indigo-600 text-white p-1.5 rounded-lg shadow-sm"><i class="fa-solid fa-dna text-sm"></i></div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base dark:text-white">Gợi ý Ngách Tiềm Năng (Micro-Niche)</h3>
                        <p class="text-[10px] text-slate-500 font-medium dark:text-slate-400">Hệ thống tự động phát hiện các cụm chủ đề nhỏ.</p>
                    </div>
                </div>
                <div id="microNicheGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
            </div>

            <!-- ROW 3: COMPETITORS & HEATMAP -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Competitors -->
                <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-slate-200 dark:bg-dark-card dark:border-dark-border">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-sm uppercase dark:text-white">
                        <i class="fa-solid fa-trophy text-yellow-500"></i> Top Kênh Thống Trị
                    </h3>
                    <div id="competitorList" class="space-y-2"></div>
                </div>

                <!-- Heatmap -->
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-slate-200 dark:bg-dark-card dark:border-dark-border">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm uppercase dark:text-white">
                            <i class="fa-solid fa-clock text-blue-500"></i> Thời Gian Đăng Hiệu Quả
                        </h3>
                        <span class="text-xs text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded dark:bg-blue-900/30 dark:text-blue-400" id="bestTimeText">--</span>
                    </div>
                    <div id="uploadHeatmap" class="h-32 flex items-end justify-between gap-1 mb-2"></div>
                    <div class="flex justify-between text-[10px] text-slate-400 font-mono border-t border-slate-100 pt-2 dark:border-slate-700">
                        <span>00h</span><span>06h</span><span>12h</span><span>18h</span><span>23h</span>
                    </div>
                </div>
            </div>

            <!-- ROW 4: VIDEO TABLE / CARD LIST -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden dark:bg-dark-card dark:border-dark-border">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row justify-between items-center gap-3 dark:border-slate-700 dark:bg-slate-800/50">
                    <h3 class="font-bold text-slate-800 text-sm uppercase flex items-center gap-2 dark:text-white">
                        <i class="fa-solid fa-list-ul"></i> Kết quả Quét (<span id="resultCount">0</span>)
                    </h3>
                    <button onclick="exportCSV()" class="text-xs bg-white border border-slate-300 hover:bg-green-50 hover:text-green-700 hover:border-green-300 text-slate-700 px-3 py-1.5 rounded shadow-sm font-bold transition flex items-center gap-1 w-full sm:w-auto justify-center dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-green-900/30 dark:hover:text-green-400">
                        <i class="fa-solid fa-file-excel"></i> Xuất Excel
                    </button>
                </div>
                
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-wider border-b border-slate-200 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400">
                                <th class="p-4 font-bold w-12">#</th>
                                <th class="p-4 font-bold max-w-md">Video / Thông tin</th>
                                <th class="p-4 font-bold text-right">Thị trường</th>
                                <th class="p-4 font-bold text-right">Loại</th>
                                <th class="p-4 font-bold text-right">Ngày đăng</th>
                                <th class="p-4 font-bold text-right">Views</th>
                                <th class="p-4 font-bold text-right">Kênh (Subs)</th>
                                <th class="p-4 font-bold text-right text-green-600 dark:text-green-400">Doanh thu ($)</th>
                            </tr>
                        </thead>
                        <tbody id="videoTableBody" class="divide-y divide-slate-100 text-sm dark:divide-slate-700"></tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div id="mobileVideoList" class="md:hidden p-4 space-y-4 bg-slate-50/50 dark:bg-slate-900/50"></div>

                <!-- Pagination -->
                <div id="paginationControls" class="px-6 py-4 border-t border-slate-200 flex justify-between items-center bg-slate-50 hidden dark:border-slate-700 dark:bg-slate-800">
                    <span class="text-xs text-slate-500 dark:text-slate-400" id="pageInfo">Trang 1</span>
                    <div class="flex gap-2" id="paginationBtns"></div>
                </div>
            </div>
        </div>
    </main>

    <!-- SETTINGS MODAL -->
    <div id="settingsModal" class="modal fixed inset-0 z-[9000] flex items-center justify-center opacity-0 invisible pointer-events-none">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeSettings()"></div>
        <div class="bg-white w-full max-w-md mx-4 rounded-2xl shadow-2xl z-10 p-6 relative transform transition-all scale-95 dark:bg-dark-card" id="modalContent">
            <button onclick="closeSettings()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:hover:text-white"><i class="fa-solid fa-xmark text-xl"></i></button>
            <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2 dark:text-white"><i class="fa-solid fa-gear text-slate-600 dark:text-slate-400"></i> Cấu hình Hệ thống</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase dark:text-slate-400">YouTube Data API Keys <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <textarea id="inputApiKey" rows="5" class="w-full px-4 py-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-slate-800 outline-none font-mono dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Paste danh sách API Key tại đây..."></textarea>
                        <div class="absolute bottom-2 right-2 text-[10px] text-slate-400 bg-white px-1 dark:bg-slate-800">Mỗi dòng 1 Key</div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2 italic dark:text-slate-400"><i class="fa-solid fa-circle-info mt-0.5"></i> Hệ thống tự động Failover.</p>
                </div>
                <div class="flex justify-end pt-2">
                    <button onclick="saveApiKey()" class="px-6 py-2.5 text-sm font-bold text-white bg-slate-900 hover:bg-black rounded-lg shadow-lg transition w-full md:w-auto dark:bg-red-600 dark:hover:bg-red-700">Lưu Cấu Hình</button>
                </div>
            </div>
        </div>
    </div>

    <!-- NICHE MODAL -->
    <div id="nicheDetailsModal" class="modal fixed inset-0 z-[9000] flex items-center justify-center opacity-0 invisible pointer-events-none">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeNicheModal()"></div>
        <div class="bg-white w-full max-w-2xl mx-4 rounded-2xl shadow-2xl z-10 p-0 relative transform transition-all scale-95 flex flex-col max-h-[90vh] overflow-hidden dark:bg-dark-card">
            <!-- Modal Content here (classes update dynamically in JS or keep neutral) -->
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50 dark:bg-slate-800 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 text-white p-2 rounded-lg"><i class="fa-solid fa-microscope"></i></div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight dark:text-white" id="modalNicheTitle">NGÁCH ABC</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Phân tích chi tiết</p>
                    </div>
                </div>
                <button onclick="closeNicheModal()" class="text-slate-400 hover:text-red-600 transition p-2"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="p-6 overflow-y-auto">
                <!-- Dynamic Content injected via JS needs dark mode classes in JS logic -->
                <div class="mb-6">
                    <h4 class="text-xs font-bold text-slate-400 uppercase mb-2">💎 Chiến Lược</h4>
                    <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-indigo-900 font-medium text-sm dark:bg-indigo-900/30 dark:border-indigo-800 dark:text-indigo-300" id="modalStrategy">--</div>
                </div>
                <!-- ... other modal parts ... -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-slate-800 dark:border-slate-700">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2"><i class="fa-solid fa-crown text-yellow-500"></i> Top Channel</h4>
                        <div id="modalTopChannel" class="dark:text-white">--</div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm dark:bg-slate-800 dark:border-slate-700">
                         <h4 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2"><i class="fa-solid fa-key text-orange-500"></i> Key Video</h4>
                        <div id="modalKeyVideo" class="dark:text-white">--</div>
                    </div>
                </div>
                
                 <div class="mb-6 mt-6">
                    <h4 class="text-xs font-bold text-slate-400 uppercase mb-2">📡 Các kênh tốt</h4>
                    <div id="modalChannelList" class="flex flex-wrap gap-2">--</div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 text-right dark:bg-slate-800 dark:border-slate-700">
                <button onclick="closeNicheModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-bold transition dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600">Đóng</button>
            </div>
        </div>
    </div>

    <script src="{{asset('js/main.js')}}"></script>
</body>
</html>