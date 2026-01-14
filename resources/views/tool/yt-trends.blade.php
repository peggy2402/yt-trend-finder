<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZTGroup Analytics | Ultimate Hunter V4.3</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        primary: '#FF0000',
                        dark: '#0F0F0F',
                        surface: '#1F1F1F',
                        light: '#F3F4F6',
                        'surface-light': '#FFFFFF'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-light text-slate-800 dark:bg-dark dark:text-slate-300 font-sans antialiased min-h-screen flex flex-col selection:bg-red-500 selection:text-white transition-colors duration-300">

    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-white/5 h-16 transition-colors duration-300">
        <div class="container mx-auto px-4 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>

                <a href="/" class="flex items-center gap-2 group ml-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center text-white font-bold shadow-lg shadow-red-900/20 group-hover:scale-110 transition-transform">
                        <i class="fa-brands fa-youtube"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">ZTGroup <span class="text-red-500">Analytics</span></span>
                </a>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/10 transition-colors text-slate-600 dark:text-slate-300 shadow-sm border border-gray-200 dark:border-white/5">
                    <i id="themeIcon" class="fa-solid fa-moon"></i>
                </button>

                <div class="relative group">
                    <button onclick="toggleSettingsModal()" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-white/10 transition-colors text-slate-600 dark:text-slate-300 shadow-sm border border-gray-200 dark:border-white/5">
                        <i class="fa-solid fa-gear group-hover:rotate-90 transition-transform duration-500"></i>
                    </button>
                    <span id="activeKeyCountBadge" class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-green-500 text-[10px] text-white font-bold shadow-sm hidden animate__animated animate__bounceIn">
                        0
                    </span>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-24 pb-12 flex-grow">
        <div class="container mx-auto px-4">
            
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Sidebar (Filter) -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="bg-surface-light dark:bg-surface rounded-2xl p-5 border border-gray-200 dark:border-white/5 shadow-xl transition-colors duration-300">
                        <h3 class="text-gray-900 dark:text-white font-bold mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-filter text-red-500"></i> Bộ Lọc Tìm Kiếm
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- API Key Input -->
                            <div>
                                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase mb-1.5 block">YouTube API Key <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fa-solid fa-key absolute left-3 top-3 text-slate-400 dark:text-slate-500 text-sm"></i>
                                    <input type="password" id="apiKeyInput" placeholder="Dán API Key vào đây..." class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 pl-9 pr-8 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-600">
                                    <button onclick="toggleApiKeyVisibility()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                                        <i id="apiKeyToggleIcon" class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <span id="apiKeyStatus" class="text-[10px] text-slate-500">Chưa nhập key</span>
                                    <button onclick="saveApiKey()" class="text-[10px] text-red-500 hover:text-red-400 font-medium hover:underline">Lưu Key</button>
                                </div>
                            </div>

                            <!-- Keyword Input -->
                            <div>
                                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase mb-1.5 block">Từ khóa chính</label>
                                <div class="relative">
                                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 dark:text-slate-500 text-sm"></i>
                                    <input type="text" id="searchInput" placeholder="Nhập chủ đề (vd: Street Food)..." class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 pl-9 pr-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-red-500/50 focus:ring-1 focus:ring-red-500/50 transition-all placeholder:text-slate-400 dark:placeholder:text-slate-600">
                                </div>
                            </div>

                            <!-- Filters: Country & Time -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase mb-1.5 block">Quốc gia</label>
                                    <select id="regionSelect" class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-red-500/50 appearance-none">
                                        <!-- Will be populated by JS -->
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase mb-1.5 block">Thời gian</label>
                                    <select id="timeFilter" class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-red-500/50 appearance-none">
                                        <option value="hour">⚡ 1 Giờ qua</option>
                                        <option value="today" selected>📅 24h qua</option>
                                        <option value="week">🗓 7 ngày qua</option>
                                        <option value="month">📆 30 ngày qua</option>
                                        <option value="year">📅 1 năm qua</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Max Results (Deep Scan) -->
                            <div>
                                <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase mb-1.5 block">Số lượng quét (Deep Scan)</label>
                                <select id="maxResultsFilter" class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:border-red-500/50 appearance-none">
                                    <option value="50">50 Videos (Nhanh)</option>
                                    <option value="100">100 Videos (Chuẩn)</option>
                                    <option value="200">200 Videos (Sâu)</option>
                                    <option value="250">250 Videos (Tối đa)</option>
                                </select>
                            </div>
                            
                            <button onclick="startAnalysis()" id="analyzeBtn" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-red-900/30 active:scale-95 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-rocket"></i> Phân Tích Ngay
                            </button>
                        </div>
                    </div>

                    <!-- Trending Niche Keywords (REAL DATA) -->
                    <div class="bg-surface-light dark:bg-surface rounded-2xl p-5 border border-gray-200 dark:border-white/5 shadow-xl transition-colors duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-gray-900 dark:text-white font-bold flex items-center gap-2">
                                <i class="fa-solid fa-fire text-orange-500 animate-pulse"></i> Ngách Hot
                            </h3>
                            <span class="text-[10px] bg-orange-500/10 text-orange-500 dark:text-orange-400 px-2 py-0.5 rounded border border-orange-500/20">Realtime API</span>
                        </div>
                        
                        <div class="space-y-3" id="nicheKeywordsList">
                            <div class="text-center py-6 text-slate-500 text-xs">
                                <div class="animate-pulse">
                                    <i class="fa-brands fa-youtube text-2xl mb-2 text-slate-300 dark:text-slate-700"></i>
                                </div>
                                <p>Nhập API Key để xem Trends thật</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-white/5 text-center">
                            <button onclick="fetchRealNicheTrends()" class="text-xs text-slate-500 hover:text-red-500 dark:text-slate-400 dark:hover:text-white flex items-center justify-center gap-1 mx-auto transition-colors">
                                <i class="fa-solid fa-sync mr-1"></i> Cập nhật Trend
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Center Content (Results) -->
                <div class="lg:col-span-9 space-y-6">
                    
                    <!-- Stats Overview -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-surface-light dark:bg-surface p-4 rounded-2xl border border-gray-200 dark:border-white/5 metric-card transition-colors duration-300">
                            <div class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase mb-2">Tổng Video Quét</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white truncate" id="statTotalVideos">0</div>
                            <div class="text-green-500 text-xs mt-1 font-medium"><i class="fa-solid fa-arrow-up"></i> Live scan</div>
                        </div>
                        <div class="bg-surface-light dark:bg-surface p-4 rounded-2xl border border-gray-200 dark:border-white/5 metric-card transition-colors duration-300">
                            <div class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase mb-2">Avg. Views</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white truncate" id="statAvgViews">0</div>
                            <div class="text-slate-500 text-xs mt-1 font-medium">Trung bình</div>
                        </div>
                         <div class="bg-surface-light dark:bg-surface p-4 rounded-2xl border border-gray-200 dark:border-white/5 metric-card transition-colors duration-300">
                            <div class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase mb-2">Cơ hội (Score)</div>
                            <div class="text-xl md:text-2xl font-bold text-green-500 dark:text-green-400 truncate" id="statOpportunity">--/10</div>
                            <div class="text-slate-500 text-xs mt-1 font-medium">Độ cạnh tranh</div>
                        </div>
                         <div class="bg-surface-light dark:bg-surface p-4 rounded-2xl border border-gray-200 dark:border-white/5 metric-card transition-colors duration-300">
                            <div class="text-slate-500 dark:text-slate-400 text-xs font-medium uppercase mb-2">RPM Ước tính</div>
                            <div class="text-2xl font-bold text-yellow-500 dark:text-yellow-400 truncate" id="statEstRevenue">$0</div>
                            <div class="text-slate-500 text-xs mt-1 font-medium">Dựa trên view</div>
                        </div>
                    </div>

                    <!-- Main Table/List -->
                    <div class="bg-surface-light dark:bg-surface rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden shadow-xl transition-colors duration-300">
                        <div class="p-5 border-b border-gray-200 dark:border-white/5 flex flex-wrap items-center justify-between gap-4">
                            <h3 class="text-gray-900 dark:text-white font-bold text-lg">Kết Quả Phân Tích</h3>
                            <div class="flex gap-2">
                                <button onclick="exportCSV()" class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 dark:bg-white/5 dark:hover:bg-white/10 text-gray-800 dark:text-white rounded-lg border border-gray-200 dark:border-white/5 transition-colors">
                                    <i class="fa-solid fa-download mr-1"></i> Export CSV
                                </button>
                                <select onchange="sortVideos(this.value)" class="px-3 py-1.5 text-xs font-medium bg-white dark:bg-dark hover:bg-gray-50 dark:hover:bg-white/5 text-gray-800 dark:text-white rounded-lg border border-gray-200 dark:border-white/10 focus:outline-none">
                                    <option value="views_desc">Views (Cao -> Thấp)</option>
                                    <option value="views_asc">Views (Thấp -> Cao)</option>
                                    <option value="date_desc">Mới nhất</option>
                                    <option value="subs_asc">Subs (Thấp -> Cao)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-dark/50 text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-gray-200 dark:border-white/5">
                                        <th class="p-4 font-semibold">Video Topic</th>
                                        <th class="p-4 font-semibold text-right">Views</th>
                                        <th class="p-4 font-semibold text-right">Revenue ($)</th>
                                        <th class="p-4 font-semibold text-right">Published</th>
                                        <th class="p-4 font-semibold text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y divide-gray-200 dark:divide-white/5 text-gray-700 dark:text-slate-300" id="resultsBody">
                                    <tr class="text-center">
                                        <td colspan="5" class="p-8 text-slate-500 italic">Vui lòng nhập từ khóa và bấm Phân Tích...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="p-4 border-t border-gray-200 dark:border-white/5 flex justify-center gap-2" id="pagination"></div>
                    </div>

                    <!-- --- NEW: STRATEGIC CONCLUSION SECTION (BELOW RESULTS) --- -->
                    <div id="strategySection" class="hidden animate__animated animate__fadeInUp">
                        <!-- 1. Strategy Box -->
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border border-indigo-100 dark:border-indigo-500/30 rounded-2xl p-6 relative overflow-hidden mb-6">
                            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-indigo-500/10 rounded-full blur-xl"></div>
                            
                            <h3 class="text-indigo-600 dark:text-indigo-400 font-bold text-xl mb-6 flex items-center gap-2 relative z-10">
                                <i class="fa-solid fa-chess-knight text-2xl"></i> KẾT LUẬN CHIẾN LƯỢC
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                                <!-- Micro Niche -->
                                <div class="bg-white dark:bg-black/20 p-4 rounded-xl border border-indigo-100 dark:border-white/5 shadow-sm">
                                    <div class="flex items-start gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 flex items-center justify-center text-indigo-500 flex-shrink-0">
                                            <i class="fa-solid fa-diamond"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Ngách Siêu Nhỏ</div>
                                            <div class="text-sm text-slate-400 dark:text-slate-500 mb-1">Dành cho: <span id="strategyKeywordBase" class="font-bold text-indigo-500">--</span></div>
                                        </div>
                                    </div>
                                    <div id="strategyMicroNiche" class="text-gray-900 dark:text-white font-bold text-lg leading-tight">Đang phân tích...</div>
                                </div>

                                <!-- Cluster Content -->
                                <div class="bg-white dark:bg-black/20 p-4 rounded-xl border border-pink-100 dark:border-white/5 shadow-sm">
                                    <div class="flex items-start gap-3 mb-2">
                                        <div class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-500/20 flex items-center justify-center text-pink-500 flex-shrink-0">
                                            <i class="fa-solid fa-network-wired"></i>
                                        </div>
                                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase pt-1">Gợi ý Cluster Chủ đề</div>
                                    </div>
                                    <div id="strategyCluster" class="text-gray-700 dark:text-slate-300 text-sm space-y-1">--</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 relative z-10">
                                <!-- King Keyword -->
                                <div class="bg-white/60 dark:bg-black/20 p-3 rounded-lg border border-indigo-100 dark:border-white/5">
                                    <div class="text-[10px] text-slate-500 uppercase mb-1 flex items-center gap-1">
                                        <i class="fa-solid fa-crown text-yellow-500"></i> Từ Khóa "Vua"
                                    </div>
                                    <div id="strategyKingKeyword" class="text-indigo-600 dark:text-indigo-400 font-bold text-lg truncate">--</div>
                                    <div class="text-[10px] text-slate-400">Lặp lại nhiều nhất</div>
                                </div>

                                <!-- Best Time -->
                                <div class="bg-white/60 dark:bg-black/20 p-3 rounded-lg border border-indigo-100 dark:border-white/5">
                                    <div class="text-[10px] text-slate-500 uppercase mb-1 flex items-center gap-1">
                                        <i class="fa-regular fa-clock text-orange-500"></i> Khung Giờ Vàng
                                    </div>
                                    <div id="strategyBestTime" class="text-orange-500 font-bold text-lg">--</div>
                                    <div class="text-[10px] text-slate-400">Giờ đăng hiệu quả</div>
                                </div>

                                <!-- Competition Score -->
                                <div class="bg-white/60 dark:bg-black/20 p-3 rounded-lg border border-indigo-100 dark:border-white/5">
                                    <div class="text-[10px] text-slate-500 uppercase mb-1 flex items-center gap-1">
                                        <i class="fa-solid fa-shield-halved text-green-500"></i> Độ Cạnh Tranh
                                    </div>
                                    <div id="strategyCompetition" class="text-green-500 font-bold text-lg">--</div>
                                    <div class="text-[10px] text-slate-400">Dựa trên volume</div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Top Channels Section -->
                        <div class="bg-surface-light dark:bg-surface rounded-2xl p-6 border border-gray-200 dark:border-white/5 shadow-xl transition-colors duration-300">
                            <h3 class="text-gray-900 dark:text-white font-bold text-lg mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-trophy text-yellow-500"></i> TOP CHANNELS THỐNG TRỊ
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="text-xs uppercase text-slate-500 dark:text-slate-400 border-b border-gray-200 dark:border-white/5">
                                            <th class="p-3">Kênh</th>
                                            <th class="p-3 text-right">Subscribers</th>
                                            <th class="p-3 text-right">Video Top</th>
                                            <th class="p-3 text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-gray-200 dark:divide-white/5 text-gray-700 dark:text-slate-300" id="topChannelsBody">
                                        <!-- JS Render -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- --- END NEW SECTION --- -->

                </div>
            </div>
        </div>
    </main>

    <!-- Niche Detail Modal (Simplified for Trends only) -->
    <div id="nicheModal" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-black/50 dark:bg-black/80 backdrop-blur-sm transition-opacity" onclick="closeNicheModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-surface-light dark:bg-surface w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 transform transition-all scale-100 flex flex-col transition-colors duration-300">
                <div class="p-6 border-b border-gray-200 dark:border-white/5 flex justify-between items-start">
                    <div>
                         <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1" id="modalTitle">Trend Details</h3>
                    </div>
                    <button onclick="closeNicheModal()" class="text-slate-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">Kênh</label>
                            <div id="modalTopChannel" class="text-gray-900 dark:text-white font-medium">--</div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">Tags</label>
                            <div id="modalChannelList" class="flex flex-wrap gap-2 mt-1">--</div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-dark/50 text-right">
                    <button onclick="closeNicheModal()" class="px-4 py-2 bg-gray-200 dark:bg-white/10 hover:bg-gray-300 dark:hover:bg-white/20 text-gray-700 dark:text-white rounded-lg text-sm font-medium transition-colors">Đóng</button>
                    <button onclick="startAnalysisFromNiche()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium ml-2 shadow-lg shadow-red-500/30 transition-all">Phân tích sâu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast-container"></div>

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal fixed inset-0 z-[70] invisible opacity-0">
        <div class="absolute inset-0 bg-black/50 dark:bg-black/80 backdrop-blur-sm transition-opacity" onclick="toggleSettingsModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-surface w-full max-w-md rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 p-6 transition-colors duration-300">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Cài Đặt Hệ Thống</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">RPM (Doanh thu/1000 views)</label>
                        <div class="flex items-center gap-4">
                            <input type="range" id="rpmSlider" min="0.1" max="10" step="0.1" value="0.3" class="flex-grow h-2 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer">
                            <span id="rpmValue" class="text-gray-900 dark:text-white font-bold w-12 text-right">$0.3</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-2">Quản lý API Key (Nhập mỗi dòng 1 key)</label>
                        <textarea id="apiKeyList" rows="4" class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl p-3 text-xs text-gray-900 dark:text-white focus:outline-none focus:border-red-500/50 font-mono" placeholder="AIzaSy..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button onclick="toggleSettingsModal()" class="px-4 py-2 text-sm text-slate-500 hover:text-gray-900 dark:text-slate-400 dark:hover:text-white">Hủy</button>
                    <button onclick="saveSettings()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-bold shadow-lg shadow-red-900/20">Lưu Cài Đặt</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Logic Script -->
    <script src="{{ asset('js/main.js') }}"></script>
</body>
</html>