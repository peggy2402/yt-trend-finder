<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </span>
            {{ __('Lịch sử giao dịch') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#f3f4f6]">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
                <div class="p-6">
                    @if($orders->isEmpty())
                        <div class="text-center py-10 text-slate-400">
                            <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                            <p>Bạn chưa mua đơn hàng nào.</p>
                            <a href="{{ route('dashboard') }}" class="inline-block mt-4 text-blue-600 font-bold hover:underline">Mua ngay</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-slate-600">
                                <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3">Mã GD</th>
                                        <th class="px-6 py-3">Sản phẩm</th>
                                        <th class="px-6 py-3 text-center">SL</th>
                                        <th class="px-6 py-3 text-right">Tổng tiền</th>
                                        <th class="px-6 py-3 text-center">Thời gian</th>
                                        <th class="px-6 py-3 text-right">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                    <tr class="bg-white border-b hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-mono font-bold text-slate-500">#{{ $order->id }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-800">
                                            {{ $order->product_name }}
                                            <div class="text-xs text-slate-400 font-normal">ID SP: {{ $order->product_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ $order->quantity }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-red-500">
                                            {{ number_format($order->total_price) }}đ
                                        </td>
                                        <td class="px-6 py-4 text-center text-xs text-slate-500">
                                            {{ $order->created_at->format('H:i d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <!-- Nút xem chi tiết (Modal hoặc Copy) -->
                                            <button onclick="viewOrder('{{ $order->id }}')" class="text-blue-600 hover:text-blue-800 font-bold text-xs bg-blue-50 px-3 py-1.5 rounded hover:bg-blue-100 transition">
                                                <i class="fa-regular fa-eye mr-1"></i> Xem hàng
                                            </button>
                                            
                                            <!-- Hidden Content -->
                                            <textarea id="order-content-{{ $order->id }}" class="hidden">{{ $order->data }}</textarea>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Modal View Order -->
    <div id="orderModal" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up">
            <div class="p-4 border-b flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800">Chi tiết đơn hàng</h3>
                <button onclick="closeModal()" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-500 mb-2">Vui lòng copy dữ liệu bên dưới:</p>
                <textarea id="modalContent" class="w-full h-64 p-4 bg-slate-900 text-green-400 font-mono text-xs rounded-xl focus:outline-none" readonly></textarea>
                <div class="mt-4 flex justify-end">
                    <button onclick="copyModalContent()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition">
                        <i class="fa-regular fa-copy"></i> Copy Toàn bộ
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="toast-history"></div>
    <script>
        function viewOrder(id) {
            const content = document.getElementById('order-content-' + id).value;
            document.getElementById('modalContent').value = content;
            document.getElementById('orderModal').classList.remove('hidden');
            document.getElementById('orderModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('orderModal').classList.add('hidden');
            document.getElementById('orderModal').classList.remove('flex');
        }

        function copyModalContent() {
            const content = document.getElementById('modalContent');
            content.select();
            document.execCommand('copy');
            showToast('Đã copy vào bộ nhớ đệm!', 'success');
        }
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-history');

            const toast = document.createElement('div');
            toast.classList.add('toast', type);
            toast.innerText = message;

            container.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    </script>
</x-app-layout>