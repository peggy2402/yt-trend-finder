/**
 * LAYER 2: INFRASTRUCTURE - YOUTUBE SERVICE
 * Wrapper gọi API với cơ chế Retry & Failover tự động
 */
const YTService = (() => {
    const BASE_URL = 'https://www.googleapis.com/youtube/v3';

    async function fetchAPI(endpoint, params = {}, retryCount = 0) {
        const key = KeyManager.getActiveKey();
        if (!key) throw new Error("NO_KEYS_AVAILABLE");

        // Tự động inject region và language vào request (Yêu cầu #3)
        const regionCode = document.getElementById('regionSelect')?.value || 'US';
        const regionConfig = AppConfig.REGIONS.find(r => r.code === regionCode);
        const lang = regionConfig ? regionConfig.lang : 'en';

        // Xây dựng URL parameters
        const urlParams = new URLSearchParams({ ...params, key });
        
        // Chỉ thêm region cho endpoint 'search'
        if (endpoint === 'search') {
            urlParams.append('regionCode', regionCode);
            urlParams.append('relevanceLanguage', lang);
        }

        try {
            const response = await fetch(`${BASE_URL}/${endpoint}?${urlParams}`);
            const data = await response.json();

            if (!response.ok) {
                const errorReason = data.error?.errors?.[0]?.reason || data.error?.message;
                
                // Phát hiện lỗi quota/key để switch key (Yêu cầu #2)
                if ([403, 400, 429].includes(response.status) || 
                    ['quotaExceeded', 'keyInvalid', 'forbidden'].includes(errorReason)) {
                    
                    KeyManager.markBad(key, errorReason);
                    
                    // Đệ quy retry với key mới
                    if (retryCount < 5) {
                        return fetchAPI(endpoint, params, retryCount + 1);
                    } else {
                        throw new Error("ALL_KEYS_EXHAUSTED");
                    }
                }
                throw new Error(data.error?.message || "API Error");
            }

            return data;
        } catch (error) {
            if (error.message === "NO_KEYS_AVAILABLE") throw error;
            if (error.message === "ALL_KEYS_EXHAUSTED") throw error;
            // Network error thì retry 1 lần
            if (retryCount === 0 && !error.message.includes("KEY")) {
                return fetchAPI(endpoint, params, retryCount + 1);
            }
            throw error;
        }
    }

    return { fetch: fetchAPI };
})();