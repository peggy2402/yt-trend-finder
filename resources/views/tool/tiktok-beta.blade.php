<!DOCTYPE html>
<html lang="vi" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="no-referrer">
    <title>TikTok Beta Hunter | ZENTRA SaaS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        tiktokCyan: '#00f2ea',
                        tiktokPink: '#ff0050',
                        surface: '#121212',
                        dark: '#000000',
                        card: '#1e1e1e'
                    },
                    animation: {
                        'radar': 'radar 2s linear infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        radar: {
                            '0%': {
                                transform: 'scale(0.95)',
                                opacity: '0.5'
                            },
                            '100%': {
                                transform: 'scale(2)',
                                opacity: '0'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        select option {
            background-color: #121212;
            color: #fff;
        }
    </style>
</head>

<body class="bg-dark text-slate-300 font-sans antialiased min-h-screen flex flex-col" x-data="tiktokApp()">

    <!-- Navbar -->
    <nav class="border-b border-white/10 bg-surface/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="font-black text-lg md:text-xl tracking-tighter text-white flex items-center gap-2">
                <div class="relative">
                    <i class="fa-brands fa-tiktok text-tiktokPink text-2xl relative z-10"></i>
                    <div class="absolute inset-0 bg-tiktokCyan blur-lg opacity-50 animate-pulse-slow"></div>
                </div>
                <span class="hidden md:inline">ZENTRA <span class="text-tiktokCyan">HUNTER</span></span>
            </div>

            <div class="flex items-center gap-3 md:gap-6">
                <button @click="showPricing = true"
                    class="group relative flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-tiktokPink/10 to-orange-500/10 border border-tiktokPink/50 hover:border-tiktokPink transition-all overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-tiktokPink to-orange-600 opacity-0 group-hover:opacity-20 transition-opacity">
                    </div>
                    <i class="fa-solid fa-crown text-tiktokPink group-hover:animate-bounce"></i>
                    <span class="text-xs font-bold text-white uppercase tracking-wider">Nâng cấp PRO</span>
                </button>

                <div class="relative">
                    <button @click="toggleProfile()" @click.outside="closeProfile()"
                        class="flex items-center gap-3 focus:outline-none hover:bg-white/5 p-1 rounded-full transition-colors border border-transparent hover:border-white/10">
                        <span class="text-sm font-bold text-white hidden sm:block text-right leading-tight">
                            {{ Auth::user()->name }} <br>
                            <span class="text-[10px] text-tiktokCyan font-normal">Số dư:
                                <span x-text="formatNumberVND(userBalance)"></span></span>
                        </span>
                        <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=00f2ea&color=000"
                            class="w-9 h-9 rounded-full border-2 border-surface ring-2 ring-white/10">
                    </button>

                    <div x-show="isProfileOpen" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                        class="absolute right-0 mt-3 w-64 bg-[#1a1a1a] border border-white/10 rounded-2xl shadow-2xl py-2 z-[100]"
                        x-cloak>

                        <div class="px-4 py-3 border-b border-white/5 mb-2">
                            <p class="text-xs text-slate-500 uppercase font-bold">Ví tiền</p>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xl font-black text-white"
                                    x-text="formatNumberVND(userBalance)"></span>
                            </div>
                        </div>

                        <a href="{{ route('deposit') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-colors group">
                            <div
                                class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-500 group-hover:bg-green-500 group-hover:text-white transition-all">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            Nạp tiền vào ví
                        </a>
                        <a href="{{ route('history') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:bg-white/5 hover:text-white transition-colors group">
                            <div
                                class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>
                            Lịch sử giao dịch
                        </a>
                        <div class="h-px bg-white/5 my-2 mx-4"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-red-500/10 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                </div>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-8 relative">

        <!-- Loading Overlay -->
        <div x-show="loading"
            class="fixed inset-0 z-[999] bg-dark/95 backdrop-blur-md flex flex-col items-center justify-center"
            x-transition.opacity x-cloak>
            <div class="relative w-32 h-32 flex items-center justify-center mb-8">
                <div
                    class="absolute inset-0 border-2 border-tiktokCyan/30 rounded-full animate-[spin_3s_linear_infinite]">
                </div>
                <div
                    class="absolute inset-4 border-2 border-tiktokPink/30 rounded-full animate-[spin_4s_linear_infinite_reverse]">
                </div>
                <div class="absolute inset-0 border-t-4 border-tiktokCyan rounded-full animate-spin"></div>
                <i class="fa-brands fa-tiktok text-5xl text-white animate-bounce"></i>
            </div>
            <h3 class="text-2xl font-black text-white mb-2 tracking-tight">ĐANG XỬ LÝ</h3>
            <p class="text-sm text-tiktokCyan font-mono" x-text="loadingText">Vui lòng chờ...</p>
        </div>

        <!-- User Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-surface border border-white/10 rounded-2xl p-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-3 opacity-10"><i class="fa-solid fa-id-card text-6xl"></i></div>
                <p class="text-xs text-slate-500 uppercase font-bold mb-1">Gói hiện tại</p>
                <h3 class="text-2xl font-bold text-white mb-1" x-text="usage.plan_name">Loading...</h3>
                <p class="text-xs flex items-center gap-1"
                    :class="usage.is_expired ? 'text-red-400' : 'text-orange-400'">
                    <i class="fa-regular fa-clock"></i> <span x-text="usage.expiry_text">Đang tải...</span>
                </p>
            </div>
            <div class="bg-surface border border-white/10 rounded-2xl p-5">
                <div class="flex justify-between items-end mb-2">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold mb-1">Lượt quét hôm nay</p>
                        <h3 class="text-2xl font-bold text-tiktokCyan"><span x-text="usage.used">0</span><span
                                class="text-slate-500 text-lg">/<span x-text="usage.limit">...</span></span></h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-400">Còn lại</p>
                        <p class="font-bold text-white" x-text="Math.max(0, usage.limit - usage.used)">...</p>
                    </div>
                </div>
                <div class="w-full bg-white/10 rounded-full h-2 mt-2">
                    <div class="bg-gradient-to-r from-tiktokCyan to-blue-500 h-2 rounded-full transition-all duration-500"
                        :style="'width: ' + Math.min(100, (usage.used / usage.limit) * 100) + '%'"></div>
                </div>
            </div>
            <div class="bg-surface border border-white/10 rounded-2xl p-5 flex flex-col justify-center items-center text-center cursor-pointer hover:border-tiktokPink/50 transition-colors group shadow-[0_0_15px_rgba(0,242,234,0.1)]"
                @click="getAiKeywords()">
                <div
                    class="w-10 h-10 rounded-full bg-tiktokPink/20 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <i class="fa-solid text-tiktokPink"
                        :class="aiLoading ? 'fa-circle-notch fa-spin' : 'fa-wand-magic-sparkles'"></i>
                </div>
                <p class="text-sm font-bold text-white group-hover:text-tiktokCyan"
                    x-text="aiLoading ? 'Đang phân tích...' : 'Gợi ý Keyword AI'"></p>
                <p class="text-xs text-slate-500" x-show="!aiLoading">Bấm để lấy từ khóa hot theo vùng</p>
            </div>
        </div>

        <!-- Filter Area -->
        <div class="bg-card border border-white/10 rounded-3xl p-6 shadow-xl mb-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-4">
                    <label class="block text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-wider">Thị trường
                        mục tiêu</label>
                    <div class="relative">
                        <select x-model="params.region"
                            class="w-full bg-surface border border-white/10 rounded-xl px-4 py-3.5 text-white text-sm font-bold focus:border-tiktokCyan focus:ring-1 focus:ring-tiktokCyan outline-none appearance-none cursor-pointer">
                            <optgroup label="🔥 Phổ biến nhất">
                                <option value="US">🇺🇸 Hoa Kỳ (USA)</option>
                                <option value="VN">🇻🇳 Việt Nam</option>
                                <option value="JP">🇯🇵 Nhật Bản</option>
                                <option value="KR">🇰🇷 Hàn Quốc</option>
                                <option value="GB">🇬🇧 Anh Quốc (UK)</option>
                            </optgroup>
                            <optgroup label="🌍 Châu Á">
                                <option value="ID">🇮🇩 Indonesia</option>
                                <option value="TH">🇹🇭 Thái Lan</option>
                                <option value="PH">🇵🇭 Philippines</option>
                                <option value="MY">🇲🇾 Malaysia</option>
                                <option value="SG">🇸🇬 Singapore</option>
                                <option value="TW">🇹🇼 Đài Loan</option>
                                <option value="IN">🇮🇳 Ấn Độ</option>
                            </optgroup>
                            <optgroup label="🌍 Châu Âu">
                                <option value="DE">🇩🇪 Đức (Germany)</option>
                                <option value="FR">🇫🇷 Pháp (France)</option>
                                <option value="IT">🇮🇹 Ý (Italy)</option>
                                <option value="ES">🇪🇸 Tây Ban Nha</option>
                                <option value="RU">🇷🇺 Nga</option>
                                <option value="UA">🇺🇦 Ukraine</option>
                            </optgroup>
                            <optgroup label="🌍 Châu Mỹ & Úc">
                                <option value="BR">🇧🇷 Brazil</option>
                                <option value="MX">🇲🇽 Mexico</option>
                                <option value="CA">🇨🇦 Canada</option>
                                <option value="AU">🇦🇺 Úc (Australia)</option>
                            </optgroup>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"><i
                                class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>
                <div class="md:col-span-5">
                    <label class="block text-[10px] font-bold text-slate-400 mb-2 uppercase tracking-wider">Lọc theo
                        thời gian đăng (Nhanh)</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button @click="activeTimeFilter = '1h'"
                            :class="activeTimeFilter === '1h' ? 'bg-tiktokCyan text-black font-bold' :
                                'bg-white/5 text-slate-400 hover:bg-white/10'"
                            class="py-3.5 rounded-xl text-xs transition-all border border-white/5">1 Giờ</button>
                        <button @click="activeTimeFilter = '24h'"
                            :class="activeTimeFilter === '24h' ? 'bg-tiktokCyan text-black font-bold' :
                                'bg-white/5 text-slate-400 hover:bg-white/10'"
                            class="py-3.5 rounded-xl text-xs transition-all border border-white/5">24 Giờ</button>
                        <button @click="activeTimeFilter = '7d'"
                            :class="activeTimeFilter === '7d' ? 'bg-tiktokCyan text-black font-bold' :
                                'bg-white/5 text-slate-400 hover:bg-white/10'"
                            class="py-3.5 rounded-xl text-xs transition-all border border-white/5">7 Ngày</button>
                        <button @click="activeTimeFilter = 'all'"
                            :class="activeTimeFilter === 'all' ? 'bg-tiktokCyan text-black font-bold' :
                                'bg-white/5 text-slate-400 hover:bg-white/10'"
                            class="py-3.5 rounded-xl text-xs transition-all border border-white/5">Tất cả</button>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <button @click="scan()" :disabled="loading"
                        class="w-full bg-gradient-to-r from-tiktokCyan via-blue-500 to-purple-600 text-white font-black py-3.5 rounded-xl hover:opacity-90 transition-all flex items-center justify-center gap-3 shadow-lg shadow-tiktokCyan/20 disabled:opacity-50 disabled:cursor-not-allowed group relative overflow-hidden">
                        <div
                            class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700 skew-x-12">
                        </div>
                        <i class="fa-solid fa-radar group-hover:animate-spin"></i> <span class="tracking-wider">QUÉT
                            TRENDING</span>
                    </button>
                </div>
            </div>
            <div x-show="error"
                class="mt-4 text-xs font-bold text-red-400 bg-red-500/10 p-3 rounded-lg border border-red-500/20 flex items-center gap-2 animate-pulse">
                <i class="fa-solid fa-triangle-exclamation"></i> <span x-text="error"></span>
            </div>
            <div x-show="successMsg"
                class="mt-4 text-xs font-bold text-green-400 bg-green-500/10 p-3 rounded-lg border border-green-500/20 flex items-center gap-2"
                x-transition>
                <i class="fa-solid fa-check-circle"></i> <span x-text="successMsg"></span>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-card border border-white/10 rounded-3xl overflow-hidden shadow-2xl flex flex-col min-h-[600px]">
            <div class="p-5 border-b border-white/10 flex justify-between items-center bg-white/5">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-400 uppercase">Hiển thị</span>
                    <select x-model="itemsPerPage"
                        class="bg-black/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-white outline-none font-bold cursor-pointer">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="text-xs text-slate-400 font-bold uppercase">Hiển thị: <span
                        class="text-tiktokCyan text-sm" x-text="filteredVideos.length">0</span> / <span
                        x-text="videos.length">0</span> video</div>
            </div>
            <div class="overflow-x-auto flex-grow relative">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-black/40 text-[10px] uppercase text-slate-400 font-bold sticky top-0 z-10 backdrop-blur-sm">
                        <tr>
                            <th class="p-5">Video Info</th>
                            <th class="p-5 text-center">Thời gian</th>
                            <th class="p-5 text-center">Tương tác</th>
                            <th class="p-5 text-right">Doanh thu (Est)</th>
                            <th class="p-5 text-center">Link</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-white/5">
                        <template x-for="v in paginatedVideos" :key="v.id">
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-5 max-w-[350px]">
                                    <div class="flex gap-4">
                                        <div
                                            class="relative w-14 h-20 flex-shrink-0 bg-slate-800 rounded-lg overflow-hidden border border-white/10 group-hover:border-tiktokCyan/50 transition-colors">
                                            <img :src="v.cover" class="w-full h-full object-cover"
                                                loading="lazy"
                                                onerror="this.src='https://placehold.co/56x80/1e293b/FFF?text=Error'">
                                            <div class="absolute bottom-0 right-0 bg-black/80 text-[9px] text-white px-1.5 py-0.5 font-mono rounded-tl-md backdrop-blur-sm"
                                                x-text="formatTime(v.duration)"></div>
                                        </div>
                                        <div class="min-w-0 flex-1 flex flex-col justify-center">
                                            <p class="text-xs text-slate-200 line-clamp-2 mb-2 font-medium leading-relaxed group-hover:text-tiktokCyan transition-colors"
                                                x-text="v.desc || 'Không có mô tả'"></p>
                                            <div class="flex items-center gap-2">
                                                <img :src="v.author.avatar"
                                                    class="w-5 h-5 rounded-full border border-white/10"
                                                    onerror="this.src='https://ui-avatars.com/api/?background=random'">
                                                <span
                                                    class="text-[11px] text-slate-500 font-bold truncate hover:text-white cursor-pointer"
                                                    x-text="v.author.uniqueId"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5 text-center">
                                    <div class="text-xs font-bold text-slate-300" x-text="v.create_time_human"></div>
                                    <div class="text-[10px] text-slate-600 mt-1" x-text="v.date_only"></div>
                                </td>
                                <td class="p-5 text-center">
                                    <div class="flex flex-col gap-1.5 items-center">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-white/5 border border-white/5 text-xs font-bold text-white min-w-[80px] justify-center"><i
                                                class="fa-solid fa-play text-[10px] text-slate-400"></i> <span
                                                x-text="formatNumber(v.stats.views)"></span></span>
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-white/5 border border-white/5 text-[10px] text-slate-400 min-w-[80px] justify-center"><i
                                                class="fa-solid fa-heart text-tiktokPink"></i> <span
                                                x-text="formatNumber(v.stats.likes)"></span></span>
                                    </div>
                                </td>
                                <td class="p-5 text-right">
                                    <template x-if="plan === 'free'">
                                        <div class="relative group cursor-pointer" @click="showPricing = true">
                                            <div class="font-mono font-bold text-slate-600 blur-sm select-none">$XXX.XX
                                            </div>
                                            <div
                                                class="absolute inset-0 flex items-center justify-end opacity-0 group-hover:opacity-100 transition-opacity">
                                                <span
                                                    class="text-[10px] font-bold text-tiktokCyan bg-black/80 px-2 py-1 rounded-full"><i
                                                        class="fa-solid fa-lock mr-1"></i> Nâng cấp</span>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="plan !== 'free'">
                                        <div class="font-mono font-bold text-base"
                                            :class="v.is_beta ? 'text-green-400' : 'text-slate-600'"
                                            x-text="v.is_beta ? '$' + v.revenue_est : '-'"></div>
                                    </template>
                                </td>
                                <td class="p-5 text-center">
                                    <a :href="v.link" target="_blank"
                                        class="w-9 h-9 rounded-xl bg-white/5 hover:bg-tiktokCyan hover:text-black flex items-center justify-center text-slate-400 transition-all border border-white/10 hover:scale-110 shadow-lg"><i
                                            class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="filteredVideos.length === 0 && !loading">
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30"><i
                                        class="fa-brands fa-tiktok text-6xl mb-4 animate-bounce"></i>
                                    <p class="text-lg font-bold text-white">Chưa có dữ liệu</p>
                                    <p class="text-sm">Hãy chọn quốc gia và bấm Quét, hoặc thay đổi bộ lọc thời gian
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-white/10 bg-black/20 flex flex-col md:flex-row justify-between items-center gap-4"
                x-show="filteredVideos.length > 0">
                <div class="text-xs text-slate-400 font-bold uppercase tracking-wide">Trang <span
                        class="text-white text-sm mx-1" x-text="currentPage"></span> / <span
                        x-text="totalPages"></span></div>
                <div class="flex items-center gap-2">
                    <button @click="changePage(1)" :disabled="currentPage === 1"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-slate-400 hover:bg-tiktokCyan hover:text-black hover:border-tiktokCyan disabled:opacity-30 disabled:cursor-not-allowed transition-all"><i
                            class="fa-solid fa-angles-left text-xs"></i></button>
                    <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-slate-400 hover:bg-tiktokCyan hover:text-black hover:border-tiktokCyan disabled:opacity-30 disabled:cursor-not-allowed transition-all"><i
                            class="fa-solid fa-angle-left text-xs"></i></button>
                    <div class="flex items-center gap-1 mx-2">
                        <template x-for="page in pagesToShow" :key="page">
                            <button @click="changePage(page)"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold transition-all border"
                                :class="currentPage === page ?
                                    'bg-tiktokCyan text-black border-tiktokCyan scale-110 shadow-[0_0_10px_rgba(0,242,234,0.3)]' :
                                    'bg-white/5 border-white/10 text-slate-300 hover:bg-white/10 hover:border-white/30'"
                                x-text="page"></button>
                        </template>
                    </div>
                    <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-slate-400 hover:bg-tiktokCyan hover:text-black hover:border-tiktokCyan disabled:opacity-30 disabled:cursor-not-allowed transition-all"><i
                            class="fa-solid fa-angle-right text-xs"></i></button>
                    <button @click="changePage(totalPages)" :disabled="currentPage === totalPages"
                        class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-slate-400 hover:bg-tiktokCyan hover:text-black hover:border-tiktokCyan disabled:opacity-30 disabled:cursor-not-allowed transition-all"><i
                            class="fa-solid fa-angles-right text-xs"></i></button>
                </div>
            </div>
        </div>

        <!-- AI Keywords Modal -->
        <div x-show="showAiModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
            x-transition.opacity x-cloak>
            <div class="bg-surface border border-tiktokCyan/30 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden relative"
                @click.outside="showAiModal = false">
                <div
                    class="bg-gradient-to-r from-tiktokCyan/10 to-blue-600/10 p-4 border-b border-white/10 flex justify-between items-center">
                    <div class="flex items-center gap-2"><i class="fa-solid fa-robot text-tiktokCyan"></i>
                        <h3 class="font-bold text-white">AI Keywords Suggestion</h3>
                    </div>
                    <button @click="showAiModal = false" class="text-slate-400 hover:text-white"><i
                            class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-400 mb-4">Top từ khóa đang thịnh hành tại <span
                            class="text-tiktokCyan font-bold" x-text="params.region"></span>:</p>
                    <div class="flex flex-wrap gap-2 mb-6 max-h-[200px] overflow-y-auto">
                        <template x-for="kw in keywords"><span
                                class="px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-sm text-slate-200 hover:border-tiktokCyan transition-colors cursor-default select-all"
                                :class="kw.startsWith('#') ? 'text-tiktokCyan' : ''" x-text="kw"></span></template>
                        <div x-show="keywords.length === 0" class="text-slate-500 italic text-sm w-full text-center">
                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Đang phân tích...
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button @click="copyKeywords()"
                            class="flex-1 bg-white/10 hover:bg-white/20 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 border border-white/10 group"><i
                                class="fa-regular fa-copy group-hover:scale-110"></i> <span x-text="copyText">Sao chép
                                tất cả</span></button>
                        <button @click="showAiModal = false"
                            class="px-6 py-3 rounded-xl border border-white/10 text-slate-400 hover:text-white hover:bg-white/5 font-bold">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Modal (Upsell) - Updated Prices with Action Logic -->
        <div x-show="showPricing"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/95 backdrop-blur-xl" x-cloak
            x-transition.opacity>
            <div class="bg-card border border-white/10 rounded-3xl max-w-4xl w-full p-8 relative shadow-2xl overflow-y-auto max-h-screen"
                @click.outside="showPricing = false">
                <button @click="showPricing = false"
                    class="absolute top-6 right-6 text-slate-500 hover:text-white transition-colors"><i
                        class="fa-solid fa-xmark text-2xl"></i></button>
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-5xl font-black text-white mb-4 uppercase tracking-tighter">Mở khóa
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-tiktokCyan to-tiktokPink">SỨC
                            MẠNH</span>
                    </h2>
                    <p class="text-slate-400 text-lg">Xem doanh thu ước tính, phân tích AI và không giới hạn lượt quét.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Gói Basic -->
                    <div
                        class="group border border-white/10 bg-white/5 rounded-2xl p-8 hover:border-white/30 hover:bg-white/10 transition-all flex flex-col relative overflow-hidden">
                        <h3 class="text-xl font-bold text-white mb-2">Basic</h3>
                        <div class="text-3xl font-black text-white mb-6">20k<span
                                class="text-sm font-normal text-slate-500">/tháng</span></div>
                        <ul class="space-y-4 text-sm text-slate-300 mb-8 flex-grow">
                            <li class="flex items-center"><i class="fa-solid fa-check text-green-500 mr-3"></i> 50
                                lượt quét/ngày</li>
                            <li class="flex items-center"><i class="fa-solid fa-check text-green-500 mr-3"></i> Xem
                                Trending cơ bản</li>
                        </ul>
                        <button @click="buyPlan('basic', 20000)"
                            class="block w-full py-3 rounded-xl border border-white/20 text-center text-white font-bold hover:bg-white/20 transition-all">
                            <span x-show="userBalance < 20000">Nạp thêm <span
                                    x-text="formatNumberVND(20000 - userBalance)"></span></span>
                            <span x-show="userBalance >= 20000">Mua ngay</span>
                        </button>
                    </div>
                    <!-- Gói Pro -->
                    <div
                        class="group border-2 border-tiktokCyan bg-tiktokCyan/5 rounded-2xl p-8 relative transform md:-translate-y-4 shadow-[0_0_30px_rgba(0,242,234,0.1)] flex flex-col">
                        <div
                            class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-tiktokCyan text-black text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-lg">
                            Khuyên dùng</div>
                        <h3 class="text-xl font-bold text-white mb-2">PRO</h3>
                        <div class="text-3xl font-black text-tiktokCyan mb-6">79k<span
                                class="text-sm font-normal text-slate-500">/tháng</span></div>
                        <ul class="space-y-4 text-sm text-slate-300 mb-8 flex-grow">
                            <li class="flex items-center"><i class="fa-solid fa-check text-tiktokCyan mr-3"></i>
                                <strong>200 lượt quét/ngày</strong>
                            </li>
                            <li class="flex items-center"><i class="fa-solid fa-check text-tiktokCyan mr-3"></i>
                                <strong>Xem doanh thu Beta</strong>
                            </li>
                            <li class="flex items-center"><i class="fa-solid fa-check text-tiktokCyan mr-3"></i>
                                <strong>Gợi ý Keyword dễ lên Xu hướng</strong>
                            </li>
                        </ul>
                        <button @click="buyPlan('pro', 79000)"
                            class="block w-full py-3.5 rounded-xl bg-gradient-to-r from-tiktokCyan to-blue-500 text-black text-center font-bold hover:opacity-90 hover:scale-105 transition-all shadow-lg">
                            <span x-show="userBalance < 79000">Nạp thêm <span
                                    x-text="formatNumberVND(79000 - userBalance)"></span></span>
                            <span x-show="userBalance >= 79000">Nâng cấp ngay</span>
                        </button>
                    </div>
                    <!-- Gói Premium -->
                    <div
                        class="group border border-white/10 bg-white/5 rounded-2xl p-8 hover:border-tiktokPink/50 hover:bg-tiktokPink/5 transition-all flex flex-col relative overflow-hidden">
                        <h3 class="text-xl font-bold text-white mb-2">Premium</h3>
                        <div class="text-3xl font-black text-white mb-6">150k<span
                                class="text-sm font-normal text-slate-500">/tháng</span></div>
                        <ul class="space-y-4 text-sm text-slate-300 mb-8 flex-grow">
                            <li class="flex items-center"><i class="fa-solid fa-check text-tiktokPink mr-3"></i>
                                <strong>1000 lượt quét</strong>
                            </li>
                            <li class="flex items-center"><i class="fa-solid fa-check text-tiktokPink mr-3"></i> Tất
                                cả tính năng PRO</li>
                            <li class="flex items-center"><i class="fa-solid fa-check text-tiktokPink mr-3"></i>
                                Support 1-1 ưu tiên</li>
                        </ul>
                        <button @click="buyPlan('premium', 150000)"
                            class="block w-full py-3 rounded-xl border border-white/20 text-center text-white font-bold hover:bg-white/20 hover:border-tiktokPink hover:text-tiktokPink transition-all">
                            <span x-show="userBalance < 150000">Nạp thêm <span
                                    x-text="formatNumberVND(150000 - userBalance)"></span></span>
                            <span x-show="userBalance >= 150000">Mua ngay</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @php
        // Logic PHP (Giữ nguyên)
        $user = Auth::user();
        $plan = $user->plan_type ?? 'free';
        $limit = 10;
        if ($plan === 'basic') {
            $limit = 50;
        }
        if ($plan === 'pro') {
            $limit = 200;
        }
        if ($plan === 'premium') {
            $limit = 1000;
        }
        $planNames = [
            'free' => 'Dùng thử (Free)',
            'basic' => 'Cơ bản (Basic)',
            'pro' => 'Chuyên nghiệp (Pro)',
            'premium' => 'Cao cấp (Premium)',
        ];
        $currentPlanName = $planNames[$plan] ?? ucfirst($plan);
        $now = now();
        $expiryText = '';
        $isExpired = false;
        if ($user->vip_expires_at) {
            $expirationDate = \Carbon\Carbon::parse($user->vip_expires_at);
        } else {
            $createdAt = $user->created_at ? \Carbon\Carbon::parse($user->created_at) : $now;
            $expirationDate = $createdAt->copy()->addDays(7);
        }
        if ($expirationDate->lt($now)) {
            $expiryText = 'Đã hết hạn';
            $isExpired = true;
        } else {
            $diffInDays = $now->diffInDays($expirationDate, false);
            $expiryText =
                $diffInDays >= 1
                    ? 'Còn ' . floor($diffInDays) . ' ngày'
                    : 'Còn ' . max(1, floor($now->diffInHours($expirationDate, false))) . ' giờ';
        }
    @endphp

    <script>
        function tiktokApp() {
            return {
                loading: false,
                error: null,
                successMsg: null,
                showPricing: false,
                isProfileOpen: false,
                loadingText: 'Đang kết nối...',

                videos: [],
                keywords: [],
                showAiModal: false,
                copyText: 'Sao chép tất cả',
                plan: '{{ $plan }}',
                userBalance: {{ $user->balance ?? 0 }},

                aiLoading: false,

                currentPage: 1,
                itemsPerPage: 10,
                activeTimeFilter: 'all',
                params: {
                    region: 'US'
                },

                usage: {
                    used: {{ $user->daily_usage_count ?? 0 }},
                    limit: {{ $limit }},
                    expiry_text: "{{ $expiryText }}",
                    is_expired: {{ $isExpired ? 'true' : 'false' }},
                    plan_name: "{{ $currentPlanName }}"
                },

                toggleProfile() {
                    this.isProfileOpen = !this.isProfileOpen;
                },
                closeProfile() {
                    this.isProfileOpen = false;
                },

                get filteredVideos() {
                    if (this.activeTimeFilter === 'all') return this.videos;
                    const now = new Date();
                    const filterMap = {
                        '1h': 3600000,
                        '24h': 86400000,
                        '7d': 604800000
                    };
                    const maxAge = filterMap[this.activeTimeFilter];
                    return this.videos.filter(v => {
                        if (!v.timestamp) return true;
                        return (now.getTime() - v.timestamp * 1000) <= maxAge;
                    });
                },

                get paginatedVideos() {
                    const start = (this.currentPage - 1) * parseInt(this.itemsPerPage);
                    const end = start + parseInt(this.itemsPerPage);
                    return this.filteredVideos.slice(start, end);
                },

                get totalPages() {
                    return Math.ceil(this.filteredVideos.length / parseInt(this.itemsPerPage)) || 1;
                },

                get pagesToShow() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const delta = 2;
                    const range = [];
                    for (let i = Math.max(1, current - delta); i <= Math.min(total, current + delta); i++) range.push(
                        i);
                    return range;
                },

                changePage(page) {
                    if (page >= 1 && page <= this.totalPages) this.currentPage = page;
                },
                formatNumber(num) {
                    if (!num) return '0';
                    return new Intl.NumberFormat('en-US', {
                        notation: "compact",
                        maximumFractionDigits: 1
                    }).format(num);
                },
                formatNumberVND(num) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(num);
                },
                formatTime(s) {
                    const m = Math.floor(s / 60);
                    const sc = s % 60;
                    return `${m}:${sc.toString().padStart(2,'0')}`;
                },

                // New Logic: Mua gói hoặc Chuyển trang nạp tiền
                async buyPlan(planName, price) {
                    if (this.userBalance < price) {
                        const missing = price - this.userBalance;
                        // Chuyển hướng sang trang deposit với số tiền thiếu
                        window.location.href = `/deposit?amount=${missing}`;
                        return;
                    }

                    if (!confirm(
                            `Bạn có chắc chắn muốn mua gói ${planName.toUpperCase()} với giá ${this.formatNumberVND(price)}?`
                        )) {
                        return;
                    }

                    this.loading = true;
                    this.loadingText = 'Đang xử lý giao dịch...';

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch('/tool/tiktok-beta/buy-plan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                plan: planName
                            })
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || 'Giao dịch thất bại');
                        }

                        // Thành công
                        this.showPricing = false;
                        this.successMsg = `Nâng cấp thành công gói ${planName.toUpperCase()}!`;
                        this.userBalance = data.new_balance;
                        this.usage.plan_name = data.plan_name;
                        this.usage.limit = data.new_limit;
                        this.usage.expiry_text = data.new_expiry;
                        this.usage.is_expired = false;
                        this.plan = planName; // Mở khóa tính năng ngay lập tức

                        // Ẩn thông báo sau 3s
                        setTimeout(() => this.successMsg = null, 3000);

                    } catch (e) {
                        this.error = e.message;
                        setTimeout(() => this.error = null, 3000);
                    } finally {
                        this.loading = false;
                    }
                },

                async scan() {
                    this.loading = true;
                    this.error = null;
                    this.videos = [];
                    this.currentPage = 1;
                    const msgs = ["Đang kết nối vệ tinh...", `Đang quét thị trường ${this.params.region}...`,
                        "Đang phân tích doanh thu...", "Đang tải dữ liệu hình ảnh..."
                    ];
                    let msgIdx = 0;
                    const interval = setInterval(() => {
                        this.loadingText = msgs[msgIdx++ % msgs.length];
                    }, 1500);

                    try {
                        const qs = new URLSearchParams(this.params).toString();
                        const res = await fetch(`/tool/tiktok-beta/search?${qs}`);
                        clearInterval(interval);
                        if (!res.ok) {
                            const data = await res.json();
                            if (res.status === 429) {
                                this.showPricing = true;
                                throw new Error("Hết lượt quét miễn phí!");
                            }
                            throw new Error(data.error || 'Lỗi hệ thống');
                        }
                        const data = await res.json();
                        this.videos = data.videos;
                        if (data.meta && data.meta.usage) {
                            this.usage.used = data.meta.usage.used;
                            this.usage.limit = data.meta.usage.limit;
                            if (data.meta.usage.expiry_text) this.usage.expiry_text = data.meta.usage.expiry_text;
                        } else {
                            this.usage.used++;
                        }
                    } catch (e) {
                        clearInterval(interval);
                        this.error = e.message;
                    } finally {
                        this.loading = false;
                    }
                },

                async getAiKeywords() {
                    if (this.aiLoading) return;
                    try {
                        this.aiLoading = true;
                        this.copyText = 'Sao chép tất cả';
                        const res = await fetch(`/tool/tiktok-beta/ai-keywords?region=${this.params.region}`);
                        const data = await res.json();
                        if (data.is_demo) {
                            this.showAiModal = false;
                            this.showPricing = true;
                            return;
                        }
                        this.keywords = data.keywords;
                        this.showAiModal = true;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.aiLoading = false;
                    }
                },

                copyKeywords() {
                    const text = this.keywords.map(k => k.startsWith('#') ? k : k + ', ').join(' ');
                    navigator.clipboard.writeText(text).then(() => {
                        this.copyText = 'Đã sao chép!';
                        setTimeout(() => this.copyText = 'Sao chép tất cả', 2000);
                    });
                }
            }
        }
    </script>
</body>

</html>
