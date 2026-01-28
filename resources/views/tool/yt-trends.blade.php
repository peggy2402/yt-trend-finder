<!DOCTYPE html>
<html lang="vi" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZTGroup Analytics | Intelligence Core V5.0</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        surface: '#1F1F1F',
                        dark: '#0F0F0F'
                    }
                }
            }
        }
    </script>
</head>

<body
    class="bg-[#F3F4F6] text-slate-800 dark:bg-dark dark:text-slate-300 font-sans antialiased min-h-screen flex flex-col transition-colors duration-300">
    <!-- Header -->
    <header
        class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-white/5 h-16">
        <div class="container mx-auto px-4 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/"
                    class="w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 dark:bg-white/5 hover:bg-gray-200 text-slate-600 dark:text-slate-300"><i
                        class="fa-solid fa-arrow-left"></i></a>
                <div class="flex items-center gap-2 group">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center text-white font-bold shadow-lg shadow-red-900/20">
                        <i class="fa-brands fa-youtube"></i>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">ZTGroup <span
                            class="text-red-500 font-black">Analytics V5</span></span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.documentElement.classList.toggle('dark')"
                    class="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center text-slate-600 dark:text-slate-300"><i
                        class="fa-solid fa-circle-half-stroke"></i></button>
                <div class="relative group">
                    <button onclick="toggleSettingsModal()"
                        class="w-9 h-9 rounded-full bg-gray-100 dark:bg-white/5 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:rotate-90 transition-transform"><i
                            class="fa-solid fa-gear"></i></button>
                    <span id="activeKeyCountBadge"
                        class="hidden absolute -top-1 -right-1 h-5 w-5 rounded-full bg-green-500 text-[10px] text-white font-bold flex items-center justify-center">0</span>
                </div>
            </div>
        </div>
    </header>
    <main class="pt-24 pb-12 flex-grow">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Sidebar -->
                <aside class="lg:col-span-3 space-y-6">
                    <div
                        class="bg-white dark:bg-surface rounded-2xl p-5 border border-gray-200 dark:border-white/5 shadow-xl">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">MÁY CHỦ API
                                    KEY</label>
                                <div class="relative">
                                    <input type="password" id="apiKeyInput" placeholder="Thêm key mới..."
                                        class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-3 text-sm focus:border-red-500/50 outline-none">
                                    <button onclick="saveApiKey()"
                                        class="absolute right-3 top-2.5 text-[10px] font-bold text-red-500 hover:underline">LƯU</button>
                                </div>
                                <div id="apiKeyStatus" class="mt-1 text-[10px] text-slate-500">Chưa nhập key</div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">TỪ KHÓA
                                    NGÁCH</label>
                                <input type="text" id="searchInput" placeholder="VD: Street Food, AI..."
                                    class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-3 text-sm focus:border-red-500/50 outline-none">
                            </div>
                            <!-- Region & Time Filter (Yêu cầu #2, #3) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">QUỐC
                                        GIA</label>
                                    <select id="regionSelect"
                                        class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-2 text-sm outline-none"></select>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">THỜI
                                        GIAN</label>
                                    <select id="timeFilter"
                                        class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-2 text-sm outline-none">
                                        <option value="1h">1 Giờ qua</option>
                                        <option value="24h" selected>24 Giờ qua</option>
                                        <option value="7d">7 Ngày qua</option>
                                        <option value="30d">30 Ngày qua</option>
                                        <option value="all">Toàn thời gian</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase block mb-1.5">GIỚI HẠN KẾT
                                    QUẢ</label>
                                <select id="maxResultsFilter"
                                    class="w-full bg-gray-50 dark:bg-dark border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-3 text-sm outline-none">
                                    <option value="25">25 (Nhanh)</option>
                                    <option value="50" selected>50 (Chuẩn)</option>
                                    <option value="100">100 (Sâu)</option>
                                </select>
                            </div>
                            <button id="analyzeBtn"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2 uppercase text-xs tracking-widest">
                                <i class="fa-solid fa-rocket"></i> PHÂN TÍCH NGAY
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <div class="lg:col-span-9 space-y-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white dark:bg-surface p-4 rounded-2xl border dark:border-white/5 shadow-md">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Tổng Video</div>
                            <div class="text-2xl font-black text-gray-900 dark:text-white" id="statTotalVideos">0</div>
                        </div>
                        <div class="bg-white dark:bg-surface p-4 rounded-2xl border dark:border-white/5 shadow-md">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Avg Views</div>
                            <div class="text-2xl font-black text-gray-900 dark:text-white" id="statAvgViews">0</div>
                        </div>
                        <div class="bg-white dark:bg-surface p-4 rounded-2xl border dark:border-white/5 shadow-md">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Hiệu suất V/S</div>
                            <div class="text-2xl font-black text-indigo-500" id="statOpportunity">--</div>
                        </div>
                        <div class="bg-white dark:bg-surface p-4 rounded-2xl border dark:border-white/5 shadow-md">
                            <div class="text-[10px] font-bold text-slate-500 uppercase">Doanh thu ($)</div>
                            <div class="text-2xl font-black text-yellow-500" id="statEstRevenue">$0</div>
                        </div>
                    </div>

                    <!-- Strategy Section (Yêu cầu #4: Advanced Analysis) -->
                    <div id="strategySection" class="hidden animate__animated animate__fadeInUp space-y-6">
                        <div
                            class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 border border-indigo-200 dark:border-indigo-500/30 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
                            <h3
                                class="text-indigo-700 dark:text-indigo-300 font-black text-xl mb-6 flex items-center gap-2 border-b border-indigo-200 dark:border-white/5 pb-4 uppercase">
                                <i class="fa-solid fa-brain animate-pulse"></i> PHÂN TÍCH CHUYÊN SÂU
                            </h3>

                            <!-- Top Blocks: Micro Niche & Cluster -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10 mb-6">
                                <div
                                    class="bg-white dark:bg-black/40 p-6 rounded-2xl border border-indigo-100 dark:border-white/5 shadow-sm">
                                    <div
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 flex items-center gap-2">
                                        <i class="fa-solid fa-fire text-red-500"></i> NGÁCH HOT ĐỀ XUẤT CHO: <span
                                            id="strategyKeywordBase" class="text-indigo-600">--</span>
                                    </div>
                                    <div id="strategyMicroNiche"
                                        class="text-gray-900 dark:text-white font-black text-xl pt-2">--</div>
                                </div>
                                <div
                                    class="bg-white dark:bg-black/40 p-6 rounded-2xl border border-pink-100 dark:border-white/5 shadow-sm">
                                    <div
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-layer-group text-pink-500"></i> CÁC CỤM CHỦ ĐỀ (CLUSTERS)
                                    </div>
                                    <div id="strategyCluster"></div>
                                </div>
                            </div>

                            <!-- Bottom Blocks: Score, Time, King Keyword (New Metrics) -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
                                <!-- Niche Score -->
                                <div
                                    class="bg-white/70 dark:bg-black/50 p-5 rounded-2xl border border-indigo-100 dark:border-white/5">
                                    <div class="text-[10px] text-slate-500 uppercase mb-2 font-black">📈 ĐIỂM TIỀM NĂNG
                                        (SCORE)</div>
                                    <div class="flex items-baseline gap-2">
                                        <div id="nicheScoreValue"
                                            class="text-3xl font-black text-gray-900 dark:text-white">--</div>
                                    </div>
                                    <div id="nicheRatingText" class="text-xs font-bold mt-1">--</div>
                                </div>

                                <!-- Best Time -->
                                <div
                                    class="bg-white/70 dark:bg-black/50 p-5 rounded-2xl border border-indigo-100 dark:border-white/5">
                                    <div class="text-[10px] text-slate-500 uppercase mb-2 font-black">⏰ KHUNG GIỜ ĐĂNG
                                        HIỆU QUẢ</div>
                                    <div id="bestUploadTime"
                                        class="text-2xl font-black text-indigo-600 dark:text-indigo-400">--</div>
                                    <div class="text-[9px] text-slate-400 mt-1">Dựa trên giờ đăng của Top Videos</div>
                                </div>

                                <!-- King Keyword -->
                                <div
                                    class="bg-white/70 dark:bg-black/50 p-5 rounded-2xl border border-indigo-100 dark:border-white/5">
                                    <div class="text-[10px] text-slate-500 uppercase mb-2 font-black">👑 TỪ KHÓA VUA
                                        (KING KEYWORD)</div>
                                    <div id="strategyKingKeyword"
                                        class="text-xl font-black text-indigo-600 dark:text-indigo-400 truncate">--
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Channels Market -->
                        <div
                            class="bg-white dark:bg-surface rounded-3xl p-8 border border-gray-200 dark:border-white/5 shadow-2xl">
                            <h3
                                class="text-gray-900 dark:text-white font-black text-xl mb-6 flex items-center gap-3 uppercase border-b dark:border-white/5 pb-4 tracking-tighter">
                                <i class="fa-solid fa-trophy text-yellow-500 text-2xl animate-pulse"></i> TOP CHANNELS
                                THỐNG TRỊ NGÁCH
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="text-[9px] uppercase font-black text-slate-400 dark:text-slate-500 border-b border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
                                            <th class="p-4 rounded-tl-2xl">Kênh YouTube</th>
                                            <th class="p-4 text-right hidden sm:table-cell">Subscribers</th>
                                            <!-- Ẩn trên Mobile -->
                                            <th class="p-4 text-right">Tổng Lượt Xem</th>
                                            <th class="p-4 text-center rounded-tr-2xl hidden sm:table-cell">Phân tích
                                            </th> <!-- Ẩn trên Mobile -->
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="text-sm divide-y divide-gray-100 dark:divide-white/5 text-gray-700 dark:text-slate-300 font-black"
                                        id="topChannelsBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div
                        class="bg-white dark:bg-surface rounded-2xl border border-gray-200 dark:border-white/5 overflow-hidden shadow-xl transition-all duration-300">
                        <div
                            class="p-5 border-b border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-white/5 flex flex-wrap items-center justify-between gap-4">
                            <div class="font-black uppercase text-sm text-gray-700 dark:text-gray-300 tracking-tight">
                                <i class="fa-solid fa-list-ul mr-2 text-red-500"></i> KẾT QUẢ CHI TIẾT
                            </div>
                            <!-- FILTER & EXPORT BUTTONS -->
                            <div class="flex items-center gap-2">
                                <button onclick="exportCSV()"
                                    class="hidden sm:flex items-center gap-1 px-3 py-1.5 text-[10px] font-black bg-white dark:bg-white/10 text-gray-800 dark:text-white rounded-lg border border-gray-200 dark:border-white/10 hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fa-solid fa-download"></i> CSV
                                </button>
                                <select onchange="sortVideos(this.value)"
                                    class="bg-white dark:bg-dark border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-white py-1.5 px-3 rounded-lg focus:outline-none focus:border-red-500 transition-colors cursor-pointer">
                                    <option value="views_desc">📈 Views (Cao - Thấp)</option>
                                    <option value="views_asc">📉 Views (Thấp - Cao)</option>
                                    <option value="vs_desc">💎 V/S Ratio (Cao - Thấp)</option>
                                    <option value="rpm_desc">💰 RPM Cao - Thấp</option>
                                    <option value="date_new">📅 Mới nhất</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table Wrapper -->
                        <div class="overflow-x-auto min-h-[400px]">
                            <table class="w-full text-left border-collapse">
                                <thead
                                    class="bg-gray-50 dark:bg-dark/50 sticky top-0 z-10 text-[9px] uppercase font-black text-slate-500 shadow-sm hidden md:table-header-group">
                                    <tr>
                                        <th class="p-4 w-10 text-center hidden md:table-cell">#</th>
                                        <th class="p-4 w-auto">Video / Kênh</th>
                                        <!-- Desktop only headers -->
                                        <th class="p-4 text-right hidden md:table-cell">Lượt xem</th>
                                        <th class="p-4 text-right hidden md:table-cell" title="Views/Subs Ratio">V/S
                                            Ratio</th>
                                        <th class="p-4 text-right hidden md:table-cell">$$$ (Est)</th>
                                        <th class="p-4 text-right hidden lg:table-cell">Thời gian</th>
                                        <th class="p-4 text-center hidden sm:table-cell">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="resultsBody"
                                    class="text-sm divide-y divide-gray-100 dark:divide-white/5 text-gray-700 dark:text-slate-300 font-medium p-4 md:p-0 block md:table-row-group">
                                    <!-- Rows sẽ được JS Render dạng Card trên Mobile -->
                                    <tr>
                                        <td colspan="6" class="p-10 text-center text-slate-500 italic">Sẵn sàng
                                            phân tích...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- CONTAINER PHÂN TRANG MỚI -->
                        <div id="pagination" class="bg-white dark:bg-surface"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Settings Modal with Close Button (Yêu cầu #1) -->
    <div id="settingsModal"
        class="fixed inset-0 z-[70] invisible opacity-0 pointer-events-none transition-all duration-300 flex items-center justify-center p-4">
        <!-- Overlay Click to Close -->
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="closeSettingsModal()"></div>
        <div class="bg-white dark:bg-surface w-full max-w-lg rounded-[2rem] p-8 shadow-2xl relative z-10 transition-transform scale-95 duration-200"
            id="modalContainer">
            <!-- Close Button -->
            <button onclick="closeSettingsModal()"
                class="absolute top-6 right-6 w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:bg-red-500 hover:text-white transition-all">
                <i class="fa-solid fa-times"></i>
            </button>

            <h3 class="text-2xl font-black text-center mb-6 text-gray-900 dark:text-white">CẤU HÌNH HỆ THỐNG</h3>
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-black text-slate-500 mb-2 uppercase">RPM ($/1000 views)</label>
                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-dark p-4 rounded-xl">
                        <input type="range" id="rpmSlider" min="0.1" max="10" step="0.1"
                            value="0.3" class="flex-grow" oninput="updateRpmLive(this.value)">
                        <span id="rpmValue" class="text-xl font-black text-red-600">$0.3</span>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-xs font-black text-slate-500 uppercase">DANH SÁCH API KEY</label>
                        <button onclick="checkKeysHealth()" id="btnCheckKeys"
                            class="text-[10px] bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full font-bold hover:bg-indigo-100 transition-colors">
                            <i class="fa-solid fa-stethoscope mr-1"></i> KIỂM TRA KEY
                        </button>
                    </div>

                    <textarea id="apiKeyList" rows="5"
                        class="w-full bg-gray-50 dark:bg-dark border dark:border-white/10 rounded-xl p-4 text-xs font-mono mb-2 focus:border-red-500/50 focus:outline-none transition-colors"
                        placeholder="Dán key vào đây, mỗi dòng một Key..."></textarea>

                    <!-- Khu vực hiển thị kết quả Test Key -->
                    <div id="keyCheckResult"
                        class="hidden space-y-2 max-h-40 overflow-y-auto p-2 bg-gray-50 dark:bg-black/20 rounded-lg border border-gray-100 dark:border-white/5">
                        <!-- JS sẽ render kết quả vào đây -->
                    </div>
                </div>

                <button onclick="saveSettings()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl uppercase tracking-widest shadow-lg shadow-red-900/20 transition-all active:scale-95">
                    LƯU CÀI ĐẶT
                </button>
            </div>
        </div>
    </div>
    <!-- SMART DETAIL MODAL (New Feature) -->
    <div id="detailModal"
        class="fixed inset-0 z-[100] invisible opacity-0 pointer-events-none transition-all duration-300">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="Renderer.closeModal()"></div>

        <!-- Modal Content Wrapper -->
        <div class="absolute inset-0 flex items-end sm:items-center justify-center p-0 sm:p-4">
            <!-- Modal Card -->
            <div id="detailModalContent"
                class="bg-white dark:bg-[#1A1A1A] w-full sm:max-w-2xl h-[85vh] sm:h-auto sm:max-h-[85vh] rounded-t-[2rem] sm:rounded-3xl shadow-2xl transform translate-y-full sm:translate-y-10 scale-95 transition-all duration-300 flex flex-col overflow-hidden">

                <!-- Header -->
                <div
                    class="p-6 border-b border-gray-100 dark:border-white/5 flex items-center justify-between flex-shrink-0 bg-white dark:bg-[#1A1A1A] z-10">
                    <h3 id="modalTitle"
                        class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight line-clamp-1 pr-4">
                        CHI TIẾT</h3>
                    <button onclick="Renderer.closeModal()"
                        class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center text-slate-500 hover:bg-red-500 hover:text-white transition-all">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Scrollable Body -->
                <div id="modalBody" class="p-6 overflow-y-auto custom-scrollbar flex-grow space-y-6">
                    <!-- Nội dung sẽ được JS inject vào đây -->
                </div>

                <!-- Footer (Optional) -->
                <div
                    class="p-4 border-t border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-black/20 text-center flex-shrink-0">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Powered by ZTGroup AI
                        Intelligence</span>
                </div>
            </div>
        </div>
    </div>
    <div id="toast-container" class="fixed bottom-5 right-5 z-[80]"></div>

    <!-- SCRIPTS -->
    <script src="{{ asset('js/core-tool-yt-trends/config.js') }}"></script>
    <script src="{{ asset('js/core-tool-yt-trends/keyManager.js') }}"></script>
    <script src="{{ asset('js/core-tool-yt-trends/ytService.js') }}"></script>
    <script src="{{ asset('js/core-tool-yt-trends/analyzer.js') }}"></script>
    <script src="{{ asset('js/core-tool-yt-trends/renderer.js') }}"></script>
    <script src="{{ asset('js/core-tool-yt-trends/app.js') }}"></script>
</body>

</html>
