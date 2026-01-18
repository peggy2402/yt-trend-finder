document.addEventListener('DOMContentLoaded', () => {
    // --- STATE ---
    const state = {
        selectedProduct: null,
        selectedCategory: null,
        categories: [],
        products: [],
        hideOutOfStock: true,
        searchQuery: '',
        currentPage: 1,
        itemsPerPage: 20,
        totalPages: 1,
        searchHistory: JSON.parse(localStorage.getItem('searchHistory') || '[]'),
        selectedSuggestionIndex: -1
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

    // Category icon mapping
    const getCategoryIcon = (categoryName) => {
        if (!categoryName) return '📦';
        
        const name = categoryName.toLowerCase();
        
        // Social Media
        if (name.includes('facebook') || name.includes('fb')) return '👥';
        if (name.includes('instagram') || name.includes('ig')) return '📸';
        if (name.includes('twitter') || name.includes('x')) return '🐦';
        if (name.includes('tiktok')) return '🎵';
        if (name.includes('youtube') || name.includes('yt')) return '▶️';
        if (name.includes('linkedin')) return '💼';
        
        // Services & Tools
        if (name.includes('gmail') || name.includes('email') || name.includes('mail')) return '📧';
        if (name.includes('google') || name.includes('gg')) return '🔍';
        if (name.includes('ads') || name.includes('quảng cáo')) return '📢';
        if (name.includes('vpn') || name.includes('proxy')) return '🔒';
        if (name.includes('cloud') || name.includes('drive')) return '☁️';
        if (name.includes('domain') || name.includes('hosting')) return '🌐';
        
        // Gaming & Entertainment  
        if (name.includes('game') || name.includes('steam')) return '🎮';
        if (name.includes('netflix') || name.includes('spotify')) return '🎬';
        
        // Finance & Payment
        if (name.includes('bank') || name.includes('ngân hàng')) return '🏦';
        if (name.includes('card') || name.includes('thẻ')) return '💳';
        if (name.includes('payment') || name.includes('thanh toán')) return '💰';
        
        // Default
        return '📦';
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
        // 1. Lọc ra danh sách tên danh mục duy nhất (Unique)
        // Set giúp loại bỏ trùng lặp
        const categories = [...new Set(products.map(p => p.category_name))].filter(Boolean);
        
        // Render desktop categories
        if (els.categoryList) {
            renderDesktopCategories(categories, products);
        }
        
        // Render mobile categories
        const mobileCategoryList = document.getElementById('mobileCategoryList');
        if (mobileCategoryList) {
            renderMobileCategories(categories, products);
        }
    }

    function renderDesktopCategories(categories, products) {
        if (!els.categoryList) return;
        
        els.categoryList.innerHTML = '';

        // Helper tạo item danh mục
        const createCategoryItem = (name, isAll = false) => {
            const li = document.createElement('li');
            const isActive = isAll ? (state.selectedCategory === null) : (state.selectedCategory === name);
            
            // Tính số lượng sản phẩm trong danh mục (nếu không phải All)
            const count = isAll ? products.length : products.filter(p => p.category_name === name).length;

            const icon = isAll ? '🏪' : getCategoryIcon(name);
            
            li.innerHTML = `
                <div class="cursor-pointer px-3 py-2.5 rounded-lg text-sm font-medium transition-all flex items-center gap-2 group
                    ${isActive 
                        ? 'bg-red-50 text-red-600 border-l-4 border-red-500 shadow-sm' 
                        : 'text-slate-600 hover:bg-slate-50 hover:text-red-500 hover:pl-4'}" 
                    style="transition: all 0.2s ease">
                    
                    <span class="text-base flex-shrink-0">${icon}</span>
                    <span class="truncate flex-1">${name}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold ${isActive ? 'bg-red-200 text-red-700' : 'bg-slate-100 text-slate-500 group-hover:bg-red-100 group-hover:text-red-600'}">
                        ${count}
                    </span>
                </div>
            `;
            
            li.addEventListener('click', () => {
                state.selectedCategory = isAll ? null : name;
                state.currentPage = 1; // Reset to page 1
                
                // Re-render danh mục để cập nhật trạng thái Active
                renderCategories(state.products);
                
                // Re-render với filters
                renderProducts(state.products);
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

    function renderMobileCategories(categories, products) {
        const mobileCategoryList = document.getElementById('mobileCategoryList');
        if (!mobileCategoryList) return;
        
        mobileCategoryList.innerHTML = '';

        // Helper tạo category pill cho mobile
        const createCategoryPill = (name, isAll = false) => {
            const pill = document.createElement('button');
            const isActive = isAll ? (state.selectedCategory === null) : (state.selectedCategory === name);
            const count = isAll ? products.length : products.filter(p => p.category_name === name).length;
            
            const icon = isAll ? '🏪' : getCategoryIcon(name);
            
            pill.className = `category-pill px-4 py-2 rounded-full text-xs font-semibold border transition-all flex-shrink-0 flex items-center gap-1.5 ${
                isActive 
                    ? 'bg-gradient-to-r from-primary to-red-600 text-white border-primary shadow-lg shadow-red-500/30' 
                    : 'bg-white text-slate-600 border-slate-200 hover:border-primary hover:text-primary'
            }`;
            
            pill.innerHTML = `
                <span class="text-sm">${icon}</span>
                <span>${name}</span>
                <span class="ml-0.5 ${isActive ? 'text-white/80' : 'text-slate-400'}">(${count})</span>
            `;
            
            pill.addEventListener('click', () => {
                state.selectedCategory = isAll ? null : name;
                state.currentPage = 1; // Reset to page 1
                renderCategories(state.products);
                renderProducts(state.products);
                
                // Scroll to products on mobile
                if (window.innerWidth < 1024) {
                    document.getElementById('productList')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
            
            return pill;
        };

        // Add "All" pill
        mobileCategoryList.appendChild(createCategoryPill('Tất cả', true));

        // Add category pills
        categories.forEach(cat => {
            mobileCategoryList.appendChild(createCategoryPill(cat));
        });
    }

    // --- FILTER & SEARCH PRODUCTS ---
    function getFilteredProducts(products) {
        let filtered = products;
        
        // 1. Filter by category
        if (state.selectedCategory) {
            filtered = filtered.filter(p => p.category_name === state.selectedCategory);
        }
        
        // 2. Filter out of stock
        if (state.hideOutOfStock) {
            filtered = filtered.filter(p => parseInt(p.amount) > 0);
        }
        
        // 3. Search filter
        if (state.searchQuery) {
            const query = state.searchQuery.toLowerCase();
            filtered = filtered.filter(p => 
                p.name.toLowerCase().includes(query) || 
                (p.category_name && p.category_name.toLowerCase().includes(query))
            );
        }
        
        return filtered;
    }

    // --- RENDER PRODUCTS ---
    function renderProducts(products) {
        if (!els.productList) return;
        
        // Apply filters
        const filtered = getFilteredProducts(products);
        
        els.productList.innerHTML = '';

        // Update stats
        updateStats(products, filtered);

        if (filtered.length === 0) {
            hidePagination();
            els.productList.innerHTML = `
                <div class="text-center py-10 flex flex-col items-center justify-center text-slate-400">
                    <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i>
                    <p>Không có sản phẩm nào phù hợp.</p>
                </div>`;
            return;
        }

        // Calculate pagination
        state.totalPages = Math.ceil(filtered.length / state.itemsPerPage);
        if (state.currentPage > state.totalPages) {
            state.currentPage = 1;
        }

        // Get products for current page
        const startIndex = (state.currentPage - 1) * state.itemsPerPage;
        const endIndex = startIndex + state.itemsPerPage;
        const paginatedProducts = filtered.slice(startIndex, endIndex);

        // Show pagination if needed
        if (filtered.length > state.itemsPerPage) {
            showPagination(filtered.length, startIndex, Math.min(endIndex, filtered.length));
        } else {
            hidePagination();
        }

        paginatedProducts.forEach(p => {
            const card = document.createElement('div');
            const isSoldOut = parseInt(p.amount) < 1;
            const isSelected = state.selectedProduct?.id === p.id;
            
            // Responsive card layout - better for mobile
            const isMobile = window.innerWidth < 640;
            
            if (isMobile) {
                // Mobile: Horizontal card layout
                card.className = `
                    product-card bg-white p-4 rounded-xl border transition-all duration-300 relative flex gap-4
                    ${isSelected ? 'border-red-500 ring-2 ring-red-200 shadow-lg bg-red-50/50' : 'border-slate-200'}
                    ${isSoldOut ? 'opacity-60 grayscale cursor-not-allowed' : 'cursor-pointer hover:shadow-lg'}
                `;
            } else {
                // Desktop: Vertical card layout
                card.className = `
                    product-card bg-white p-4 rounded-xl border transition-all duration-300 relative flex flex-col group
                    ${isSelected ? 'border-red-500 ring-2 ring-red-200 shadow-lg bg-red-50/50' : 'border-slate-200'}
                    ${isSoldOut ? 'opacity-60 grayscale cursor-not-allowed' : 'cursor-pointer hover:shadow-lg'}
                `;
            }
            
            // Flag image
            let flagHtml = '';
            if (p.country) {
                const flag = getFlagUrl(p.country);
                if (flag) flagHtml = `<img src="${flag}" class="w-5 h-auto rounded-sm shadow-sm" alt="${p.country}">`;
            }

            // Stock badge
            let stockBadge = '';
            if (!isSoldOut) {
                stockBadge = `
                    <div class="stock-badge">
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full ${
                            parseInt(p.amount) > 10 
                                ? 'bg-green-100 text-green-700 border border-green-200' 
                                : 'bg-yellow-100 text-yellow-700 border border-yellow-200'
                        }">
                            ${p.amount} có sẵn
                        </span>
                    </div>
                `;
            }

            // Nội dung Card - Responsive layout
            if (isMobile) {
                // Mobile horizontal layout
                card.innerHTML = `
                    ${stockBadge}
                    <div class="flex-1 flex flex-col justify-between min-w-0">
                        <div class="mb-2">
                            <div class="flex items-start gap-2 mb-1">
                                ${flagHtml}
                                <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2 flex-1">${p.name}</h3>
                            </div>
                            ${p.category_name ? `<p class="text-xs text-slate-500 mt-1">${p.category_name}</p>` : ''}
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-primary font-black text-lg">${money(p.price)}</div>
                            <button class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${
                                isSelected 
                                    ? 'bg-gradient-to-r from-primary to-red-600 text-white shadow-lg' 
                                    : 'bg-slate-100 text-slate-600'
                            }">
                                ${isSelected ? '✓' : 'Chọn'}
                            </button>
                        </div>
                    </div>
                    ${p.icon ? `<img src="${p.icon}" class="w-12 h-12 object-contain opacity-70 flex-shrink-0">` : ''}
                `;
            } else {
                // Desktop vertical layout
                card.innerHTML = `
                    ${stockBadge}
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-2 flex-1 min-w-0">
                            ${flagHtml}
                            <h3 class="font-bold text-slate-800 text-sm leading-tight line-clamp-2 flex-1">${p.name}</h3>
                        </div>
                        ${p.icon ? `<img src="${p.icon}" class="w-6 h-6 object-contain opacity-70 flex-shrink-0 ml-2">` : ''}
                    </div>
                    
                    <div class="mt-auto pt-3 border-t border-slate-100 flex justify-between items-center">
                        <div class="text-primary font-black text-xl">${money(p.price)}</div>
                        <button class="px-4 py-2 rounded-lg text-xs font-bold transition-all transform ${
                            isSelected 
                                ? 'bg-gradient-to-r from-primary to-red-600 text-white shadow-lg shadow-red-500/40 scale-105' 
                                : 'bg-slate-100 text-slate-600 group-hover:bg-primary group-hover:text-white'
                        }">
                            ${isSelected ? '<i class="fa-solid fa-check"></i> Đã chọn' : '<i class="fa-solid fa-cart-plus"></i> Chọn'}
                        </button>
                    </div>
                `;
            }

            if (!isSoldOut) {
            card.addEventListener('click', () => selectProduct(p));
            }

            els.productList.appendChild(card);
        });
    }

    // --- UPDATE STATS ---
    function updateStats(allProducts, filteredProducts) {
        const availableCount = allProducts.filter(p => parseInt(p.amount) > 0).length;
        const availableEl = document.getElementById('availableProducts');
        if (availableEl) availableEl.textContent = availableCount;
        
        // Update category count
        const categoryCountEl = document.getElementById('categoryCount');
        const mobileCategoryCountEl = document.getElementById('mobileCategoryCount');
        const uniqueCategories = [...new Set(allProducts.map(p => p.category_name))].filter(Boolean);
        if (categoryCountEl) categoryCountEl.textContent = uniqueCategories.length;
        if (mobileCategoryCountEl) mobileCategoryCountEl.textContent = `${uniqueCategories.length} danh mục`;
    }

    // --- PAGINATION FUNCTIONS ---
    function showPagination(total, start, end) {
        const container = document.getElementById('paginationContainer');
        if (!container) return;
        
        container.classList.remove('hidden');
        
        // Update info
        document.getElementById('totalProducts').textContent = total;
        document.getElementById('pageStart').textContent = start + 1;
        document.getElementById('pageEnd').textContent = end;
        
        // Update buttons
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (prevBtn) prevBtn.disabled = state.currentPage === 1;
        if (nextBtn) nextBtn.disabled = state.currentPage === state.totalPages;
        
        // Render page numbers
        renderPageNumbers();
    }

    function hidePagination() {
        const container = document.getElementById('paginationContainer');
        if (container) container.classList.add('hidden');
    }

    function renderPageNumbers() {
        const pageNumbersContainer = document.getElementById('pageNumbers');
        if (!pageNumbersContainer) return;
        
        pageNumbersContainer.innerHTML = '';
        
        // Show max 5 page numbers
        let startPage = Math.max(1, state.currentPage - 2);
        let endPage = Math.min(state.totalPages, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.className = `w-10 h-10 rounded-lg text-sm font-semibold transition ${
                i === state.currentPage 
                    ? 'bg-primary text-white shadow' 
                    : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
            }`;
            pageBtn.addEventListener('click', () => {
                state.currentPage = i;
                renderProducts(state.products);
                // Scroll to top of product list
                if (els.productList) {
                    els.productList.scrollTop = 0;
                }
            });
            pageNumbersContainer.appendChild(pageBtn);
        }
    }

    function goToPage(direction) {
        if (direction === 'prev' && state.currentPage > 1) {
            state.currentPage--;
        } else if (direction === 'next' && state.currentPage < state.totalPages) {
            state.currentPage++;
        }
        renderProducts(state.products);
        if (els.productList) {
            els.productList.scrollTop = 0;
        }
    }

    function selectProduct(product) {
        state.selectedProduct = product;
        
        // Update desktop form
        if (els.productNameInput) els.productNameInput.value = product.name;
        if (els.productIdInput) els.productIdInput.value = product.id;
        
        // Reset amount for both desktop and mobile
        if (els.amountInput) els.amountInput.value = 1;
        const mobileAmountInput = document.getElementById('mobileAmount');
        if (mobileAmountInput) mobileAmountInput.value = 1;
        
        // Update product display in desktop
        const selectedProductDisplay = document.getElementById('selectedProductDisplay');
        if (selectedProductDisplay) {
            selectedProductDisplay.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-primary"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 text-sm">${product.name}</h4>
                        <p class="text-xs text-slate-500">${product.category_name || 'Sản phẩm'}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-bold text-primary">${money(product.price)}</div>
                        <div class="text-[10px] text-green-600 font-semibold">${product.amount} có sẵn</div>
                    </div>
                </div>
            `;
        }
        
        // Update mobile selected product
        const mobileSelectedProduct = document.getElementById('mobileSelectedProduct');
        if (mobileSelectedProduct) {
            mobileSelectedProduct.innerHTML = `
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-slate-800 text-sm">${product.name}</h4>
                        <span class="text-xs text-green-600 font-semibold">${product.amount} có sẵn</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-xs text-slate-500">${product.category_name || 'Sản phẩm'}</p>
                        <div class="text-lg font-bold text-primary">${money(product.price)}</div>
                    </div>
                </div>
            `;
        }
        
        // Store price for calculations
        const selectedProductPrice = document.getElementById('selectedProductPrice');
        if (selectedProductPrice) selectedProductPrice.value = product.price;
        
        // Re-render products to show selected state
        renderProducts(state.products);
        
        updateTotal();
        updateMobileTotal();

        // Open mobile bottom sheet if on mobile
        if (window.innerWidth < 1024) {
            const openSheetFn = window.openMobileSheet;
            if (typeof openSheetFn === 'function') {
                openSheetFn();
            }
        }
    }

    function updateTotal() {
        if (!state.selectedProduct) {
            if (els.totalPriceDisplay) els.totalPriceDisplay.innerText = '0đ';
            const unitPriceEl = document.getElementById('unitPrice');
            const displayQtyEl = document.getElementById('displayQty');
            if (unitPriceEl) unitPriceEl.textContent = '0đ';
            if (displayQtyEl) displayQtyEl.textContent = '0';
            return;
        }
        
        const amount = parseInt(els.amountInput?.value) || 0;
        const total = amount * state.selectedProduct.price;
        if (els.totalPriceDisplay) els.totalPriceDisplay.innerText = money(total);
        
        // Update breakdown
        const unitPriceEl = document.getElementById('unitPrice');
        const displayQtyEl = document.getElementById('displayQty');
        if (unitPriceEl) unitPriceEl.textContent = money(state.selectedProduct.price);
        if (displayQtyEl) displayQtyEl.textContent = amount;
        
        if (els.btnBuy) {
            els.btnBuy.disabled = amount <= 0 || amount > state.selectedProduct.amount;
            if(amount > state.selectedProduct.amount) {
                alert(`Kho chỉ còn ${state.selectedProduct.amount} sản phẩm!`);
                els.amountInput.value = state.selectedProduct.amount;
                updateTotal();
            }
        }
    }

    function updateMobileTotal() {
        if (!state.selectedProduct) {
            const mobileTotalPriceEl = document.getElementById('mobileTotalPrice');
            if (mobileTotalPriceEl) mobileTotalPriceEl.textContent = '0đ';
            return;
        }
        
        const mobileAmountInput = document.getElementById('mobileAmount');
        const amount = parseInt(mobileAmountInput?.value) || 0;
        const total = amount * state.selectedProduct.price;
        
        const mobileTotalPriceEl = document.getElementById('mobileTotalPrice');
        if (mobileTotalPriceEl) mobileTotalPriceEl.textContent = money(total);
        
        const mobileBtnBuy = document.getElementById('mobileBtnBuy');
        if (mobileBtnBuy) {
            mobileBtnBuy.disabled = amount <= 0 || amount > state.selectedProduct.amount;
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

    // --- SEARCH FUNCTIONS ---
    function fuzzyMatch(str, pattern) {
        pattern = pattern.toLowerCase();
        str = str.toLowerCase();
        
        // Exact match gets highest priority
        if (str.includes(pattern)) return 3;
        
        // Fuzzy match - check if all characters of pattern exist in order
        let patternIdx = 0;
        for (let i = 0; i < str.length && patternIdx < pattern.length; i++) {
            if (str[i] === pattern[patternIdx]) {
                patternIdx++;
            }
        }
        return patternIdx === pattern.length ? 1 : 0;
    }

    function getSearchSuggestions(query) {
        if (!query || query.length < 1) return [];
        
        const suggestions = [];
        const seen = new Set();
        
        // 1. Get product suggestions
        state.products.forEach(p => {
            const score = fuzzyMatch(p.name, query);
            if (score > 0 && !seen.has(p.name.toLowerCase())) {
                suggestions.push({
                    type: 'product',
                    text: p.name,
                    category: p.category_name,
                    score: score,
                    data: p
                });
                seen.add(p.name.toLowerCase());
            }
        });
        
        // 2. Get category suggestions
        const categories = [...new Set(state.products.map(p => p.category_name))].filter(Boolean);
        categories.forEach(cat => {
            const score = fuzzyMatch(cat, query);
            if (score > 0 && !seen.has(cat.toLowerCase())) {
                const count = state.products.filter(p => p.category_name === cat).length;
                suggestions.push({
                    type: 'category',
                    text: cat,
                    count: count,
                    score: score + 0.5 // Slight boost for categories
                });
                seen.add(cat.toLowerCase());
            }
        });
        
        // 3. Add search history
        state.searchHistory.forEach(term => {
            const score = fuzzyMatch(term, query);
            if (score > 0 && !seen.has(term.toLowerCase())) {
                suggestions.push({
                    type: 'history',
                    text: term,
                    score: score - 0.5 // Lower priority for history
                });
                seen.add(term.toLowerCase());
            }
        });
        
        // Sort by score and limit
        return suggestions
            .sort((a, b) => b.score - a.score)
            .slice(0, 8);
    }

    function renderSearchSuggestions(suggestions) {
        const container = document.getElementById('searchSuggestions');
        if (!container) return;
        
        if (suggestions.length === 0) {
            container.classList.add('hidden');
            return;
        }
        
        container.innerHTML = '';
        container.classList.remove('hidden');
        
        suggestions.forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.className = `px-4 py-3 hover:bg-slate-50 cursor-pointer transition-colors border-b border-slate-100 last:border-0 ${
                index === state.selectedSuggestionIndex ? 'bg-slate-50' : ''
            }`;
            
            let icon = '🔍';
            let subtitle = '';
            
            if (suggestion.type === 'product') {
                icon = '📦';
                subtitle = suggestion.category || '';
            } else if (suggestion.type === 'category') {
                icon = getCategoryIcon(suggestion.text);
                subtitle = `${suggestion.count} sản phẩm`;
            } else if (suggestion.type === 'history') {
                icon = '🕐';
                subtitle = 'Tìm kiếm gần đây';
            }
            
            item.innerHTML = `
                <div class="flex items-center gap-3">
                    <span class="text-lg">${icon}</span>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-slate-800 text-sm truncate">${highlightMatch(suggestion.text, state.searchQuery)}</div>
                        ${subtitle ? `<div class="text-xs text-slate-500 truncate">${subtitle}</div>` : ''}
                    </div>
                    ${suggestion.type === 'category' ? '<i class="fas fa-arrow-right text-slate-400 text-xs"></i>' : ''}
                </div>
            `;
            
            item.addEventListener('click', () => {
                selectSuggestion(suggestion);
            });
            
            container.appendChild(item);
        });
    }

    function highlightMatch(text, query) {
        if (!query) return text;
        
        const index = text.toLowerCase().indexOf(query.toLowerCase());
        if (index === -1) return text;
        
        const before = text.substring(0, index);
        const match = text.substring(index, index + query.length);
        const after = text.substring(index + query.length);
        
        return `${before}<mark class="bg-yellow-100 text-slate-900 font-bold px-0.5 rounded">${match}</mark>${after}`;
    }

    function selectSuggestion(suggestion) {
        const searchInput = document.getElementById('searchInput');
        const suggestionsContainer = document.getElementById('searchSuggestions');
        
        if (suggestion.type === 'category') {
            // Select category
            state.selectedCategory = suggestion.text;
            state.searchQuery = '';
            if (searchInput) searchInput.value = '';
            renderCategories(state.products);
            renderProducts(state.products);
            
        } else if (suggestion.type === 'product') {
            // Select product
            selectProduct(suggestion.data);
            state.searchQuery = '';
            if (searchInput) searchInput.value = '';
            
        } else {
            // Use search term
            state.searchQuery = suggestion.text;
            if (searchInput) searchInput.value = suggestion.text;
            renderProducts(state.products);
        }
        
        // Add to history
        addToSearchHistory(suggestion.text);
        
        // Hide suggestions
        if (suggestionsContainer) {
            suggestionsContainer.classList.add('hidden');
        }
        
        state.selectedSuggestionIndex = -1;
    }

    function addToSearchHistory(term) {
        if (!term || term.length < 2) return;
        
        // Remove if exists
        state.searchHistory = state.searchHistory.filter(t => t.toLowerCase() !== term.toLowerCase());
        
        // Add to beginning
        state.searchHistory.unshift(term);
        
        // Keep only last 10
        state.searchHistory = state.searchHistory.slice(0, 10);
        
        // Save to localStorage
        localStorage.setItem('searchHistory', JSON.stringify(state.searchHistory));
    }

    function clearSearchSuggestions() {
        const container = document.getElementById('searchSuggestions');
        if (container) {
            container.classList.add('hidden');
        }
        state.selectedSuggestionIndex = -1;
    }

    // --- GLOBAL FUNCTIONS FOR TEMPLATE ---
    window.filterOutOfStockProducts = function(shouldHide) {
        state.hideOutOfStock = shouldHide;
        state.currentPage = 1;
        renderProducts(state.products);
    };
    
    window.handleSearch = function(query) {
        state.searchQuery = query;
        state.currentPage = 1;
        renderProducts(state.products);
    };

    window.syncQuantity = function(source) {
        if (source === 'desktop') {
            const desktopValue = els.amountInput?.value || 1;
            const mobileAmountInput = document.getElementById('mobileAmount');
            if (mobileAmountInput) mobileAmountInput.value = desktopValue;
            updateMobileTotal();
        } else if (source === 'mobile') {
            const mobileAmountInput = document.getElementById('mobileAmount');
            const mobileValue = mobileAmountInput?.value || 1;
            if (els.amountInput) els.amountInput.value = mobileValue;
            updateTotal();
        }
    };

    // --- INIT ---
    loadProfile();
    loadProducts();
    
    // Event listeners
    if (els.amountInput) {
        els.amountInput.addEventListener('input', () => {
            updateTotal();
            syncQuantity('desktop');
        });
    }
    
    const mobileAmountInput = document.getElementById('mobileAmount');
    if (mobileAmountInput) {
        mobileAmountInput.addEventListener('input', () => {
            updateMobileTotal();
            syncQuantity('mobile');
        });
    }

    // Search input listener with smart suggestions
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const suggestionsContainer = document.getElementById('searchSuggestions');
    
    if (searchInput) {
        // Input event - show suggestions
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value;
            
            // Show/hide clear button
            if (clearSearchBtn) {
                if (query) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
            }
            
            // Get and render suggestions
            if (query.length > 0) {
                const suggestions = getSearchSuggestions(query);
                renderSearchSuggestions(suggestions);
            } else {
                clearSearchSuggestions();
            }
            
            // Apply search filter
            handleSearch(query);
        });
        
        // Focus event - show suggestions if has value
        searchInput.addEventListener('focus', (e) => {
            if (e.target.value.length > 0) {
                const suggestions = getSearchSuggestions(e.target.value);
                renderSearchSuggestions(suggestions);
            }
        });
        
        // Keyboard navigation
        searchInput.addEventListener('keydown', (e) => {
            if (!suggestionsContainer || suggestionsContainer.classList.contains('hidden')) return;
            
            const suggestions = suggestionsContainer.querySelectorAll('div[class*="cursor-pointer"]');
            if (suggestions.length === 0) return;
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                state.selectedSuggestionIndex = Math.min(state.selectedSuggestionIndex + 1, suggestions.length - 1);
                updateSuggestionSelection(suggestions);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                state.selectedSuggestionIndex = Math.max(state.selectedSuggestionIndex - 1, -1);
                updateSuggestionSelection(suggestions);
            } else if (e.key === 'Enter' && state.selectedSuggestionIndex >= 0) {
                e.preventDefault();
                suggestions[state.selectedSuggestionIndex].click();
            } else if (e.key === 'Escape') {
                clearSearchSuggestions();
            }
        });
    }
    
    // Clear search button
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            clearSearchBtn.classList.add('hidden');
            state.searchQuery = '';
            clearSearchSuggestions();
            renderProducts(state.products);
        });
    }
    
    // Update suggestion selection highlight
    function updateSuggestionSelection(suggestions) {
        suggestions.forEach((item, index) => {
            if (index === state.selectedSuggestionIndex) {
                item.classList.add('bg-slate-50');
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.classList.remove('bg-slate-50');
            }
        });
    }
    
    // Click outside to close suggestions
    document.addEventListener('click', (e) => {
        if (searchInput && !searchInput.contains(e.target) && suggestionsContainer && !suggestionsContainer.contains(e.target)) {
            clearSearchSuggestions();
        }
    });

    // Mobile buy button
    const mobileBtnBuy = document.getElementById('mobileBtnBuy');
    if (mobileBtnBuy && els.btnBuy) {
        mobileBtnBuy.addEventListener('click', () => {
            // Sync quantities first
            const mobileAmount = document.getElementById('mobileAmount');
            if (mobileAmount && els.amountInput) {
                els.amountInput.value = mobileAmount.value;
            }
            // Trigger desktop buy
            els.btnBuy.click();
        });
    }

    // Mobile reset button
    const mobileBtnReset = document.getElementById('mobileBtnReset');
    const btnReset = document.getElementById('btnReset');
    if (mobileBtnReset && btnReset) {
        mobileBtnReset.addEventListener('click', () => btnReset.click());
    }

    // Desktop reset button
    if (btnReset) {
        btnReset.addEventListener('click', () => {
            state.selectedProduct = null;
            if (els.productNameInput) els.productNameInput.value = '';
            if (els.productIdInput) els.productIdInput.value = '';
            if (els.amountInput) els.amountInput.value = 1;
            const mobileAmountInput = document.getElementById('mobileAmount');
            if (mobileAmountInput) mobileAmountInput.value = 1;
            
            const selectedProductDisplay = document.getElementById('selectedProductDisplay');
            if (selectedProductDisplay) {
                selectedProductDisplay.innerHTML = `
                    <div class="text-center text-slate-400">
                        <i class="fas fa-box-open text-2xl mb-2"></i>
                        <p class="text-sm">Chưa chọn sản phẩm</p>
                    </div>
                `;
            }
            
            const mobileSelectedProduct = document.getElementById('mobileSelectedProduct');
            if (mobileSelectedProduct) {
                mobileSelectedProduct.innerHTML = '';
            }
            
            renderProducts(state.products);
            updateTotal();
            updateMobileTotal();
        });
    }

    // Pagination event listeners
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    
    if (prevPageBtn) {
        prevPageBtn.addEventListener('click', () => goToPage('prev'));
    }
    
    if (nextPageBtn) {
        nextPageBtn.addEventListener('click', () => goToPage('next'));
    }

    // Handle window resize for responsive card layout
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (state.products.length > 0) {
                renderProducts(state.products);
            }
        }, 250);
    });
});