document.addEventListener('DOMContentLoaded', () => {
    // --- STATE ---
    const state = {
        selectedProduct: null,
        categories: [],
        products: [] // Dùng để search nếu cần
    };

    // --- DOM ELEMENTS ---
    const els = {
        balance: document.getElementById('userBalance'),
        username: document.getElementById('userName'),
        productList: document.getElementById('productList'),
        buyForm: document.getElementById('buyForm'),
        productNameInput: document.getElementById('selectedProductName'),
        productIdInput: document.getElementById('selectedProductId'),
        amountInput: document.getElementById('amount'),
        totalPriceDisplay: document.getElementById('totalPrice'),
        resultArea: document.getElementById('resultArea'),
        btnBuy: document.getElementById('btnBuy'),
        themeToggle: document.getElementById('themeToggle')
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

    // --- THEME ---
    function initTheme() {
        const theme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
        if(els.themeToggle) els.themeToggle.innerText = theme === 'light' ? '🌙 Dark' : '☀️ Light';
    }
    if(els.themeToggle) {
        els.themeToggle.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            els.themeToggle.innerText = next === 'light' ? '🌙 Dark' : '☀️ Light';
        });
    }

    // --- API HANDLER ---
    async function fetchAPI(endpoint, method = 'GET', body = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        const config = { 
            method, 
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken 
            } 
        };
        
        if (body) config.body = JSON.stringify(body);
        
        try {
            const res = await fetch(`/api/${endpoint}`, config);
            
            if (!res.ok) {
                let errorDetails = `Lỗi HTTP ${res.status}`;
                try {
                    const jsonError = await res.json();
                    errorDetails = jsonError.message || jsonError.error || JSON.stringify(jsonError);
                } catch (e) {
                    // Ignore JSON parse error on error response
                }
                throw new Error(errorDetails);
            }

            const text = await res.text();
            if (!text || text.trim() === "") return null;

            try {
                return JSON.parse(text);
            } catch (jsonError) {
                console.error("Invalid JSON response");
                return null;
            }

        } catch (err) {
            console.error(`Fetch API [${endpoint}] Failed:`, err.message);
            return null;
        }
    }

    // --- LOGIC ---
    async function loadProfile() {
        const res = await fetch('/api/profile');
        const json = await res.json();

        document.getElementById("userName").innerText = json.data.username;
        document.getElementById("userBalance").innerText =
            Number(json.data.money).toLocaleString() + "đ";
    }

    function renderSidebar(categories) {
        const ul = document.getElementById("categoryList");
        if (!ul) return;

        ul.innerHTML = "";
        console.log("Rendering sidebar categories:", ul);
        categories.forEach(cat => {
            const li = document.createElement("li");
            li.className = "cursor-pointer px-3 py-2 rounded hover:bg-blue-50 hover:text-blue-600 transition font-medium";
            li.innerHTML = `
                <div class="flex items-center gap-2">
                    <img src="${cat.icon}" class="w-5 h-5 object-contain" onerror="this.style.display='none'">
                    <span>${cat.name}</span>
                    <span class="ml-auto text-xs bg-gray-200 px-2 rounded">${cat.products.length}</span>
                </div>
            `;

            li.onclick = () => renderProductGrid(cat.products);
            ul.appendChild(li);
        });
    }

    document.addEventListener("DOMContentLoaded", loadProfile);
    function renderProductGrid(products) {
        els.productList.innerHTML = "";
        products.forEach(p => els.productList.appendChild(createProductCard(p)));
    }

    async function loadProducts() {
        const res = await fetchAPI('products');

        if (!res?.data || !Array.isArray(res.data)) {
            els.productList.innerHTML = '<div class="text-red-500">Không lấy được sản phẩm</div>';
            return;
        }

        const categories = groupProductsByCategory(res.data);

        state.categories = categories;
        renderSidebar(categories);
        renderProductGrid(categories[0].products);
    }

    function groupProductsByCategory(products) {
        const map = {};

        products.forEach(p => {
            if (!map[p.category_name]) {
                map[p.category_name] = {
                    name: p.category_name,
                    products: []
                };
            }
            map[p.category_name].products.push(p);
        });

        return Object.values(map);
    }



    // Render danh sách phẳng (cho trường hợp API khác)
    function renderFlatList(products) {
        els.productList.innerHTML = '';
        products.forEach(p => {
            els.productList.appendChild(createProductCard(p, p.category_name || 'Khác'));
        });
    }

    function createProductCard(p) {
        const div = document.createElement('div');

        const stock = parseInt(p.amount);
        const stockBadge = stock > 0 
            ? `<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Còn ${stock}</span>`
            : `<span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">Hết hàng</span>`;

        div.className = `
            bg-white border rounded-xl p-4 shadow-sm
            hover:shadow-lg hover:-translate-y-1 transition cursor-pointer
            flex flex-col gap-2
        `;

        div.innerHTML = `
            <div class="flex justify-between items-start gap-2">
                <h4 class="font-bold text-sm line-clamp-2">${p.name}</h4>
                ${p.flag ? `<img src="https://flagcdn.com/24x18/${p.flag.toLowerCase()}.png">` : ""}
            </div>

            <p class="text-xs text-gray-500 line-clamp-2">${p.description || ""}</p>

            <div class="mt-auto flex justify-between items-center">
                <span class="font-black text-blue-600">${money(p.price)}</span>
                ${stockBadge}
            </div>
        `;

        if (stock > 0) div.onclick = () => selectProduct(p, div);
        else div.classList.add("opacity-60", "cursor-not-allowed");

        return div;
    }


    function selectProduct(product, element) {
        // Highlight UI
        document.querySelectorAll('#productList > div > div').forEach(el => el.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50')); // Reset card style
        // Note: Selector trên có thể cần điều chỉnh tùy DOM structure, nhưng logic classlist remove là quan trọng
        
        // Cách đơn giản hơn để reset active state:
        const prevActive = document.querySelector('.product-card-active');
        if(prevActive) prevActive.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50', 'product-card-active');
        
        element.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50', 'product-card-active');

        state.selectedProduct = product;
        
        if(els.productNameInput) els.productNameInput.value = product.name;
        if(els.productIdInput) els.productIdInput.value = product.id;
        if(els.amountInput) {
            els.amountInput.value = 1;
            els.amountInput.max = product.amount; 
        }
        updateTotal();

        if(window.innerWidth < 1024 && els.buyForm) { // Scroll trên mobile/tablet
            els.buyForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function updateTotal() {
        if (!state.selectedProduct || !els.totalPriceDisplay) return;
        const qty = parseInt(els.amountInput?.value) || 0;
        const price = parseFloat(state.selectedProduct.price) || 0;
        const total = qty * price;
        els.totalPriceDisplay.innerText = money(total);
    }

    async function handleBuy() {
        if (!state.selectedProduct) return alert('Vui lòng chọn sản phẩm cần mua!');
        const qty = parseInt(els.amountInput?.value);
        if(!qty || qty < 1 || qty > parseInt(state.selectedProduct.amount)) return alert('Số lượng không hợp lệ!');

        if(els.btnBuy) {
            els.btnBuy.disabled = true;
            els.btnBuy.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';
        }
        if(els.resultArea) els.resultArea.className = 'hidden';

        const result = await fetchAPI('buy', 'POST', {
            product_id: state.selectedProduct.id, 
            amount: qty,
            coupon: document.getElementById('coupon')?.value || ''
        });

        if(els.btnBuy) {
            els.btnBuy.disabled = false;
            els.btnBuy.innerText = 'MUA NGAY';
        }

        if (result && result.success) { 
            if(els.resultArea) {
                els.resultArea.className = 'alert alert-success mt-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg block';
                const apiData = result.data || {};
                const accounts = Array.isArray(apiData.data) ? apiData.data.join('\n') : JSON.stringify(apiData.data || apiData);
                
                els.resultArea.innerHTML = `
                    <div class="font-bold mb-2">✅ ${apiData.msg || 'Mua hàng thành công!'}</div>
                    <textarea class="w-full p-2 text-xs font-mono bg-white border rounded h-32 focus:outline-none" readonly>${accounts}</textarea>
                    <div class="text-xs mt-1 text-gray-500">Giao dịch thành công.</div>
                `;
            }
            loadProfile();
            loadProducts();
        } else {
            if(els.resultArea) {
                els.resultArea.className = 'alert alert-error mt-4 p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg block';
                els.resultArea.innerText = `❌ ${result?.message || result?.msg || 'Giao dịch thất bại. Vui lòng thử lại sau.'}`;
            }
        }
    }

    // --- LISTENERS ---
    if(els.amountInput) els.amountInput.addEventListener('input', updateTotal);
    if(els.btnBuy) els.btnBuy.addEventListener('click', handleBuy);

    // --- BOOTSTRAP ---
    initTheme();
    if(els.productList) {
        loadProfile();
        loadProducts();
    }
});