document.addEventListener('DOMContentLoaded', () => {
    // --- CONFIG & STATE ---
    const CACHE_KEYS = {
        PRODUCTS: 'shop_products_v1',
        PROFILE: 'shop_profile_v1',
        SEARCH_HISTORY: 'searchHistory'
    };

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
        searchHistory: JSON.parse(localStorage.getItem(CACHE_KEYS.SEARCH_HISTORY) || '[]'),
        selectedSuggestionIndex: -1
    };

    // --- DOM ELEMENTS ---
    const els = {
        desktopBalance: document.getElementById('desktopUserBalance'),
        mobileBalance: document.getElementById('mobileUserBalance'),
        username: document.getElementById('userName'),
        productList: document.getElementById('productList'),
        categoryList: document.getElementById('categoryList'),
        mobileCategoryList: document.getElementById('mobileCategoryList'),
        buyForm: document.getElementById('buyForm'),
        productNameInput: document.getElementById('selectedProductName'),
        productIdInput: document.getElementById('selectedProductId'),
        amountInput: document.getElementById('amount'),
        mobileAmountInput: document.getElementById('mobileAmount'), // Cache mobile input
        totalPriceDisplay: document.getElementById('totalPrice'),
        resultArea: document.getElementById('resultArea'),
        btnBuy: document.getElementById('btnBuy'),
        // ELEMENT MỚI: Số lượng item trên header giỏ hàng
        cartItemCount: document.getElementById('cartItemCount'),
        // MODAL ELEMENTS
        successModal: document.getElementById('successModal'),
        successModalBackdrop: document.getElementById('successModalBackdrop'),
        successModalContent: document.getElementById('successModalContent'),
        closeSuccessModalBtn: document.getElementById('closeSuccessModalBtn'),
        purchasedData: document.getElementById('purchasedData'),
        copyDataBtn: document.getElementById('copyDataBtn'),
        
        // STATS ELEMENTS
        availableProducts: document.getElementById('availableProducts'),
        categoryCount: document.getElementById('categoryCount'),
        mobileCategoryCount: document.getElementById('mobileCategoryCount'),
    };

    // --- HELPER FUNCTIONS ---
    const money = (amount) => {
        const num = parseFloat(amount) || 0;
        return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
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
        if (name.includes('facebook') || name.includes('fb')) return '<img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" class="w-6 h-6">';
        if (name.includes('instagram') || name.includes('ig')) return '<img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" class="w-6 h-6">';
        if (name.includes('twitter')) return '<img src="https://cdn.wikiwiki.jp/to/w/sudomemoflip/MenuBar/::attach/x_logo.png?rev=d33f848a50c3345d4d084fb7d4838da3&t=20230730105059" class="w-6 h-6">';
        if (name.includes('tiktok')) return '<img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" class="w-6 h-6">';
        if (name.includes('youtube') || name.includes('yt')) return '<img src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png" class="w-6 h-6">';
        if (name.includes('linkedin')) return '<img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" class="w-6 h-6">';
        if (name.includes('netflix')) return '<img src="https://th.bing.com/th/id/R.0a7962746bf20998069f3b4b91f83050?rik=HhlNpiOoGHnrHw&pid=ImgRaw&r=0" class="w-6 h-6">';
        if (name.includes('outlook')) return '<img src="https://tse4.mm.bing.net/th/id/OIP.sbnvFrmYd5MJRky0_VqTegHaG4?rs=1&pid=ImgDetMain&o=7&rm=3" class="w-6 h-6">';
        
        // Services & Tools
        if (name.includes('gmail') || name.includes('email') || name.includes('mail')) return '<img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" class="w-6 h-6">';
        if (name.includes('google') || name.includes('gg')) return '<img src="https://cdn-icons-png.flaticon.com/512/300/300221.png" class="w-6 h-6">';
        if (name.includes('pia s5 proxy')) return '<img src="https://proxy-zone.net/wp-content/uploads/2023/08/PIA-S5-Proxy-Logo-2048x1962.png" class="w-6 h-6">';
        if (name.includes('express vpn')) return '<img src="https://diebestenvpn.at/wp-content/uploads/2017/06/expressvpn-logo-600x600_preview-1024x1024.jpg" class="w-6 h-6">';
        if (name.includes('nord vpn')) return '<img src="https://cdn.joinhoney.com/images/lp/store-logos/nord-vpn-logo.png" class="w-6 h-6">';
        if (name.includes('surfshark')) return '<img src="https://assets.findstack.com/5ztz4p92626ewg5l7f6ui5kp15qb" class="w-6 h-6">';
        if (name.includes('proton')) return '<img src="https://tse1.mm.bing.net/th/id/OIP.NS6gVUQIYeL4MIU33vhgFgHaHa?rs=1&pid=ImgDetMain&o=7&rm=3" class="w-6 h-6">';
        if (name.includes('luna')) return '<img src="https://tse3.mm.bing.net/th/id/OIP.fWkSGEva9mCmfxrX68N4QwAAAA?rs=1&pid=ImgDetMain&o=7&rm=3" class="w-6 h-6">';
        if (name.includes('kling')) return '<img src="https://registry.npmmirror.com/@lobehub/icons-static-png/latest/files/light/kling-color.png" class="w-6 h-6">';
        
        if (name.includes('hma vpn')) return '<img src="https://www.vpngids.nl/wp-content/uploads/hma-vpn-logo-png-fallback.png" class="w-6 h-6">';
        if (name.includes('capcut pro')) return '<img src="https://www.pngall.com/wp-content/uploads/13/Capcut-Transparent.png" class="w-6 h-6">';
        if (name.includes('cavana pro')) return '<img src="https://uxwing.com/wp-content/themes/uxwing/download/brands-and-social-media/canva-icon.png" class="w-6 h-6">';
        if (name.includes('chat gpt')) return '<img src="https://s3-alpha.figma.com/hub/file/2732115288/c4c05388-d833-45ac-913b-c914cf08187a-cover.png" class="w-6 h-6">';
        if (name.includes('veo 3')) return '<img src="https://veo3api.com/logo.png" class="w-6 h-6">';
        if (name.includes('gemini pro')) return '<img src="https://uxwing.com/wp-content/themes/uxwing/download/brands-and-social-media/google-gemini-icon.png" class="w-6 h-6">';
        if (name.includes('mst us')) return '<img src="https://luatnhandan.vn/wp-content/uploads/2019/12/ho-kinh-doanh-gia-dinh-co-can-dang-ky-ma-so-thue-khong-e1575560020944.jpg" class="w-6 h-6">';
        if (name.includes('tool auto')) return '<img src="https://static.vecteezy.com/system/resources/previews/013/658/544/original/automation-clip-art-icon-vector.jpg" class="w-6 h-6">';
        if (name.includes('tool get')) return '<img src="https://static.vecteezy.com/system/resources/previews/013/658/544/original/automation-clip-art-icon-vector.jpg" class="w-6 h-6">';
        if (name.includes('adobe')) return '<img src="https://www.adobe.com/content/dam/dx-dc/us/en/acrobat/acrobat_prodc_appicon_noshadow_1024.png.img.png" class="w-6 h-6">';
        
        // Default
        return '📦';
    };

    // --- API CALLS ---
    // UI Update Function for Profile
    const updateProfileUI = (data) => {
        if (!data) return;
        const formattedBalance = money(data.balance);
        if(els.desktopBalance) els.desktopBalance.innerText = formattedBalance;
        if(els.mobileBalance) els.mobileBalance.innerText = formattedBalance;
        if(els.username) els.username.innerText = data.username || 'User';
    };

    // 1. Optimized Load Profile (Cache First)
    async function loadProfile() {
        // Step 1: Check cache & Render immediately
        const cachedProfile = localStorage.getItem(CACHE_KEYS.PROFILE);
        if (cachedProfile) {
            try {
                updateProfileUI(JSON.parse(cachedProfile));
            } catch(e) { console.error("Cache parse error", e); }
        }

        // Step 2: Fetch fresh data in background
        try {
            const res = await fetch('/tool/profile');
            const result = await res.json();
            
            if (result.status === 'success' && result.data) {
                // Update UI with fresh data
                updateProfileUI(result.data);
                // Update Cache
                localStorage.setItem(CACHE_KEYS.PROFILE, JSON.stringify(result.data));
            }
        } catch (e) {
            console.error('Lỗi tải thông tin user:', e);
        }
    }

    // 2. Optimized Load Products (Cache First + Parallel)
    async function loadProducts() {
        let hasRenderedFromCache = false;

        // Step 1: Check cache & Render immediately
        const cachedProducts = localStorage.getItem(CACHE_KEYS.PRODUCTS);
        if (cachedProducts) {
            try {
                const data = JSON.parse(cachedProducts);
                if (Array.isArray(data) && data.length > 0) {
                    state.products = data;
                    renderCategories(state.products);
                    renderProducts(state.products);
                    hasRenderedFromCache = true;
                }
            } catch(e) { console.error("Product cache parse error", e); }
        }

        // Only show loading spinner if NO cache was available
        if (!hasRenderedFromCache && els.productList) {
            els.productList.innerHTML = `
                <div class="text-center py-20 text-slate-400">
                    <i class="fa-solid fa-spinner fa-spin text-3xl mb-3"></i>
                    <p>Đang tải dữ liệu...</p>
                </div>`;
        }
        
        // Step 2: Fetch fresh data
        try {
            const res = await fetch('/tool/products');
            const result = await res.json();
            
            if (result.success && Array.isArray(result.data)) {
                // Check if data actually changed to avoid unnecessary re-render could be added here, 
                // but for now, we just refresh to ensure stock is accurate.
                state.products = result.data;
                
                // Render with fresh data
                renderCategories(state.products);
                renderProducts(state.products);
                
                // Update Cache
                localStorage.setItem(CACHE_KEYS.PRODUCTS, JSON.stringify(result.data));
            } else if (!hasRenderedFromCache) {
                if (els.productList) els.productList.innerHTML = '<div class="text-center text-red-500 py-10">Không tải được danh sách sản phẩm.</div>';
            }
        } catch (e) {
            console.error(e);
            if (!hasRenderedFromCache && els.productList) {
                els.productList.innerHTML = '<div class="text-center text-red-500 py-10">Lỗi kết nối máy chủ.</div>';
            }
        }
    }

    function renderCategories(products) {
        // Prepare Data
        const categories = [...new Set(products.map(p => p.category_name))].filter(Boolean);
        
        // Render Desktop
        if (els.categoryList) renderDesktopCategories(categories, products);
        
        // Render Mobile Horizontal
        if (els.mobileCategoryList) renderMobileCategories(categories, products);

        // Render Mobile Dropdown
        const mobileDrawerList = document.getElementById('mobileCategoryDropdownList');
        if (mobileDrawerList) renderMobileDrawerCategories(categories, products, mobileDrawerList);
    }

    function renderDesktopCategories(categories, products) {
        const fragment = document.createDocumentFragment();

        // Helper
        const createItem = (name, isAll = false) => {
            const li = document.createElement('li');
            const isActive = isAll ? (state.selectedCategory === null) : (state.selectedCategory === name);
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
                state.currentPage = 1;
                renderCategories(state.products);
                renderProducts(state.products);
            });
            return li;
        };

        fragment.appendChild(createItem('Tất cả sản phẩm', true));
        categories.forEach(cat => fragment.appendChild(createItem(cat)));
        
        els.categoryList.innerHTML = '';
        els.categoryList.appendChild(fragment);
    }

    function renderMobileCategories(categories, products) {
        const fragment = document.createDocumentFragment();

        const createPill = (name, isAll = false) => {
            const pill = document.createElement('button');
            const isActive = isAll ? (state.selectedCategory === null) : (state.selectedCategory === name);
            const count = isAll ? products.length : products.filter(p => p.category_name === name).length;
            const icon = isAll ? '🏪' : getCategoryIcon(name);
            
            pill.className = `category-pill px-4 py-2 rounded-full text-xs font-semibold border transition-all flex-shrink-0 flex items-center gap-1.5 ${
                isActive 
                    ? 'bg-gradient-to-r from-primary to-red-600 text-white border-primary shadow-lg shadow-red-500/30' 
                    : 'bg-white text-slate-600 border-slate-200 hover:border-primary hover:text-primary'
            }`;
            
            pill.innerHTML = `<span class="text-sm">${icon}</span><span>${name}</span><span class="ml-0.5 ${isActive ? 'text-white/80' : 'text-slate-400'}">(${count})</span>`;
            
            pill.addEventListener('click', () => {
                state.selectedCategory = isAll ? null : name;
                state.currentPage = 1;
                renderCategories(state.products);
                renderProducts(state.products);
                if (window.innerWidth < 1024 && els.productList) {
                    els.productList.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
            return pill;
        };

        fragment.appendChild(createPill('Tất cả', true));
        categories.forEach(cat => fragment.appendChild(createPill(cat)));

        els.mobileCategoryList.innerHTML = '';
        els.mobileCategoryList.appendChild(fragment);
    }

    function renderMobileDrawerCategories(categories, products, container) {
        const fragment = document.createDocumentFragment();
        
        const createItem = (name, isAll = false) => {
            const div = document.createElement('div');
            const isActive = isAll ? (state.selectedCategory === null) : (state.selectedCategory === name);
            const count = isAll ? products.length : products.filter(p => p.category_name === name).length;
            
            div.className = `flex items-center justify-between py-2.5 px-3 pl-11 rounded-lg cursor-pointer transition-colors ${
                isActive ? 'bg-red-50 text-primary font-bold' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50'
            }`;
            
            div.innerHTML = `<span class="text-sm truncate">${name}</span><span class="text-[10px] bg-white border border-slate-200 px-2 py-0.5 rounded-full text-slate-400">${count}</span>`;
            
            div.addEventListener('click', () => {
                state.selectedCategory = isAll ? null : name;
                state.currentPage = 1;
                renderCategories(state.products);
                renderProducts(state.products);
                const closeMenuBtn = document.getElementById('closeMobileMenuBtn');
                if (closeMenuBtn) closeMenuBtn.click();
            });
            return div;
        };
        
        fragment.appendChild(createItem('Tất cả', true));
        categories.forEach(cat => fragment.appendChild(createItem(cat)));
        
        container.innerHTML = '';
        container.appendChild(fragment);
    }

    // --- FILTER & RENDER PRODUCTS (Optimized) ---
    function getFilteredProducts(products) {
        let filtered = products;
        
        if (state.selectedCategory) {
            filtered = filtered.filter(p => p.category_name === state.selectedCategory);
        }
        if (state.hideOutOfStock) {
            filtered = filtered.filter(p => parseInt(p.amount) > 0);
        }
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
        
        const filtered = getFilteredProducts(products);
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

        // Pagination
        state.totalPages = Math.ceil(filtered.length / state.itemsPerPage);
        if (state.currentPage > state.totalPages) state.currentPage = 1;

        const startIndex = (state.currentPage - 1) * state.itemsPerPage;
        const endIndex = startIndex + state.itemsPerPage;
        const paginatedProducts = filtered.slice(startIndex, endIndex);

        if (filtered.length > state.itemsPerPage) {
            showPagination(filtered.length, startIndex, Math.min(endIndex, filtered.length));
        } else {
            hidePagination();
        }

        // Render using DocumentFragment
        const fragment = document.createDocumentFragment();
        const isMobile = window.innerWidth < 640;

        paginatedProducts.forEach(p => {
            const card = document.createElement('div');
            const isSoldOut = parseInt(p.amount) < 1;
            const isSelected = state.selectedProduct?.id === p.id;
            
            card.className = isMobile 
                ? `product-card bg-white p-4 rounded-xl border transition-all duration-300 relative flex gap-4 ${isSelected ? 'border-red-500 ring-2 ring-red-200 shadow-lg bg-red-50/50' : 'border-slate-200'} ${isSoldOut ? 'opacity-60 grayscale cursor-not-allowed' : 'cursor-pointer hover:shadow-lg'}`
                : `product-card bg-white p-4 rounded-xl border transition-all duration-300 relative flex flex-col group ${isSelected ? 'border-red-500 ring-2 ring-red-200 shadow-lg bg-red-50/50' : 'border-slate-200'} ${isSoldOut ? 'opacity-60 grayscale cursor-not-allowed' : 'cursor-pointer hover:shadow-lg'}`;
            
            const flagHtml = p.country ? `<img src="${getFlagUrl(p.country)}" class="w-5 h-auto rounded-sm shadow-sm" alt="${p.country}">` : '';
            const stockBadge = !isSoldOut ? `<div class="stock-badge"><span class="text-[10px] font-bold px-2 py-1 rounded-full ${parseInt(p.amount) > 10 ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200'}">${p.amount} có sẵn</span></div>` : '';

            if (isMobile) {
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
                            <button class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${isSelected ? 'bg-gradient-to-r from-primary to-red-600 text-white shadow-lg' : 'bg-slate-100 text-slate-600'}">
                                ${isSelected ? '✓' : 'Chọn'}
                            </button>
                        </div>
                    </div>
                    ${p.icon ? `<img src="${p.icon}" class="w-12 h-12 object-contain opacity-70 flex-shrink-0">` : ''}
                `;
            } else {
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
                        <button class="px-4 py-2 rounded-lg text-xs font-bold transition-all transform ${isSelected ? 'bg-gradient-to-r from-primary to-red-600 text-white shadow-lg shadow-red-500/40 scale-105' : 'bg-slate-100 text-slate-600 group-hover:bg-primary group-hover:text-white'}">
                            ${isSelected ? '<i class="fa-solid fa-check"></i> Đã chọn' : '<i class="fa-solid fa-cart-plus"></i> Chọn'}
                        </button>
                    </div>
                `;
            }

            if (!isSoldOut) {
                card.addEventListener('click', () => selectProduct(p));
            }
            fragment.appendChild(card);
        });

        els.productList.innerHTML = '';
        els.productList.appendChild(fragment);
    }

    // --- UPDATE STATS ---
    function updateStats(allProducts, filteredProducts) {
        if (els.availableProducts) {
            const count = allProducts.filter(p => parseInt(p.amount) > 0).length;
            els.availableProducts.textContent = count;
        }
        
        const uniqueCategories = [...new Set(allProducts.map(p => p.category_name))].filter(Boolean);
        if (els.categoryCount) els.categoryCount.textContent = uniqueCategories.length;
        if (els.mobileCategoryCount) els.mobileCategoryCount.textContent = `${uniqueCategories.length} danh mục`;
    }

    // --- PAGINATION FUNCTIONS ---
    function showPagination(total, start, end) {
        const container = document.getElementById('paginationContainer');
        if (!container) return;
        
        container.classList.remove('hidden');
        document.getElementById('totalProducts').textContent = total;
        document.getElementById('pageStart').textContent = start + 1;
        document.getElementById('pageEnd').textContent = end;
        
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (prevBtn) {
            prevBtn.disabled = state.currentPage === 1;
            prevBtn.onclick = () => { if(state.currentPage > 1) { state.currentPage--; renderProducts(state.products); els.productList.scrollTop = 0; }};
        }
        if (nextBtn) {
            nextBtn.disabled = state.currentPage === state.totalPages;
            nextBtn.onclick = () => { if(state.currentPage < state.totalPages) { state.currentPage++; renderProducts(state.products); els.productList.scrollTop = 0; }};
        }
        
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
        let startPage = Math.max(1, state.currentPage - 2);
        let endPage = Math.min(state.totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.className = `w-10 h-10 rounded-lg text-sm font-semibold transition ${i === state.currentPage ? 'bg-primary text-white shadow' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'}`;
            pageBtn.onclick = () => { state.currentPage = i; renderProducts(state.products); els.productList.scrollTop = 0; };
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
        
        if (els.productNameInput) els.productNameInput.value = product.name;
        if (els.productIdInput) els.productIdInput.value = product.id;
        
        // Reset inputs
        if (els.amountInput) els.amountInput.value = 1;
        if (els.mobileAmountInput) els.mobileAmountInput.value = 1;
        
        // Update Displays
        const selectedProductDisplay = document.getElementById('selectedProductDisplay');
        if (selectedProductDisplay) {
            selectedProductDisplay.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center"><i class="fas fa-box text-primary"></i></div>
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-800 text-sm">${product.name}</h4>
                        <p class="text-xs text-slate-500">${product.category_name || 'Sản phẩm'}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-base font-bold text-primary">${money(product.price)}</div>
                        <div class="text-[10px] text-green-600 font-semibold">${product.amount} có sẵn</div>
                    </div>
                </div>`;
        }
        
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
                </div>`;
        }
        
        const selectedProductPrice = document.getElementById('selectedProductPrice');
        if (selectedProductPrice) selectedProductPrice.value = product.price;
        
        renderProducts(state.products); // To show selected state
        updateTotal();
        updateMobileTotal();

        if (window.innerWidth < 1024 && typeof window.openMobileSheet === 'function') {
            window.openMobileSheet();
        }
    }

    function updateTotal() {
        if (!state.selectedProduct) {
            if (els.totalPriceDisplay) els.totalPriceDisplay.innerText = '0đ';
            document.getElementById('unitPrice').textContent = '0đ';
            document.getElementById('displayQty').textContent = '0';
            
            if (els.cartItemCount) els.cartItemCount.innerText = '0';
            return;
        }
        const amount = parseInt(els.amountInput?.value) || 0;
        const total = amount * state.selectedProduct.price;
        if (els.totalPriceDisplay) els.totalPriceDisplay.innerText = money(total);
        document.getElementById('unitPrice').textContent = money(state.selectedProduct.price);
        document.getElementById('displayQty').textContent = amount;
        if (els.cartItemCount) els.cartItemCount.innerText = '0';

        if (els.btnBuy) {
            els.btnBuy.disabled = amount <= 0 || amount > state.selectedProduct.amount;
            if(amount > state.selectedProduct.amount) {
                showToast(`Kho chỉ còn ${state.selectedProduct.amount} sản phẩm!`, "warning");
                els.amountInput.value = state.selectedProduct.amount;
                updateTotal();
            }
        }
    }

    function updateMobileTotal() {
        if (!state.selectedProduct) return;
        const amount = parseInt(els.mobileAmountInput?.value) || 0;
        const total = amount * state.selectedProduct.price;
        document.getElementById('mobileTotalPrice').textContent = money(total);
        const mobileBtnBuy = document.getElementById('mobileBtnBuy');
        if (mobileBtnBuy) mobileBtnBuy.disabled = amount <= 0 || amount > state.selectedProduct.amount;
    }

    // Buy Event
    if (els.btnBuy) {
        els.btnBuy.addEventListener('click', async () => {
            if (!state.selectedProduct) {
                showToast("Vui lòng chọn sản phẩm trước", "warning");
                return;
            }
            const amount = els.amountInput.value;
            const confirmMsg = `
                <div class="space-y-4">
                    <div class="bg-slate-50 p-4 rounded-xl space-y-2">
                        <div class="flex justify-between"><span class="text-slate-500">Sản phẩm:</span> <span class="font-bold text-slate-800">${state.selectedProduct.name}</span></div>
                        <div class="flex justify-between"><span class="text-slate-500">Số lượng:</span> <span class="font-bold text-slate-800">${amount}</span></div>
                        <div class="flex justify-between pt-2 border-t"><span class="text-slate-500">Tổng tiền:</span> <span class="font-bold text-red-600 text-lg">${els.totalPriceDisplay.innerText}</span></div>
                    </div>
                    <p class="text-xs text-slate-500 text-center">Đơn hàng sẽ được xử lý ngay lập tức.</p>
                </div>`;

            showConfirmToast(confirmMsg, async () => {
                els.btnBuy.disabled = true;
                const originalBtnText = els.btnBuy.innerHTML;
                els.btnBuy.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const res = await fetch('/tool/buy', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
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
                        // Reload data in background to update stock and balance
                        loadProfile();
                        loadProducts(); 
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
        });
    }

    // --- MODAL FUNCTIONS (MỚI) ---
    function showSuccess(result) {
        if (!els.successModal || !els.purchasedData) {
            // Fallback nếu không có modal
            console.error('Modal elements not found');
            return;
        }

        const apiData = result.data || {};
        const dataContent = Array.isArray(apiData) ? apiData : (apiData.data || apiData);
        const accounts = Array.isArray(dataContent) ? dataContent.join('\n') : JSON.stringify(dataContent);
        
        // 1. Set nội dung
        els.purchasedData.value = accounts;
        
        // 2. Hiển thị modal
        els.successModal.classList.remove('hidden');
        
        // 3. Animation Open
        // Timeout nhỏ để đảm bảo class 'hidden' đã được remove trước khi thêm transition classes
        setTimeout(() => {
            if(els.successModalBackdrop) els.successModalBackdrop.classList.remove('opacity-0');
            if(els.successModalContent) {
                els.successModalContent.classList.remove('opacity-0', 'scale-95');
                els.successModalContent.classList.add('opacity-100', 'scale-100');
            }
        }, 10);

        // 4. Close mobile sheet if open
        const closeSheetFn = window.closeMobileSheet;
        if (typeof closeSheetFn === 'function') {
            closeSheetFn();
        }
    }

    function closeModal() {
        if (!els.successModal) return;

        // 1. Animation Close
        if(els.successModalBackdrop) els.successModalBackdrop.classList.add('opacity-0');
        if(els.successModalContent) {
            els.successModalContent.classList.remove('opacity-100', 'scale-100');
            els.successModalContent.classList.add('opacity-0', 'scale-95');
        }
        
        // 2. Hide modal after animation finishes
        setTimeout(() => {
            els.successModal.classList.add('hidden');
        }, 300); // Matches transition duration
    }

    // Event listeners for Modal
    if(els.closeSuccessModalBtn) {
        els.closeSuccessModalBtn.addEventListener('click', closeModal);
    }
    
    // Close when clicking backdrop
    if(els.successModalBackdrop) {
        els.successModalBackdrop.addEventListener('click', closeModal);
    }

    // Copy button in modal
    if (els.copyDataBtn && els.purchasedData) {
        els.copyDataBtn.addEventListener('click', () => {
            els.purchasedData.select();
            navigator.clipboard.writeText(els.purchasedData.value).then(() => {
                const originalText = els.copyDataBtn.innerHTML;
                els.copyDataBtn.innerHTML = '<i class="fa-solid fa-check text-emerald-500"></i> Đã sao chép';
                els.copyDataBtn.classList.add('bg-emerald-50', 'text-emerald-700', 'ring-emerald-200');
                
                setTimeout(() => {
                    els.copyDataBtn.innerHTML = originalText;
                    els.copyDataBtn.classList.remove('bg-emerald-50', 'text-emerald-700', 'ring-emerald-200');
                }, 2000);
            });
        });
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
            if (els.mobileAmountInput && els.amountInput) els.mobileAmountInput.value = els.amountInput.value;
            updateMobileTotal();
        } else if (source === 'mobile') {
            if (els.amountInput && els.mobileAmountInput) els.amountInput.value = els.mobileAmountInput.value;
            updateTotal();
        }
    };

    // --- INIT ---
    Promise.all([loadProfile(), loadProducts()]).catch(console.error);
    
    // UI Event Listeners
    if (els.amountInput) els.amountInput.addEventListener('input', () => { updateTotal(); syncQuantity('desktop'); });
    if (els.mobileAmountInput) els.mobileAmountInput.addEventListener('input', () => { updateMobileTotal(); syncQuantity('mobile'); });
    

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
            renderProducts(state.products);
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

    // --- MOBILE DRAWER DROPDOWN LOGIC ---
    const mobileDropdownBtn = document.getElementById('mobileCategoryDropdownBtn');
    const mobileDropdownList = document.getElementById('mobileCategoryDropdownList');
    const mobileChevron = document.getElementById('mobileCategoryChevron');

    if (mobileDropdownBtn && mobileDropdownList) {
        mobileDropdownBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isHidden = mobileDropdownList.classList.contains('hidden');
            
            if (isHidden) {
                mobileDropdownList.classList.remove('hidden');
                mobileChevron.classList.remove('fa-chevron-right');
                mobileChevron.classList.add('fa-chevron-down', 'text-primary');
            } else {
                mobileDropdownList.classList.add('hidden');
                mobileChevron.classList.add('fa-chevron-right');
                mobileChevron.classList.remove('fa-chevron-down', 'text-primary');
            }
        });
    }
});

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-float');

    const toast = document.createElement('div');
    toast.classList.add('toast', type);
    toast.innerText = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
function showConfirmToast(message, onConfirm, options = {}) {
    const container = document.getElementById('toast-modal');
    const overlay = document.getElementById('toast-overlay');
    
    // Ẩn overlay cũ nếu đang hiển thị
    overlay.classList.remove('hidden');
    
    // Default options
    const { 
        title = 'Xác nhận đơn hàng', 
        cancelText = 'Quay lại', 
        confirmText = 'Đặt hàng ngay',
        showCloseBtn = true,
        onCancel = null
    } = options;

    // Tạo nội dung modal
    const modalContent = `
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[440px] mx-4 animate-scaleIn overflow-hidden border border-slate-200">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-bold text-slate-800">${title}</h3>
                ${showCloseBtn ? `
                    <button type="button" class="close-btn text-slate-400 hover:text-slate-700 transition-colors p-1 rounded-full hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                ` : ''}
            </div>
            
            <!-- Body - Message Content -->
            <div class="p-6 message-content max-h-[60vh] overflow-y-auto">
                ${message}
            </div>
            
            <!-- Footer - Action Buttons -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button type="button" class="cancel-btn px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-colors duration-200 text-base flex-1 sm:flex-none">
                        ${cancelText}
                    </button>
                    <button type="button" class="confirm-btn px-5 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow text-base flex-1 sm:flex-none">
                        ${confirmText}
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Xóa modal cũ và thêm modal mới
    container.innerHTML = modalContent;
    
    // Lấy phần tử modal mới
    const modal = container.querySelector('div');
    
    // Hàm đóng modal
    const closeModal = () => {
        overlay.classList.add('hidden');
        container.innerHTML = '';
    };
    
    // Xử lý sự kiện click trên overlay (bên ngoài modal)
    overlay.addEventListener('click', function overlayClickHandler(e) {
        if (e.target === overlay) {
            closeModal();
            if (onCancel) onCancel();
            // Xóa event listener sau khi dùng
            overlay.removeEventListener('click', overlayClickHandler);
        }
    });
    
    // Xử lý sự kiện nút đóng
    const closeBtn = modal.querySelector('.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            closeModal();
            if (onCancel) onCancel();
        });
    }
    
    // Xử lý sự kiện nút huỷ
    const cancelBtn = modal.querySelector('.cancel-btn');
    cancelBtn.addEventListener('click', () => {
        closeModal();
        if (onCancel) onCancel();
    });
    
    // Xử lý sự kiện nút xác nhận
    const confirmBtn = modal.querySelector('.confirm-btn');
    confirmBtn.addEventListener('click', () => {
        closeModal();
        onConfirm();
    });
    
    // Ngăn sự kiện click trong modal lan ra ngoài
    modal.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}