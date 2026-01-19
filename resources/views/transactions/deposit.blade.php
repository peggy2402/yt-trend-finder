<x-app-layout>
    {{-- Custom Style cho Toast & Animation --}}
    <style>
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        .toast-enter { animation: slideInRight 0.3s ease-out forwards; }
        .toast-exit { animation: fadeOut 0.3s ease-in forwards; }
        
        /* Ẩn nút tăng giảm mặc định của input number */
        input[type=number]::-webkit-inner-spin-button, 
        input[type=number]::-webkit-outer-spin-button { 
            -webkit-appearance: none; 
            margin: 0; 
        }
    </style>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-xl text-slate-100 leading-tight flex items-center gap-3">
                <div class="relative">
                    <div class="absolute inset-0 bg-green-200 blur rounded-full opacity-50"></div>
                    <span class="relative w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30">
                        <i class="fa-solid fa-wallet"></i>
                    </span>
                </div>
                <span>{{ __('Nạp tiền tài khoản') }}</span>
            </h2>
            <div class="hidden md:flex items-center gap-2 text-sm font-medium text-slate-100 bg-slate-800 px-3 py-1.5 rounded-full border border-slate-200 shadow-sm hover:bg-slate-500">
                <i class="fa-solid fa-headset text-green-500"></i> <a href="https://zalo.me/0862587229">Hỗ trợ 24/7</a>
            </div>
        </div>
    </x-slot>

    <!-- Toast Container (Góc trên phải) -->
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] flex flex-col gap-3 pointer-events-none w-full max-w-xs sm:max-w-sm px-4 sm:px-0"></div>

    <!-- Modal Thông báo Thành công -->
    <div id="successModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 p-8 transform scale-90 transition-all duration-300 relative overflow-hidden">
            <!-- Confetti decoration background -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none opacity-10">
                <div class="absolute top-[-50%] left-[-50%] w-[200%] h-[200%] bg-[url('https://www.transparenttextures.com/patterns/confetti.png')]"></div>
            </div>

            <div class="text-center relative z-10">
                <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                    <div class="absolute inset-0 bg-green-400 rounded-full opacity-20 animate-ping"></div>
                    <i class="fa-solid fa-check text-5xl text-green-500 drop-shadow-sm"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">Thành công!</h3>
                <p class="text-slate-500 mb-8 font-medium">Bạn vừa nạp thành công <br> <strong class="text-green-600 text-xl" id="receivedAmount">0đ</strong></p>
                
                <a href="{{ route('dashboard') }}" class="group block w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 rounded-2xl shadow-xl shadow-slate-900/20 transition-all active:scale-[0.98]">
                    Quay lại trang mua hàng <i class="fa-solid fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="py-6 md:py-12 bg-slate-50 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 px-4">
            
            <!-- Banner Thông Báo -->
            <div class="mb-8 relative overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-2xl shadow-slate-900/10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-green-500/20 rounded-full blur-[80px] -mr-16 -mt-16 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 w-40 h-40 bg-blue-500/10 rounded-full blur-[60px] -ml-10 -mb-10 pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-xl mb-2 flex items-center gap-2">
                            <span class="bg-yellow-500/20 text-yellow-300 p-1.5 rounded-lg"><i class="fa-solid fa-bolt animate-pulse"></i></span> 
                            Hệ thống nạp tự động 24/7
                        </h3>
                        <p class="text-slate-300 text-sm md:text-base leading-relaxed max-w-2xl">
                            Hệ thống tự động xác nhận giao dịch trong <strong>1-3 phút</strong>. Vui lòng giữ nguyên nội dung chuyển khoản để được cộng tiền nhanh nhất.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                
                <!-- CỘT TRÁI: FORM NHẬP -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100">
                        <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                                <span class="bg-blue-50 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-black">1</span>
                                Nhập số tiền
                            </h3>
                            <span class="text-xs font-semibold bg-slate-100 text-slate-500 px-2 py-1 rounded">VNĐ</span>
                        </div>
                        
                        <div class="space-y-6">
                            <!-- Input Amount -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-600 mb-2 ml-1">Số tiền muốn nạp</label>
                                <div class="relative group">
                                    <input type="number" id="depositAmount" 
                                        class="block w-full bg-slate-50 border-0 text-slate-900 rounded-2xl p-5 pr-16 text-2xl font-bold placeholder:text-slate-300 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all shadow-inner" 
                                        placeholder="0" min="10000" step="10000">
                                    <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none">
                                        <span class="font-black text-slate-400 text-lg">đ</span>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-400 mt-2 ml-1 flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-info text-blue-400"></i> Tối thiểu: 10.000đ
                                </p>
                            </div>

                            <!-- Nút chọn nhanh -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 ml-1">Chọn nhanh mệnh giá</label>
                                <div class="grid grid-cols-3 sm:grid-cols-3 gap-3">
                                    @foreach([20000, 50000, 100000, 200000, 500000, 1000000] as $amount)
                                        <button onclick="setAmount({{ $amount }})" 
                                            class="py-3 px-2 rounded-xl border border-slate-100 bg-white hover:border-blue-500 hover:bg-blue-50 hover:text-blue-600 text-slate-600 font-bold text-sm transition-all shadow-sm hover:shadow-md active:scale-95">
                                            {{ number_format($amount/1000) }}k
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Nội dung chuyển khoản -->
                            <div class="relative group cursor-pointer" onclick="copySyntax()">
                                <div class="absolute inset-0 bg-yellow-400/20 blur-lg rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="relative bg-gradient-to-br from-yellow-50 to-orange-50 p-5 rounded-2xl border border-yellow-100 hover:border-yellow-300 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-[10px] sm:text-xs font-bold text-yellow-700/70 uppercase mb-1 tracking-wide">Nội dung bắt buộc</p>
                                            <div class="text-xl sm:text-2xl font-black text-slate-800 tracking-wider font-mono select-all break-all" id="syntaxText">ZT{{ Auth::id() }}CKNH</div>
                                        </div>
                                        <div class="bg-white p-2.5 rounded-xl text-yellow-600 shadow-sm group-hover:scale-110 transition-transform">
                                            <i class="fa-regular fa-copy text-lg"></i>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-yellow-200/50 flex items-center gap-2 text-xs font-medium text-yellow-700">
                                        <span class="animate-bounce-horizontal"><i class="fa-solid fa-arrow-pointer"></i></span>
                                        Bấm vào khung để sao chép
                                    </div>
                                </div>
                            </div>

                            <!-- Nút Tạo mã -->
                            <button onclick="generateQR()" class="w-full relative overflow-hidden bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 rounded-2xl shadow-xl shadow-slate-900/20 transition-all active:scale-[0.98] group">
                                <span class="relative z-10 flex items-center justify-center gap-3 text-lg">
                                    <i class="fa-solid fa-qrcode group-hover:rotate-12 transition-transform"></i> TẠO MÃ QR THANH TOÁN
                                </span>
                                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            </button>
                        </div>
                    </div>

                    <!-- Lưu ý -->
                    <div class="bg-blue-50/50 p-6 rounded-3xl border border-blue-100/50">
                        <h4 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-sm uppercase tracking-wide">
                            <i class="fa-solid fa-shield-halved text-blue-500"></i> Lưu ý quan trọng
                        </h4>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span>Nhập chính xác <strong>Nội dung chuyển khoản</strong> để tiền vào ví tự động.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-check text-green-500 mt-1"></i>
                                <span>Nếu sai nội dung, vui lòng liên hệ Admin qua <a href="https://zalo.me/0862587229">Zalo</a> để được hỗ trợ.</span>
                            </li>
                            <center>
                                <img src="https://sf-static.upanhlaylink.com/img/image_20260119b807f0816508b7c75eacd0a55d4a620c.jpg" alt="HỖ TRỢ 24/7" class="w-20 h-20">
                            </center>
                        </ul>
                    </div>
                </div>

                <!-- CỘT PHẢI: HIỂN THỊ QR -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-6 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 sticky top-6">
                        <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                                <span class="bg-purple-50 text-purple-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-black">2</span>
                                Quét mã
                            </h3>
                            <span class="text-xs font-medium text-slate-400"><i class="fa-solid fa-lock"></i> Bảo mật</span>
                        </div>

                        <div class="flex flex-col items-center justify-center text-center min-h-[300px]">
                            <!-- Trạng thái chờ -->
                            <div id="qrPlaceholder" class="w-full flex flex-col items-center justify-center py-10 opacity-60">
                                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-4 border-2 border-dashed border-slate-200">
                                    <i class="fa-solid fa-qrcode text-4xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-400 font-medium text-sm">Nhập số tiền và bấm <br>"Tạo mã QR" để hiển thị</p>
                            </div>
                            
                            <!-- Kết quả QR -->
                            <div id="qrResult" class="hidden w-full animate-fade-in-up">
                                <div class="relative group perspective-1000">
                                    <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-2xl mb-6 inline-block transform transition-transform group-hover:rotate-x-2">
                                        <img id="qrImage" src="" class="max-w-full h-auto rounded-xl" alt="VietQR">
                                        <!-- Logo ngân hàng giữa QR -->
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-1.5 rounded-full shadow-lg">
                                            <i class="fa-solid fa-building-columns text-blue-600 text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="bankInfo" class="w-full bg-slate-50 rounded-2xl border border-slate-200 p-5 text-left space-y-4 relative overflow-hidden">
                                    <!-- Decorative Circle -->
                                    <div class="absolute top-0 right-0 w-20 h-20 bg-white/50 rounded-full -mr-10 -mt-10 pointer-events-none"></div>

                                    <div class="flex justify-between items-center border-b border-slate-200/60 pb-3">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ngân hàng</span>
                                        <span class="font-bold text-slate-800 flex items-center gap-2 text-sm">
                                            <img src="https://img.vietqr.io/image/MB-0000716679906-compact.png" class="h-6 w-auto hidden" onerror="this.style.display='none'"> 
                                            MB BANK
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-200/60 pb-3">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Chủ tài khoản</span>
                                        <span class="font-bold text-slate-800 text-sm">TRAN VAN CHIEN</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-1 group cursor-pointer" onclick="copyAccountNo()">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Số tài khoản</span>
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-black text-blue-600 text-lg" id="accountNo">0000716679906</span>
                                            <i class="fa-regular fa-copy text-slate-400 group-hover:text-blue-600 transition-colors text-xs"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-center gap-3 text-xs text-blue-600 font-bold bg-blue-50/80 py-3 rounded-xl">
                                    <span class="relative flex h-2.5 w-2.5">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                                    </span>
                                    Đang chờ hệ thống xác nhận...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script xử lý Logic -->
    <script>
        // Set số dư hiện tại của user (Lấy từ PHP)
        let currentBalance = {{ Auth::user()->balance }};
        let checkInterval = null;

        // --- TOAST SYSTEM (Thay thế Alert) ---
        const toastContainer = document.getElementById('toast-container');

        function showToast(message, type = 'success') {
            // Định nghĩa style theo type
            const styles = {
                success: { icon: '<i class="fa-solid fa-circle-check"></i>', color: 'bg-green-500', text: 'text-white' },
                error: { icon: '<i class="fa-solid fa-circle-xmark"></i>', color: 'bg-red-500', text: 'text-white' },
                info: { icon: '<i class="fa-solid fa-circle-info"></i>', color: 'bg-slate-800', text: 'text-white' }
            };
            const style = styles[type] || styles.info;

            // Tạo element
            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-center w-full p-4 mb-3 rounded-xl shadow-lg transform transition-all duration-300 toast-enter ${style.color} ${style.text}`;
            toast.innerHTML = `
                <div class="text-xl mr-3">${style.icon}</div>
                <div class="text-sm font-semibold">${message}</div>
            `;

            // Thêm vào container
            toastContainer.appendChild(toast);

            // Tự động xóa sau 3s
            setTimeout(() => {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                toast.addEventListener('animationend', () => toast.remove());
            }, 3000);
        }

        // --- LOGIC GIAO DIỆN ---
        function setAmount(value) {
            const input = document.getElementById('depositAmount');
            input.value = value;
            input.focus();
            
            // Hiệu ứng Visual feedback
            input.classList.add('ring-4', 'ring-blue-200');
            setTimeout(() => input.classList.remove('ring-4', 'ring-blue-200'), 300);
        }

        function copySyntax() {
            const text = document.getElementById('syntaxText').innerText.trim();
            if(!text) return;
            
            navigator.clipboard.writeText(text).then(() => {
                showToast(`Đã sao chép nội dung: ${text}`, 'success');
            }).catch(() => {
                showToast('Không thể sao chép, vui lòng copy thủ công', 'error');
            });
        }

        function copyAccountNo() {
            const text = document.getElementById('accountNo').innerText.trim();
            navigator.clipboard.writeText(text).then(() => {
                showToast(`Đã sao chép số tài khoản`, 'success');
            });
        }

        function generateQR() {
            const amountInput = document.getElementById('depositAmount');
            const amount = amountInput.value;
            
            if(!amount || amount < 10000) {
                showToast('Vui lòng nạp tối thiểu 10.000đ', 'error');
                amountInput.focus();
                amountInput.classList.add('animate-shake'); // Thêm animation rung lắc nếu muốn
                setTimeout(() => amountInput.classList.remove('animate-shake'), 500);
                return;
            }

            const BANK_ID = 'MB'; 
            const ACCOUNT_NO = '0000716679906'; 
            const ACCOUNT_NAME = 'TRAN VAN CHIEN'; 
            const CONTENT = 'ZT{{Auth::id()}}CKNH'; 

            const qrUrl = `https://img.vietqr.io/image/${BANK_ID}-${ACCOUNT_NO}-print.png?amount=${amount}&addInfo=${CONTENT}&accountName=${encodeURIComponent(ACCOUNT_NAME)}`;

            // UI Updates
            document.getElementById('qrPlaceholder').classList.add('hidden');
            const resultDiv = document.getElementById('qrResult');
            const img = document.getElementById('qrImage');
            
            // Loading effect
            img.style.opacity = '0.3';
            img.src = qrUrl;
            
            img.onload = () => {
                img.style.opacity = '1';
                resultDiv.classList.remove('hidden');
                
                showToast('Đã tạo mã QR thành công!', 'success');

                // Scroll to QR on Mobile
                if(window.innerWidth < 1024) {
                    setTimeout(() => {
                        resultDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
                
                // Start Polling
                startCheckingBalance();
            };
            
            img.onerror = () => {
                showToast('Lỗi tạo mã QR, vui lòng thử lại', 'error');
                document.getElementById('qrPlaceholder').classList.remove('hidden');
            }
        }

        // --- HÀM KIỂM TRA SỐ DƯ (POLLING) ---
        function startCheckingBalance() {
            if (checkInterval) clearInterval(checkInterval);

            console.log("System: Start watching balance...");
            
            checkInterval = setInterval(async () => {
                try {
                    const res = await fetch('/tool/profile');
                    const data = await res.json();

                    if (data.status === 'success') {
                        const newBalance = parseFloat(data.data.balance);
                        
                        if (newBalance > currentBalance) {
                            clearInterval(checkInterval);
                            const addedAmount = newBalance - currentBalance;
                            showSuccessModal(addedAmount);
                        }
                    }
                } catch (e) {
                    console.error("Connection error:", e);
                }
            }, 3000);
        }

        function showSuccessModal(amount) {
            const modal = document.getElementById('successModal');
            const amountText = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
            
            document.getElementById('receivedAmount').innerText = amountText;
            
            // Play Sound (Optional)
            // const audio = new Audio('/sounds/success.mp3');
            // audio.play().catch(e => console.log('Audio autoplay blocked'));

            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.querySelector('.transform').classList.remove('scale-90');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }
    </script>
</x-app-layout>