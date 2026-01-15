/**
 * LAYER 2: INFRASTRUCTURE - KEY MANAGER (ENHANCED)
 * Cập nhật: Thêm tính năng Validate Key chủ động
 */
const KeyManager = (() => {
    let keys = [];
    let badKeys = new Set();
    let currentIndex = 0;

    function init() {
        try {
            const stored = localStorage.getItem(AppConfig.STORAGE_KEYS.API_KEYS);
            keys = stored ? JSON.parse(stored) : [];
        } catch (e) {
            console.error("Lỗi load keys từ localStorage", e);
            keys = [];
        }
        _updateUI();
    }

    function save(rawInput) {
        if (typeof rawInput !== 'string') rawInput = "";
        
        // Lọc trùng và chuẩn hóa
        const cleanKeys = [...new Set(
            rawInput.split(/\r?\n/)
            .map(k => k.trim())
            .filter(k => k.length > 20)
        )];

        keys = cleanKeys;
        badKeys.clear(); // Reset bad keys khi user lưu mới
        currentIndex = 0;
        
        try {
            localStorage.setItem(AppConfig.STORAGE_KEYS.API_KEYS, JSON.stringify(keys));
        } catch (e) { console.error(e); }

        _updateUI();
        return keys.length;
    }

    // --- NEW: Validate Single Key ---
    async function validateKey(apiKey) {
        // Gọi endpoint nhẹ nhất: channels?mine=true hoặc search đơn giản
        // Search tốn 100 quota, channels id tốn 1 quota. Dùng channels id giả để test key validity.
        // Tuy nhiên channels cần id. Cách an toàn nhất để check key sống/chết mà tốn ít quota là search trả về 0 kết quả hoặc list categories.
        // Dùng videoCategories (1 quota) là tối ưu nhất.
        const url = `https://www.googleapis.com/youtube/v3/videoCategories?part=snippet&regionCode=US&key=${apiKey}`;
        
        try {
            const res = await fetch(url);
            const data = await res.json();
            
            if (!res.ok) {
                return { 
                    valid: false, 
                    reason: data.error?.errors?.[0]?.reason || 'Unknown Error',
                    code: res.status 
                };
            }
            return { valid: true };
        } catch (e) {
            return { valid: false, reason: 'Network Error', code: 0 };
        }
    }

    // --- Standard Logic ---
    function getActiveKey() {
        if (keys.length === 0) return null;
        let attempts = 0;
        while (attempts < keys.length) {
            if (currentIndex >= keys.length) currentIndex = 0;
            const key = keys[currentIndex];
            if (!badKeys.has(key)) return key;
            _rotate();
            attempts++;
        }
        return null;
    }

    function markBad(key, reason) {
        console.warn(`[KeyManager] Key marked BAD: ${key.substring(0,8)}... Reason: ${reason}`);
        badKeys.add(key);
        _rotate();
        _updateUI();
    }

    function _rotate() { currentIndex = (currentIndex + 1) % keys.length; }

    function _updateUI() {
        const total = keys.length;
        const alive = total - badKeys.size;
        const event = new CustomEvent('zt:keyUpdate', { detail: { total, alive } });
        document.dispatchEvent(event);
    }

    function getRawList() { return keys.join('\n'); }

    return { init, save, getActiveKey, markBad, getRawList, validateKey };
})();