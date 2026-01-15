/**
 * LAYER 1: CONFIGURATION & CONSTANTS
 * Cập nhật: Thêm Time Ranges, Global Region
 */
const AppConfig = {
    DEFAULT_RPM: 0.3,
    STORAGE_KEYS: {
        API_KEYS: 'zt_yt_api_keys_v5',
        RPM: 'zt_yt_rpm',
        THEME: 'zt_theme',
        REGION: 'zt_region'
    },
    
    // Cập nhật: Thêm Global vào đầu danh sách
    REGIONS: [
        { code: '', name: 'Global (Toàn cầu)', flag: '🌍', lang: 'en' }, // Option Global
        { code: 'VN', name: 'Vietnam', flag: '🇻🇳', lang: 'vi' },
        { code: 'US', name: 'United States', flag: '🇺🇸', lang: 'en' },
        { code: 'GB', name: 'United Kingdom', flag: '🇬🇧', lang: 'en' },
        { code: 'JP', name: 'Japan', flag: '🇯🇵', lang: 'ja' },
        { code: 'KR', name: 'South Korea', flag: '🇰🇷', lang: 'ko' },
        { code: 'DE', name: 'Germany', flag: '🇩🇪', lang: 'de' },
        { code: 'FR', name: 'France', flag: '🇫🇷', lang: 'fr' },
        { code: 'IN', name: 'India', flag: '🇮🇳', lang: 'en' },
        { code: 'BR', name: 'Brazil', flag: '🇧🇷', lang: 'pt' },
        { code: 'RU', name: 'Russia', flag: '🇷🇺', lang: 'ru' },
        { code: 'CA', name: 'Canada', flag: '🇨🇦', lang: 'en' },
        { code: 'AU', name: 'Australia', flag: '🇦🇺', lang: 'en' }
    ],

    STOP_WORDS: new Set([
        'video', 'vlog', 'review', '2024', '2025', 'official', 'full', 'hd', '4k',
        'cách', 'hướng', 'dẫn', 'là', 'gì', 'của', 'những', 'top', 'best',
        'the', 'and', 'with', 'for', 'how', 'to', 'in', 'on', 'at', 'vs', 'or'
    ])
};