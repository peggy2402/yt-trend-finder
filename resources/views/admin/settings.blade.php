<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cấu hình Hệ thống - ZENTRA Admin</title>
    <!-- Quan trọng: CSRF Token cho Ajax -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 p-6 font-sans text-gray-800">
    <div class="max-w-6xl mx-auto" x-data="settingApp()">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <div class="bg-blue-600 text-white p-3 rounded-2xl shadow-lg shadow-blue-500/30">
                    <i class="fa-solid fa-cogs text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-800 tracking-tight">Cấu hình Hệ thống</h1>
                    <p class="text-sm text-gray-500 font-medium">Quản lý tài nguyên API và kết nối</p>
                </div>
            </div>
            <a href="{{ route('admin.users') }}"
                class="flex items-center gap-2 text-gray-600 hover:text-blue-600 bg-white px-5 py-2.5 rounded-xl shadow-sm hover:shadow-md transition-all font-bold">
                <i class="fa-solid fa-users"></i> Quản lý User
            </a>
        </div>

        @if (session('success'))
            <div
                class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border border-green-200 shadow-sm flex items-center gap-3 animate-pulse">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Cột trái: Form nhập & Bảng điều khiển -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                    <div
                        class="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex justify-between items-center">
                        <h2 class="font-bold text-xl text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-robot text-blue-500"></i> Apify API Tokens
                        </h2>
                        <span class="text-xs font-bold px-3 py-1 bg-gray-100 rounded-full text-gray-500"
                            x-text="tokenList.length + ' Keys'"></span>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
                        @csrf

                        <!-- Khu vực nhập liệu (Hiển thị khi cần edit nhanh) -->
                        <div class="mb-6" x-show="viewMode === 'raw'">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider ml-1">
                                Nhập danh sách Token (Mỗi dòng 1 key hoặc phẩy)
                            </label>
                            <textarea x-model="tokensRaw" name="apify_tokens" rows="6"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none font-mono text-sm bg-gray-50 text-gray-700 leading-relaxed resize-y"
                                placeholder="apify_api_key_xxxxxxxxxxx&#10;apify_api_key_yyyyyyyyyyy">{{ $apifyTokens }}</textarea>
                        </div>

                        <!-- Bảng danh sách Key (Hiển thị đẹp) -->
                        <div class="mb-6 overflow-hidden rounded-xl border border-gray-200"
                            x-show="viewMode === 'table'">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-500 font-bold text-[10px] uppercase tracking-wider">
                                    <tr>
                                        <th class="p-3 w-10 text-center">STT</th>
                                        <th class="p-3">Token (Masked)</th>
                                        <th class="p-3">Trạng thái</th>
                                        <th class="p-3 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(token, index) in tokenList" :key="index">
                                        <tr class="hover:bg-blue-50/50 transition-colors">
                                            <td class="p-3 text-center text-gray-400" x-text="index + 1"></td>
                                            <td class="p-3 font-mono text-gray-700" x-text="maskToken(token.key)"></td>
                                            <td class="p-3">
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                                    :class="getStatusClass(token.status)">
                                                    <i class="fa-solid" :class="getStatusIcon(token.status)"></i>
                                                    <span x-text="token.message || 'Chưa check'"></span>
                                                </span>
                                            </td>
                                            <td class="p-3 text-right">
                                                <button type="button" @click="removeToken(index)"
                                                    class="text-red-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition-colors"
                                                    title="Xóa key này">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="tokenList.length === 0">
                                        <td colspan="4" class="p-4 text-center text-gray-500 italic">Chưa có API Key
                                            nào. Hãy chuyển sang chế độ nhập liệu để thêm.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Thanh công cụ dưới -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 mt-4">
                            <div class="flex gap-2">
                                <button type="button" @click="toggleViewMode()"
                                    class="text-gray-600 hover:text-blue-600 text-sm font-bold px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                    <i class="fa-solid"
                                        :class="viewMode === 'raw' ? 'fa-table' : 'fa-pen-to-square'"></i>
                                    <span x-text="viewMode === 'raw' ? 'Xem dạng bảng' : 'Sửa thủ công'"></span>
                                </button>
                                <button type="button" @click="checkStatus()"
                                    :disabled="checking || tokenList.length === 0"
                                    class="text-blue-600 hover:text-blue-700 text-sm font-bold flex items-center gap-2 disabled:opacity-50 transition-colors px-3 py-2 rounded-lg hover:bg-blue-50">
                                    <i class="fa-solid"
                                        :class="checking ? 'fa-circle-notch fa-spin' : 'fa-stethoscope'"></i>
                                    <span x-text="checking ? 'Đang kiểm tra...' : 'Check All'"></span>
                                </button>
                            </div>

                            <!-- Hidden input để submit giá trị thực -->
                            <input type="hidden" name="apify_tokens" :value="tokensRaw">

                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2.5 rounded-xl hover:bg-blue-700 font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center gap-2 transform active:scale-95">
                                <i class="fa-solid fa-save"></i> Lưu Cấu Hình
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Error Message -->
                <div x-show="errorMsg"
                    class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-200 flex items-center gap-3 animate-pulse"
                    x-cloak>
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i> <span x-text="errorMsg"
                        class="font-medium"></span>
                </div>
            </div>

            <!-- Cột phải: Thông tin & Mẹo -->
            <div class="space-y-6">
                <div
                    class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 rounded-3xl border border-yellow-100 shadow-lg shadow-yellow-500/10">
                    <h3 class="font-bold text-yellow-800 mb-3 flex items-center gap-2 text-lg">
                        <i class="fa-solid fa-lightbulb text-yellow-500"></i> Chiến thuật Free
                    </h3>
                    <div class="text-sm text-yellow-800/80 leading-relaxed space-y-3">
                        <p>Mỗi tài khoản Apify Free được tặng <strong>$5/tháng</strong>. Scraper này tốn khoảng $0.5 -
                            $1 cho 1000 video.</p>
                        <p class="font-bold">Cách tối ưu:</p>
                        <ul class="list-disc list-inside space-y-1 ml-1">
                            <li>Tạo 5-10 tài khoản Apify Free.</li>
                            <li>Lấy API Token của từng tài khoản.</li>
                            <li>Dán tất cả vào đây.</li>
                        </ul>
                        <p class="italic mt-2">-> Hệ thống ZENTRA sẽ tự động chuyển sang Key tiếp theo khi Key cũ hết
                            tiền.</p>
                    </div>
                    <a href="https://console.apify.com/" target="_blank"
                        class="mt-5 block w-full text-center bg-white border border-yellow-200 text-yellow-700 font-bold py-3 rounded-xl hover:bg-yellow-100 hover:scale-105 transition-all shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Lấy thêm API Key
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function settingApp() {
            return {
                tokensRaw: `{{ $apifyTokens }}`,
                viewMode: 'table', // 'table' or 'raw'
                tokenList: [],
                checking: false,
                errorMsg: null,

                init() {
                    this.parseTokens();
                    // Watch tokensRaw changes to update list if in raw mode
                    this.$watch('tokensRaw', value => {
                        if (this.viewMode === 'raw') this.parseTokens();
                    });
                },

                parseTokens() {
                    const raw = this.tokensRaw.split(/[\n,]+/).map(t => t.trim()).filter(t => t.length > 5);
                    // Giữ lại status cũ nếu có
                    this.tokenList = raw.map(key => {
                        const existing = this.tokenList.find(t => t.key === key);
                        return existing ? existing : {
                            key: key,
                            status: 'unknown',
                            message: ''
                        };
                    });
                },

                toggleViewMode() {
                    this.viewMode = this.viewMode === 'table' ? 'raw' : 'table';
                },

                maskToken(token) {
                    if (!token) return '';
                    return token.substring(0, 8) + '...' + token.substring(token.length - 4);
                },

                removeToken(index) {
                    if (!confirm('Xóa key này?')) return;
                    this.tokenList.splice(index, 1);
                    this.tokensRaw = this.tokenList.map(t => t.key).join('\n');
                },

                getStatusClass(status) {
                    if (status === 'alive') return 'bg-green-100 text-green-700 border-green-200';
                    if (status === 'dead' || status === 'error') return 'bg-red-100 text-red-700 border-red-200';
                    return 'bg-gray-100 text-gray-600 border-gray-200';
                },

                getStatusIcon(status) {
                    if (status === 'alive') return 'fa-check-circle';
                    if (status === 'dead' || status === 'error') return 'fa-times-circle';
                    return 'fa-question-circle';
                },

                async checkStatus() {
                    if (this.tokenList.length === 0) {
                        this.errorMsg = "Danh sách token trống.";
                        return;
                    }

                    this.checking = true;
                    this.errorMsg = null;

                    // Reset status visual
                    this.tokenList.forEach(t => {
                        t.status = 'loading';
                        t.message = 'Đang check...';
                    });

                    try {
                        const res = await fetch("{{ route('admin.settings.check') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                tokens: this.tokensRaw
                            })
                        });

                        if (!res.ok) throw new Error(`Lỗi server (${res.status})`);

                        const results = await res.json();

                        // Update status back to list
                        this.tokenList.forEach(t => {
                            const result = results.find(r => r.full_token === t.key);
                            if (result) {
                                t.status = result.status;
                                t.message = result.message + (result.plan ? ` (${result.plan})` : '');
                            } else {
                                t.status = 'error';
                                t.message = 'Không có phản hồi';
                            }
                        });

                    } catch (e) {
                        console.error(e);
                        this.errorMsg = e.message;
                        this.tokenList.forEach(t => {
                            if (t.status === 'loading') {
                                t.status = 'error';
                                t.message = 'Lỗi mạng';
                            }
                        });
                    } finally {
                        this.checking = false;
                    }
                }
            }
        }
    </script>
</body>

</html>
