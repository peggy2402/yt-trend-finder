<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-slate-800 leading-tight flex items-center gap-2">
            <span class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-wallet"></i>
            </span>
            {{ __('Nạp tiền tài khoản') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-[#f3f4f6] min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Banner Thông Báo -->
            <div class="mb-8 bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="font-bold text-xl mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-yellow-400"></i> Hệ thống nạp tự động 24/7
                    </h3>
                    <p class="text-slate-400 text-sm">Tiền sẽ được cộng vào tài khoản sau 1-3 phút kể từ khi chuyển khoản thành công.</p>
                </div>
                <!-- Decor Background -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-green-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- CỘT TRÁI: FORM NHẬP (7 phần) -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200">
                        <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                            <span class="bg-green-100 text-green-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-pen-to-square"></i></span>
                            1. Nhập số tiền nạp
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Input Amount -->
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-2">Số tiền muốn nạp (VNĐ)</label>
                                <div class="relative group">
                                    <input type="number" id="depositAmount" class="w-full bg-slate-50 border-slate-200 rounded-xl p-4 pl-4 pr-16 text-xl font-bold text-green-600 placeholder-slate-400 focus:ring-2 focus:ring-green-500/20 focus:border-green-500 transition-all outline-none" placeholder="0" min="10000" step="10000">
                                    <span class="absolute right-4 top-4 font-bold text-slate-400">VNĐ</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-2 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info"></i> Tối thiểu: 10.000đ
                                </p>
                            </div>

                            <!-- Nút chọn nhanh (Quick Select) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-3">Chọn nhanh</label>
                                <div class="grid grid-cols-3 sm:grid-cols-3 gap-3">
                                    <button onclick="setAmount(20000)" class="py-2.5 px-3 rounded-lg border border-slate-200 bg-white hover:border-green-500 hover:text-green-600 hover:bg-green-50 font-semibold text-sm transition-all shadow-sm">20.000</button>
                                    <button onclick="setAmount(50000)" class="py-2.5 px-3 rounded-lg border border-slate-200 bg-white hover:border-green-500 hover:text-green-600 hover:bg-green-50 font-semibold text-sm transition-all shadow-sm">50.000</button>
                                    <button onclick="setAmount(100000)" class="py-2.5 px-3 rounded-lg border border-slate-200 bg-white hover:border-green-500 hover:text-green-600 hover:bg-green-50 font-semibold text-sm transition-all shadow-sm">100.000</button>
                                    <button onclick="setAmount(200000)" class="py-2.5 px-3 rounded-lg border border-slate-200 bg-white hover:border-green-500 hover:text-green-600 hover:bg-green-50 font-semibold text-sm transition-all shadow-sm">200.000</button>
                                    <button onclick="setAmount(500000)" class="py-2.5 px-3 rounded-lg border border-slate-200 bg-white hover:border-green-500 hover:text-green-600 hover:bg-green-50 font-semibold text-sm transition-all shadow-sm">500.000</button>
                                    <button onclick="setAmount(1000000)" class="py-2.5 px-3 rounded-lg border border-slate-200 bg-white hover:border-green-500 hover:text-green-600 hover:bg-green-50 font-semibold text-sm transition-all shadow-sm">1 Triệu</button>
                                </div>
                            </div>

                            <!-- Nội dung chuyển khoản (Quan trọng) -->
                            <div class="bg-yellow-50 p-5 rounded-xl border border-yellow-200 relative group cursor-pointer transition-all hover:bg-yellow-100" onclick="copySyntax()">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-bold text-yellow-800 uppercase mb-1">Nội dung chuyển khoản (Bắt buộc)</p>
                                        <div class="text-2xl font-black text-red-600 tracking-wider font-mono" id="syntaxText">ZT{{Auth::id()}}CKNH</div>
                                    </div>
                                    <div class="bg-white/50 p-2 rounded-lg text-yellow-700 group-hover:bg-white group-hover:text-yellow-800 transition">
                                        <i class="fa-regular fa-copy text-lg"></i>
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-yellow-700 opacity-80 flex items-center gap-1">
                                    <i class="fa-solid fa-hand-pointer"></i> Bấm vào khung này để copy nội dung
                                </div>
                            </div>

                            <!-- Nút Tạo mã -->
                            <button onclick="generateQR()" class="w-full bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2 text-lg">
                                <i class="fa-solid fa-qrcode"></i> TẠO MÃ QR
                            </button>
                        </div>
                    </div>

                    <!-- Lưu ý -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                        <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-question text-blue-500"></i> Lưu ý quan trọng
                        </h4>
                        <ul class="space-y-2 text-sm text-slate-600 list-disc list-inside">
                            <li>Vui lòng nhập chính xác <strong>Nội dung chuyển khoản</strong> để được cộng tiền tự động.</li>
                            <li>Nếu sai nội dung, vui lòng liên hệ Admin để được hỗ trợ cộng tay.</li>
                            <li>Hệ thống xử lý giao dịch tự động trong vài giây.</li>
                        </ul>
                    </div>
                </div>

                <!-- CỘT PHẢI: HIỂN THỊ QR (5 phần) -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 sticky top-6">
                        <h3 class="font-bold text-lg text-slate-800 mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                            <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm"><i class="fa-solid fa-mobile-screen-button"></i></span>
                            2. Quét mã thanh toán
                        </h3>

                        <div class="flex flex-col items-center justify-center text-center">
                            <!-- Trạng thái chờ -->
                            <div id="qrPlaceholder" class="py-12 px-6 border-2 border-dashed border-slate-200 rounded-xl w-full flex flex-col items-center bg-slate-50/50">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 text-slate-300 shadow-sm border border-slate-100">
                                    <i class="fa-solid fa-qrcode text-4xl"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Nhập số tiền và bấm <br>"Tạo mã QR" để hiển thị</p>
                            </div>
                            
                            <!-- Kết quả QR (Ẩn mặc định) -->
                            <div id="qrResult" class="hidden w-full animate-fade-in-up">
                                <div class="bg-white p-2 rounded-xl border border-green-200 shadow-lg mb-6 inline-block relative group">
                                    <!-- QR Image -->
                                    <img id="qrImage" src="" class="max-w-full h-auto rounded-lg" alt="VietQR">
                                    
                                    <!-- Logo Ngân Hàng chèn giữa (Decor) -->
                                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-1 rounded-full shadow-md">
                                        <i class="fa-solid fa-building-columns text-green-600 text-xl"></i>
                                    </div>
                                </div>
                                
                                <!-- Thông tin chi tiết -->
                                <div id="bankInfo" class="w-full bg-slate-50 rounded-xl border border-slate-200 p-5 text-left space-y-3 shadow-inner">
                                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Ngân hàng</span>
                                        <span class="font-bold text-slate-800 flex items-center gap-2">
                                            <img src="https://tse4.mm.bing.net/th/id/OIP.UaxzHFNzRx-nmr05GDFAmgHaHa?pid=Api&P=0&h=220" class="h-10 w-auto" alt="MB"> MB BANK
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center border-b border-slate-200 pb-2">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Chủ TK</span>
                                        <span class="font-bold text-slate-800">TRAN VAN CHIEN</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-1">
                                        <span class="text-xs font-bold text-slate-400 uppercase">Số TK</span>
                                        <span class="font-mono font-bold text-green-600 text-lg flex items-center gap-2 group cursor-pointer" onclick="navigator.clipboard.writeText('0000716679906'); alert('Đã copy số tài khoản')">
                                            0000716679906
                                            <i class="fa-regular fa-copy text-slate-400 group-hover:text-green-600 transition-colors text-sm"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-green-600 font-bold bg-green-50 py-2 rounded-lg">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                    Đang chờ thanh toán...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // Set amount from quick buttons
        function setAmount(value) {
            document.getElementById('depositAmount').value = value;
            // Hiệu ứng focus nhẹ để user biết đã nhận
            const input = document.getElementById('depositAmount');
            input.classList.add('ring-2', 'ring-green-500');
            setTimeout(() => input.classList.remove('ring-2', 'ring-green-500'), 300);
        }

        // Copy syntax helper
        function copySyntax() {
            const text = document.getElementById('syntaxText').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Đã copy nội dung chuyển khoản: ' + text);
            });
        }

        function generateQR() {
            const amount = document.getElementById('depositAmount').value;
            if(!amount || amount < 10000) {
                alert('Vui lòng nạp tối thiểu 10.000đ');
                document.getElementById('depositAmount').focus();
                return;
            }

            // CONFIG NGÂN HÀNG (Bro sửa STK ở đây và cả ở HTML phía trên nhé)
            const BANK_ID = 'MB'; 
            const ACCOUNT_NO = '0000716679906'; 
            const ACCOUNT_NAME = 'TRAN VAN CHIEN'; 
            const CONTENT = 'ZT{{Auth::id()}}CKNH'; 

            // API VietQR
            const qrUrl = `https://img.vietqr.io/image/${BANK_ID}-${ACCOUNT_NO}-print.png?amount=${amount}&addInfo=${CONTENT}&accountName=${encodeURIComponent(ACCOUNT_NAME)}`;

            // UI Updates
            document.getElementById('qrPlaceholder').classList.add('hidden');
            const resultDiv = document.getElementById('qrResult');
            const img = document.getElementById('qrImage');
            
            // Hiện loading giả 1 xíu cho mượt
            img.style.opacity = '0.5';
            img.src = qrUrl;
            
            img.onload = () => {
                img.style.opacity = '1';
                resultDiv.classList.remove('hidden');
                // Scroll tới QR trên mobile
                if(window.innerWidth < 1024) {
                    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            };
        }
    </script>
</x-app-layout>