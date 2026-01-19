<x-app-layout>
    {{-- Custom CSS cho Toast --}}
    <style>
        .toast-enter { transform: translateX(100%); opacity: 0; }
        .toast-enter-active { transform: translateX(0); opacity: 1; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .toast-exit { transform: translateX(0); opacity: 1; }
        .toast-exit-active { transform: translateX(100%); opacity: 0; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-xl text-slate-100 leading-tight flex items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-200 blur rounded-full opacity-50"></div>
                    <span class="relative w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                </div>
                <span>{{ __('Lịch sử giao dịch') }}</span>
            </h2>
            <div class="text-sm font-medium text-slate-100 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-100 shadow-sm hidden sm:block">
                Tổng đơn: <strong class="text-blue-400">{{ $orders->count() }}</strong>
            </div>
        </div>
    </x-slot>

    <div class="py-6 md:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            
            @if($orders->isEmpty())
                <!-- EMPTY STATE -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-12 text-center">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Chưa có giao dịch nào</h3>
                    <p class="text-slate-500 mb-6">Bạn chưa mua đơn hàng nào tại hệ thống.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-1">
                        <i class="fa-solid fa-store mr-2"></i> Mua ngay
                    </a>
                </div>
            @else
                <!-- DESKTOP VIEW (TABLE) - Ẩn trên Mobile -->
                <div class="hidden md:block bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-4 font-bold">Mã GD</th>
                                    <th class="px-6 py-4 font-bold">Sản phẩm</th>
                                    <th class="px-6 py-4 text-center font-bold">SL</th>
                                    <th class="px-6 py-4 text-right font-bold">Tổng tiền</th>
                                    <th class="px-6 py-4 text-center font-bold">Thời gian</th>
                                    <th class="px-6 py-4 text-right font-bold">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($orders as $order)
                                <tr class="group hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="font-mono font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md text-xs">#{{ $order->id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-800 text-base mb-0.5">{{ $order->product_name }}</div>
                                        <div class="text-xs text-slate-400 font-medium flex items-center gap-1">
                                            <i class="fa-solid fa-tag text-[10px]"></i> ID: {{ $order->product_id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg text-xs font-bold border border-blue-100">{{ $order->quantity }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-red-500 text-base">{{ number_format($order->total_price) }}đ</span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-slate-500 font-medium">
                                        {{ $order->created_at->format('H:i d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="viewOrder('{{ $order->id }}')" 
                                                class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 font-bold text-xs hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm group-hover:shadow">
                                            <i class="fa-regular fa-eye mr-2"></i> Xem hàng
                                        </button>
                                        <textarea id="order-content-{{ $order->id }}" class="hidden">{{ $order->data }}</textarea>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- MOBILE VIEW (CARDS) - Hiện trên Mobile -->
                <div class="md:hidden space-y-4">
                    @foreach($orders as $order)
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 relative overflow-hidden">
                        <!-- Decor line -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-400 to-purple-400"></div>
                        
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded">#{{ $order->id }}</span>
                                <span class="text-xs text-slate-400">{{ $order->created_at->format('d/m H:i') }}</span>
                            </div>
                            <span class="font-bold text-red-500 bg-red-50 px-2 py-1 rounded-lg text-sm border border-red-100">
                                {{ number_format($order->total_price) }}đ
                            </span>
                        </div>

                        <div class="mb-4">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight mb-1">{{ $order->product_name }}</h3>
                            <div class="flex items-center gap-3 text-xs text-slate-500">
                                <span class="bg-slate-50 px-2 py-0.5 rounded border border-slate-100">ID: {{ $order->product_id }}</span>
                                <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-bold">x{{ $order->quantity }} cái</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="viewOrder('{{ $order->id }}')" class="flex-1 bg-slate-900 text-white py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-slate-900/20 active:scale-[0.98] transition-transform flex items-center justify-center gap-2">
                                <i class="fa-regular fa-eye"></i> Xem ngay
                            </button>
                            <textarea id="order-content-mobile-{{ $order->id }}" class="hidden">{{ $order->data }}</textarea>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal View Order (Cải tiến) -->
    <div id="orderModal" class="fixed inset-0 z-[60] hidden transition-opacity duration-300 opacity-0" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl scale-95 duration-300" id="modalPanel">
                
                <!-- Header -->
                <div class="bg-slate-50 px-4 py-3 sm:px-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-base font-bold leading-6 text-slate-900" id="modal-title">
                        <i class="fa-solid fa-receipt text-blue-500 mr-2"></i> Chi tiết đơn hàng
                    </h3>
                    <button type="button" class="text-slate-400 hover:text-slate-500 focus:outline-none" onclick="closeModal()">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-4 py-5 sm:p-6">
                    <div class="mb-2 flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Dữ liệu đơn hàng</label>
                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Live Data</span>
                    </div>
                    <div class="relative group">
                        <textarea id="modalContent" class="block w-full rounded-xl border-0 bg-slate-800 py-3 px-4 text-green-400 shadow-inner ring-1 ring-inset ring-slate-700 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-xs sm:leading-6 font-mono h-48 resize-none selection:bg-green-900 selection:text-white" readonly></textarea>
                        <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <span class="text-[10px] text-slate-500 bg-slate-900/80 px-2 py-1 rounded">Read Only</span>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        <i class="fa-solid fa-circle-info text-blue-400"></i> Hãy copy toàn bộ dữ liệu trên để sử dụng.
                    </p>
                </div>

                <!-- Footer -->
                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="button" onclick="copyModalContent()" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-3 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all active:scale-95 items-center gap-2">
                        <i class="fa-regular fa-copy"></i> Sao chép tất cả
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-3 py-2.5 text-sm font-bold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-5 right-5 z-[70] flex flex-col gap-2"></div>

    <script>
        // Modal Logic
        const modal = document.getElementById('orderModal');
        const modalPanel = document.getElementById('modalPanel');

        function viewOrder(id) {
            // Lấy content từ desktop hoặc mobile textarea (ưu tiên tìm ID nào có trước)
            let content = document.getElementById('order-content-' + id)?.value;
            if(!content) content = document.getElementById('order-content-mobile-' + id)?.value;

            document.getElementById('modalContent').value = content;
            
            modal.classList.remove('hidden');
            // Animation In
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalPanel.classList.remove('scale-95');
                modalPanel.classList.add('scale-100');
            }, 10);
        }

        function closeModal() {
            // Animation Out
            modal.classList.add('opacity-0');
            modalPanel.classList.remove('scale-100');
            modalPanel.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Toast Logic Pro
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            // Icon & Color Setup
            const icons = {
                success: '<i class="fa-solid fa-circle-check"></i>',
                error: '<i class="fa-solid fa-circle-xmark"></i>'
            };
            const colors = {
                success: 'bg-slate-900 text-white shadow-green-500/20', // Style tối sang trọng
                error: 'bg-red-500 text-white shadow-red-500/30'
            };

            toast.className = `flex items-center gap-3 px-4 py-3 rounded-xl shadow-xl min-w-[300px] toast-enter toast-enter-active ${colors[type] || colors.success}`;
            toast.innerHTML = `
                <span class="text-xl">${icons[type]}</span>
                <span class="font-bold text-sm">${message}</span>
            `;

            container.appendChild(toast);

            // Auto Remove
            setTimeout(() => {
                toast.classList.remove('toast-enter-active');
                toast.classList.add('toast-exit-active');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function copyModalContent() {
            const content = document.getElementById('modalContent');
            content.select();
            content.setSelectionRange(0, 99999); // For mobile
            
            try {
                navigator.clipboard.writeText(content.value).then(() => {
                    showToast('Đã sao chép nội dung thành công!');
                });
            } catch (err) {
                // Fallback
                document.execCommand('copy');
                showToast('Đã sao chép nội dung!');
            }
        }
    </script>
</x-app-layout>