document.addEventListener('DOMContentLoaded', () => {
    // --- STATE ---
    const state = {
        selectedProduct: null,
        selectedCategory: null, // Thêm state danh mục đang chọn
        categories: [],
        products: [] 
    };

    // --- DOM ELEMENTS ---
    const els = {
        balance: document.getElementById('userBalance'),
        username: document.getElementById('userName'),
        productList: document.getElementById('productList'),
        categoryList: document.getElementById('categoryList'), // Sidebar danh mục
        buyForm: document.getElementById('buyForm'),
        productNameInput: document.getElementById('selectedProductName'),
        productIdInput: document.getElementById('selectedProductId'),
        amountInput: document.getElementById('amount'),
        totalPriceDisplay: document.getElementById('totalPrice'),
        resultArea: document.getElementById('resultArea'),
        btnBuy: document.getElementById('btnBuy'),
    };

    // --- HELPER ---
    const money = (amount) => {
        const num = parseFloat(amount) || 0;
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);
    };

    const getFlagUrl = (code) => {
        if (!code || code.length !== 2) return null;
        return `https://flagcdn.com/24x18/${code.toLowerCase()}.png`;
    };

    // --- API CALLS ---
    
    // 1. Load Profile
    async function loadProfile() {
        try {
            const res = await fetch('/tool/profile');
            const result = await res.json();
            
            if (result.status === 'success' && result.data) {
                if(els.balance) els.balance.innerText = money(result.data.balance);
                if(els.username) els.username.innerText = result.data.username || 'User';
            }
        } catch (e) {
            console.error('Lỗi tải thông tin user:', e);
        }
    }

    // 2. Load Products & Categories
    async function loadProducts() {
        // Hien thi loading
        if (els.productList) els.productList.innerHTML = '<div class="text-center py-20 text-slate-400"><i class="fa-solid fa-spinner fa-spin text-3xl mb-3"></i><p>Đang tải dữ liệu...</p></div>';
        
        try {
            const res = await fetch('/tool/products');
            const result = await res.json();
            
            if (result.success && Array.isArray(result.data)) {
                state.products = result.data;
                
                // --- UPDATE: RENDER CATEGORIES ---
                renderCategories(state.products);
                
                // Render All Products Init
                renderProducts(state.products);
            } else {
                if (els.productList) els.productList.innerHTML = '<div class="text-center text-red-500 py-10">Không tải được danh sách sản phẩm.</div>';
            }
        } catch (e) {
            console.error(e);
            if (els.productList) els.productList.innerHTML = '<div class="text-center text-red-500 py-10">Lỗi kết nối máy chủ.</div>';
        }
    }

    // --- RENDER CATEGORIES (MỚI) ---
    function renderCategories(products) {
        if (!els.categoryList) return;
        
        // 1. Lọc ra danh sách tên danh mục duy nhất (Unique)
        // Set giúp loại bỏ trùng lặp
        const categories = [...new Set(products.map(p => p.category_name))].filter(Boolean);
        
        els.categoryList.innerHTML = '';

        // Helper tạo item danh mục
        const createCategoryItem = (name, isAll = false) => {
            const li = document.createElement('li');
            const isActive = isAll ? (state.selectedCategory === null) : (state.selectedCategory === name);
            
            // Tính số lượng sản phẩm trong danh mục (nếu không phải All)
            const count = isAll ? products.length : products.filter(p => p.category_name === name).length;

            li.innerHTML = `
                <div class="cursor-pointer px-3 py-2.5 rounded-lg text-sm font-medium transition-all flex justify-between items-center group
                    ${isActive 
                        ? 'bg-red-50 text-red-600 border-l-4 border-red-500 shadow-sm' 
                        : 'text-slate-600 hover:bg-slate-50 hover:text-red-500 hover:pl-4'}" 
                    style="transition: all 0.2s ease">
                    
                    <span class="truncate">${name}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full ${isActive ? 'bg-red-200 text-red-700' : 'bg-slate-100 text-slate-500 group-hover:bg-red-100 group-hover:text-red-600'}">
                        ${count}
                    </span>
                </div>
            `;
            
            li.addEventListener('click', () => {
                state.selectedCategory = isAll ? null : name;
                
                // Re-render danh mục để cập nhật trạng thái Active
                renderCategories(state.products);
                
                // Lọc sản phẩm
                if (state.selectedCategory) {
                    const filtered = state.products.filter(p => p.category_name === state.selectedCategory);
                    renderProducts(filtered);
                } else {
                    renderProducts(state.products);
                }
            });
            
            return li;
        };

        // 2. Thêm nút "Tất cả"
        els.categoryList.appendChild(createCategoryItem('Tất cả sản phẩm', true));

        // 3. Thêm các danh mục từ API
        categories.forEach(cat => {
            els.categoryList.appendChild(createCategoryItem(cat));
        });
    }

    // --- RENDER PRODUCTS ---
    function renderProducts(products) {
        if (!els.productList) return;
        els.productList.innerHTML = '';

        if (products.length === 0) {
            els.productList.innerHTML = `
                <div class="text-center py-10 flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                    <p>Không có sản phẩm nào trong mục này.</p>
                </div>`;
            return;
        }

        const grid = document.createElement('div');
        // Sử dụng Grid cho đẹp
        grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4';

        products.forEach(p => {
            const card = document.createElement('div');
            const isSoldOut = parseInt(p.amount) < 1;
            const isSelected = state.selectedProduct?.id === p.id;
            
            // Style card
            card.className = `
                bg-white p-4 rounded-xl border transition-all duration-300 relative flex flex-col
                ${isSelected ? 'border-red-500 ring-1 ring-red-500 shadow-md bg-red-50/30' : 'border-slate-200 hover:border-red-400 hover:shadow-lg hover:-translate-y-1'}
                ${isSoldOut ? 'opacity-60 grayscale cursor-not-allowed' : 'cursor-pointer'}
            `;
            
            // Flag image
            let flagHtml = '';
            if (p.country) {
                const flag = getFlagUrl(p.country);
                if (flag) flagHtml = `<img src="${flag}" class="w-5 h-auto rounded-sm shadow-sm" alt="${p.country}">`;
            }

            // Nội dung Card
            card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-2">
                        ${flagHtml}
                        <h3 class="font-bold text-slate-700 text-sm line-clamp-2 leading-tight min-h-[2.5em]">${p.name}</h3>
                    </div>
                    ${p.icon ? `<img src="${p.icon}" class="w-6 h-6 object-contain opacity-80">` : ''}
                </div>
                
                <div class="mt-auto pt-3 border-t border-slate-100 flex justify-between items-end">
                    <div>
                        <div class="text-xs text-slate-500 mb-0.5">Hiện có: <span class="font-bold ${isSoldOut ? 'text-red-500' : 'text-green-600'}">${p.amount}</span></div>
                        <div class="text-red-600 font-extrabold text-lg leading-none">${money(p.price)}</div>
                    </div>
                    <button class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors ${isSelected ? 'bg-red-600 text-white shadow-lg shadow-red-500/40' : 'bg-slate-100 text-slate-600 hover:bg-red-500 hover:text-white'}">
                        ${isSelected ? '<i class="fa-solid fa-check"></i> Đã chọn' : 'Chọn'}
                    </button>
                </div>
            `;

            if (!isSoldOut) {
                card.addEventListener('click', () => selectProduct(p));
            }

            grid.appendChild(card);
        });

        els.productList.appendChild(grid);
    }

    function selectProduct(product) {
        state.selectedProduct = product;
        
        // Update form
        if (els.productNameInput) els.productNameInput.value = product.name;
        if (els.productIdInput) els.productIdInput.value = product.id;
        
        // Reset amount
        if (els.amountInput) els.amountInput.value = 1;
        
        // Re-render UI (để highlight card được chọn)
        // Lọc lại theo category hiện tại để không bị nhảy list
        const currentList = state.selectedCategory 
            ? state.products.filter(p => p.category_name === state.selectedCategory)
            : state.products;
        renderProducts(currentList);
        
        updateTotal();

        // Mobile scroll
        if(window.innerWidth < 1024 && els.buyForm) {
            els.buyForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function updateTotal() {
        if (!state.selectedProduct) {
            if (els.totalPriceDisplay) els.totalPriceDisplay.innerText = '0đ';
            return;
        }
        
        const amount = parseInt(els.amountInput?.value) || 0;
        const total = amount * state.selectedProduct.price;
        if (els.totalPriceDisplay) els.totalPriceDisplay.innerText = money(total);
        
        if (els.btnBuy) {
            els.btnBuy.disabled = amount <= 0 || amount > state.selectedProduct.amount;
            if(amount > state.selectedProduct.amount) {
                alert(`Kho chỉ còn ${state.selectedProduct.amount} sản phẩm!`);
                els.amountInput.value = state.selectedProduct.amount;
                updateTotal();
            }
        }
    }

    // --- BUY ACTION ---
    if (els.btnBuy) {
        els.btnBuy.addEventListener('click', async () => {
            if (!state.selectedProduct) {
                alert('Vui lòng chọn sản phẩm trước!');
                return;
            }

            const amount = els.amountInput.value;
            const confirmMsg = `Xác nhận mua ${amount} ${state.selectedProduct.name}?\nTổng tiền: ${els.totalPriceDisplay.innerText}`;
            
            if (!confirm(confirmMsg)) return;

            // UI Loading
            els.btnBuy.disabled = true;
            const originalBtnText = els.btnBuy.innerHTML;
            els.btnBuy.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            if (els.resultArea) els.resultArea.classList.add('hidden');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                const res = await fetch('/tool/buy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        product_id: state.selectedProduct.id,
                        amount: amount,
                        product_name: state.selectedProduct.name,
                        current_price: state.selectedProduct.price
                    })
                });

                const result = await res.json();

                if (res.ok && (result.status === 'success' || result.success)) {
                    showSuccess(result);
                    loadProfile(); // Cập nhật tiền
                    loadProducts(); // Cập nhật kho
                } else {
                    showError(result);
                }

            } catch (e) {
                console.error(e);
                showError({ msg: 'Lỗi hệ thống. Vui lòng thử lại.' });
            } finally {
                els.btnBuy.disabled = false;
                els.btnBuy.innerHTML = originalBtnText;
            }
        });
    }

    function showSuccess(result) {
        if (!els.resultArea) return;
        els.resultArea.classList.remove('hidden');
        els.resultArea.className = 'mt-4 animate-fade-in-up';
        
        const apiData = result.data || {};
        // Data trả về có thể là array hoặc object tùy API
        const dataContent = Array.isArray(apiData) ? apiData : (apiData.data || apiData);
        const accounts = Array.isArray(dataContent) ? dataContent.join('\n') : JSON.stringify(dataContent);
        
        els.resultArea.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center gap-2 text-green-700 font-bold mb-2">
                    <i class="fa-solid fa-circle-check"></i> ${result.msg || 'Giao dịch thành công!'}
                </div>
                <div class="relative">
                    <textarea class="w-full h-32 p-3 text-xs font-mono bg-white border border-green-200 rounded-lg focus:outline-none resize-none text-slate-700" readonly>${accounts}</textarea>
                    <button onclick="navigator.clipboard.writeText(this.previousElementSibling.value); this.innerHTML='<i class=\\'fa-solid fa-check\\'></i> Đã Copy';" class="absolute top-2 right-2 bg-green-100 hover:bg-green-200 text-green-700 text-xs px-2 py-1 rounded transition">
                        <i class="fa-regular fa-copy"></i> Copy
                    </button>
                </div>
                <div class="mt-2 text-xs text-green-600">
                    * Vui lòng lưu lại dữ liệu này ngay.
                </div>
            </div>
        `;
    }

    function showError(result) {
        if (!els.resultArea) return;
        els.resultArea.classList.remove('hidden');
        els.resultArea.className = 'mt-4 animate-fade-in-up';
        
        els.resultArea.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-600 mt-0.5"></i>
                <div>
                    <div class="text-red-700 font-bold text-sm">Giao dịch thất bại</div>
                    <div class="text-red-600 text-xs mt-1">${result.msg || result.message || 'Lỗi không xác định.'}</div>
                </div>
            </div>
        `;
    }

    // --- INIT ---
    loadProfile();
    loadProducts();
    
    if (els.amountInput) els.amountInput.addEventListener('input', updateTotal);
});