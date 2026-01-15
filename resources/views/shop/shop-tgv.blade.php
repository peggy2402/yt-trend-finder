<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cửa Hàng Nguyên Liệu VIA/Clone</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <!-- Tailwind CSS (Optional but recommended) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="container mx-auto px-4 py-6 max-w-6xl">
        <!-- Header -->
        <header class="flex justify-between items-center mb-8 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center gap-3">
                <a href="/" class="text-gray-500 hover:text-blue-600 transition"><i class="fa-solid fa-arrow-left"></i></a>
                <div class="text-xl font-bold text-blue-600 uppercase tracking-tight">SHOP VIA <span class="text-black">AUTO</span></div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-sm font-semibold flex flex-col items-end">
                    <span id="userName" class="text-gray-600">Khách</span>
                    <span id="userBalance" class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-md border border-green-200">0đ</span>
                </div>
                <button id="themeToggle" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition text-gray-600">🌙</button>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- SIDEBAR -->
            <aside class="bg-white rounded-xl border p-4">
                <h3 class="font-bold mb-3">📂 Danh mục</h3>
                <ul id="categoryList" class="space-y-2 text-sm"></ul>
            </aside>
            <!-- Left Column: Products -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700"><i class="fa-solid fa-layer-group mr-2 text-blue-500"></i> DANH SÁCH TÀI NGUYÊN</h3>
                    <span class="text-xs text-gray-400 bg-white px-2 py-1 rounded border border-gray-200">Auto Update</span>
                </div>
                <div id="productList" class="p-4 h-[600px] overflow-y-auto space-y-2">
                    <div class="text-center py-10 text-gray-400">Đang tải dữ liệu...</div>
                </div>
            </div>

            <!-- Right Column: Buy Form -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 sticky top-4" id="buyForm">
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-700"><i class="fa-solid fa-cart-shopping mr-2 text-green-500"></i> THANH TOÁN</h3>
                    </div>
                    
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sản phẩm</label>
                            <input type="text" id="selectedProductName" class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2.5 text-sm font-semibold text-gray-700 focus:outline-none" readonly placeholder="Chọn bên trái...">
                            <input type="hidden" id="selectedProductId">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Số lượng</label>
                                <input type="number" id="amount" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:border-blue-500 focus:outline-none" value="1" min="1">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mã giảm giá</label>
                                <input type="text" id="coupon" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:border-blue-500 focus:outline-none" placeholder="...">
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-4 border-t border-dashed border-gray-200 mt-2">
                            <span class="text-sm font-bold text-gray-500">Thành tiền:</span>
                            <span id="totalPrice" class="text-2xl font-black text-blue-600">0đ</span>
                        </div>

                        <button id="btnBuy" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition shadow-lg shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed">
                            MUA NGAY
                        </button>

                        <!-- Result Area -->
                        <div id="resultArea" class="hidden text-sm"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/shop-online/dashboard.js') }}"></script>
</body>
</html>