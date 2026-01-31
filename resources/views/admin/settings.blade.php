<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cấu hình Hệ thống - ZENTRA Admin</title>
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
        <div class="flex justify-between items-center mb-6">
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
                class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 border border-green-200 shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <!-- Toolbar -->
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-lg text-gray-800">Danh sách API Keys</h2>
                    <p class="text-xs text-gray-500">Hệ thống tự động xoay vòng key khi hết tiền (< $0.2).</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="checkStatus()" :disabled="checking"
                        class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-md disabled:opacity-50 flex items-center gap-2">
                        <i class="fa-solid" :class="checking ? 'fa-spinner fa-spin' : 'fa-stethoscope'"></i>
                        <span x-text="checking ? 'Đang kiểm tra...' : 'Kiểm tra Sức khỏe Key'"></span>
                    </button>
                    <button type="button" @click="addKey()"
                        class="bg-gray-800 text-white hover:bg-black px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Thêm Key
                    </button>
                </div>
            </div>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="apify_tokens" :value="tokensString">

                <!-- TABLE QUẢN LÝ -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-100 text-gray-500 font-bold text-xs uppercase border-b border-gray-200">
                            <tr>
                                <th class="p-4 w-10 text-center"><input type="checkbox" @click="toggleAll"
                                        x-model="selectAll" class="rounded cursor-pointer"></th>
                                <th class="p-4 w-16 text-center">STT</th>
                                <th class="p-4">Token (Masked)</th>
                                <th class="p-4 text-center">Trạng thái</th>
                                <th class="p-4">Email Account</th>
                                <th class="p-4 text-center">Đã dùng / Giới hạn</th>
                                <th class="p-4 text-center">Số dư khả dụng</th>
                                <th class="p-4 text-center w-32">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(item, index) in tokenList" :key="index">
                                <tr class="transition-colors group"
                                    :class="item.status === 'dead' ? 'bg-red-50 hover:bg-red-100' : 'hover:bg-blue-50'">
                                    <!-- Check box -->
                                    <td class="p-4 text-center"><input type="checkbox" :value="index"
                                            x-model="selectedIndices" class="rounded cursor-pointer"></td>
                                    <!-- STT -->
                                    <td class="p-4 text-center text-gray-400 font-mono" x-text="index + 1"></td>
                                    <!-- Token (masked) -->
                                    <td class="p-4 font-mono text-gray-700 font-bold text-xs"
                                        x-text="maskToken(item.full_token)"></td>

                                    <!-- Status -->
                                    <td class="p-4 text-center">
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border whitespace-nowrap"
                                            :class="getStatusColor(item.status)">
                                            <i class="fa-solid" :class="getStatusIcon(item.status)"></i>
                                            <span x-text="item.message || 'Chưa check'"></span>
                                        </span>
                                    </td>
                                    <!-- Email Account -->
                                    <td class="p-4 text-gray-600 text-xs" x-text="item.email || '-'"></td>
                                    <!-- Đã dùng / Giới hạn -->
                                    <td class="p-4 text-right font-mono"
                                        x-text="formatMoney(item.usageUsd, 5) + ' / ' + formatMoney(item.limitUsd, 2)">
                                    </td>
                                    <!-- Số dư khả dụng -->
                                    <td class="p-4 text-right">
                                        <div class="flex flex-col items-end">
                                            <span class="font-mono font-bold text-sm"
                                                :class="{
                                                    'text-green-600': item.remaining >= 1.0,
                                                    'text-yellow-600': item.remaining >= 0.25 && item.remaining < 1.0,
                                                    'text-red-600': item.remaining < 0.25
                                                }"
                                                x-text="formatMoney(item.remaining, 6)">
                                            </span>
                                            <span class="text-[10px] text-gray-500 mt-1"
                                                x-show="item.estimated_job_cost && item.remaining < item.estimated_job_cost">
                                                Cần <span x-text="formatMoney(item.estimated_job_cost, 2)"></span> để
                                                chạy job
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 text-center">
                                        <div class="flex justify-center gap-2 opacity-100">
                                            <button type="button" @click="editKey(index)"
                                                class="w-8 h-8 rounded bg-white border border-gray-200 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                                title="Sửa Key">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </button>
                                            <button type="button" @click="removeKey(index)"
                                                class="w-8 h-8 rounded bg-white border border-gray-200 text-red-600 hover:bg-red-600 hover:text-white transition-all shadow-sm"
                                                title="Xóa Key">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="tokenList.length === 0">
                                <td colspan="7" class="p-8 text-center text-gray-400 italic">Chưa có API Key nào. Hãy
                                    bấm "Thêm Key" để bắt đầu.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Actions -->
                <div class="p-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                    <div x-show="selectedIndices.length > 0" class="flex items-center gap-2 animate-pulse">
                        <button type="button" @click="removeSelected()"
                            class="bg-red-500 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-600 transition-colors shadow-sm">
                            <i class="fa-solid fa-trash-can mr-1"></i> Xóa <span
                                x-text="selectedIndices.length"></span>
                            mục đã chọn
                        </button>
                    </div>
                    <div x-show="selectedIndices.length === 0"></div>

                    <button type="submit"
                        class="bg-green-600 text-white px-6 py-2.5 rounded-xl hover:bg-green-700 font-bold shadow-lg shadow-green-500/30 transition-all transform active:scale-95 flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Lưu Cấu Hình
                    </button>
                </div>
            </form>
        </div>

        <!-- Error Toast -->
        <div x-show="errorMsg"
            class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 z-50 animate-bounce"
            x-cloak @click="errorMsg = null">
            <i class="fa-solid fa-circle-exclamation"></i> <span x-text="errorMsg"></span>
        </div>
    </div>

    <script>
        function settingApp() {
            return {
                initialTokens: `{{ $apifyTokens }}`,
                tokenList: [],
                checking: false,
                errorMsg: null,
                selectedIndices: [],
                selectAll: false,

                init() {
                    if (this.initialTokens) {
                        this.tokenList = this.initialTokens.split(/[\n,]+/)
                            .map(t => t.trim())
                            .filter(t => t.length > 5)
                            .map(token => ({
                                full_token: token,
                                status: 'unknown',
                                message: 'Chưa check',
                                email: '',
                                plan: '',
                                remaining: 0,
                                estimated_cost: 0.20
                            }));

                        // Tự động check status sau 1 giây (tùy chọn)
                        setTimeout(() => {
                            if (this.tokenList.length > 0 && this.tokenList.every(t => t.status === 'unknown')) {
                                this.checkStatus();
                            }
                        }, 1000);
                    }
                },

                get tokensString() {
                    return this.tokenList.map(t => t.full_token).join(',');
                },
                maskToken(token) {
                    if (!token) return '';
                    return token.substring(0, 8) + '...' + token.substring(token.length - 4);
                },

                getStatusColor(status) {
                    if (status === 'alive') return 'bg-green-100 text-green-700 border-green-200';
                    if (status === 'dead') return 'bg-red-100 text-red-700 border-red-200';
                    if (status === 'loading') return 'bg-blue-100 text-blue-700 border-blue-200';
                    return 'bg-gray-100 text-gray-500 border-gray-200';
                },
                getStatusIcon(status) {
                    if (status === 'alive') return 'fa-check';
                    if (status === 'dead') return 'fa-xmark';
                    if (status === 'loading') return 'fa-spinner fa-spin';
                    return 'fa-question';
                },

                addKey() {
                    let key = prompt("Nhập API Token mới:");
                    if (key && key.trim().length > 10) {
                        this.tokenList.push({
                            full_token: key.trim(),
                            status: 'unknown',
                            message: 'Mới thêm',
                            email: '',
                            plan: ''
                        });
                    }
                },

                editKey(index) {
                    let oldKey = this.tokenList[index].full_token;
                    let newKey = prompt("Chỉnh sửa API Key:", oldKey);
                    if (newKey !== null && newKey.trim() !== "") {
                        this.tokenList[index].full_token = newKey.trim();
                        this.tokenList[index].status = 'unknown';
                        this.tokenList[index].message = 'Đã sửa';
                        this.tokenList[index].plan = '';
                    }
                },

                removeKey(index) {
                    if (confirm("Xóa Key này?")) {
                        this.tokenList.splice(index, 1);
                        this.selectedIndices = [];
                    }
                },

                toggleAll() {
                    this.selectAll = !this.selectAll;
                    this.selectedIndices = this.selectAll ? this.tokenList.map((_, i) => i) : [];
                },

                removeSelected() {
                    if (confirm(`Xóa ${this.selectedIndices.length} key?`)) {
                        this.selectedIndices.sort((a, b) => b - a).forEach(i => this.tokenList.splice(i, 1));
                        this.selectedIndices = [];
                        this.selectAll = false;
                    }
                },

                async checkStatus() {
                    if (this.tokenList.length === 0) return;
                    this.checking = true;
                    this.errorMsg = null;

                    // Reset tất cả về trạng thái loading
                    this.tokenList.forEach(t => {
                        t.status = 'loading';
                        t.message = 'Đang kiểm tra số dư chính xác...';
                        t.plan = '';
                        t.remaining = 0;
                    });

                    try {
                        const rawTokens = this.tokenList.map(t => t.full_token).join(',');
                        const res = await fetch("{{ route('admin.settings.check') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                tokens: rawTokens
                            })
                        });

                        if (!res.ok) throw new Error("Lỗi server: " + res.status);

                        const results = await res.json();

                        // Cập nhật từng key với dữ liệu chính xác
                        this.tokenList.forEach((tokenItem, index) => {
                            const result = results.find(r => r.full_token === tokenItem.full_token);
                            if (result) {
                                // Cập nhật tất cả thông tin
                                tokenItem.status = result.status;
                                tokenItem.message = result.message;
                                tokenItem.email = result.email;
                                tokenItem.plan = result.plan;
                                tokenItem.remaining = result.remaining || 0;
                                tokenItem.estimated_cost = result.estimated_job_cost || 0.20;
                                tokenItem.usageUsd = result.usageUsd || 0;
                                tokenItem.limitUsd = result.limitUsd || 5.0;

                                // Đảm bảo key hết tiền được đánh dấu 'dead'
                                if (result.remaining < result.estimated_job_cost && result.status !== 'dead') {
                                    tokenItem.status = 'dead';
                                    tokenItem.message = 'Không đủ tiền (Còn $' + result.remaining.toFixed(6) +
                                        ')';
                                }
                            } else {
                                tokenItem.status = 'error';
                                tokenItem.message = 'Không phản hồi';
                            }
                        });

                    } catch (e) {
                        this.errorMsg = "Lỗi kiểm tra: " + e.message;
                        this.tokenList.forEach(t => {
                            if (t.status === 'loading') {
                                t.status = 'error';
                                t.message = 'Lỗi mạng';
                            }
                        });
                    } finally {
                        this.checking = false;
                    }
                },

                formatMoney(value, decimals = 5) {
                    if (value === undefined || value === null) return 'N/A';
                    return '$' + parseFloat(value).toFixed(decimals);
                },
            }
        }
    </script>
</body>

</html>
