/**
 * NDGroup Analytics Pro - Ultimate Version V3.7 (API Time Filter + Massive Regions)
 */

// --- STATE VARIABLES ---
let apiKeys = [];
try {
    const stored = localStorage.getItem('yt_api_keys');
    apiKeys = stored ? JSON.parse(stored) : [];
    const oldKey = localStorage.getItem('yt_api_key');
    if (oldKey && !apiKeys.includes(oldKey)) apiKeys.push(oldKey);
} catch (e) { apiKeys = []; }

let currentKeyIndex = 0;
let currentRpm = 0.3;
let globalVideos = []; 
let filteredVideos = []; 
let currentNicheData = {}; 
let currentPage = 1;
const itemsPerPage = 10;
const STOP_WORDS = ["video", "youtube", "2024", "2025", "review", "vlog", "new", "mới", "nhất", "tại", "của", "là", "gì", "how", "to", "in", "on", "the", "a", "and", "with", "|", "-", "official", "channel", "kênh", "tv", "full", "hd", "4k", "phim", "nhạc", "song", "music", "cover", "live", "vs", "cực", "top", "những", "các", "tập", "phần", "short", "shorts", "tik", "tok", "tiktok", "cho", "người"];

// --- HELPERS (EXPANDED TIERS) ---
// Tier 1: Highest RPM (Rich Western/Nordic)
const TIER_1_COUNTRIES = ['US', 'GB', 'CA', 'AU', 'DE', 'CH', 'SE', 'NO', 'DK', 'NZ', 'NL', 'IE'];
// Tier 2: High/Mid RPM (Developed/Emerging)
const TIER_2_COUNTRIES = ['FR', 'ES', 'IT', 'JP', 'KR', 'BR', 'RU', 'SG', 'AE', 'SA', 'BE', 'FI', 'PL', 'IL', 'KW', 'QA'];

const getFlag = (code) => {
    if (!code || code === 'N/A') return '🌐';
    try {
        const codePoints = code.toUpperCase().split('').map(char =>  127397 + char.charCodeAt());
        return String.fromCodePoint(...codePoints);
    } catch (e) { return '🌐'; }
}

const getTierInfo = (code) => {
    if (!code || code === 'N/A') return { label: 'Global', class: 'text-slate-400 bg-slate-50 border-slate-100' };
    const c = code.toUpperCase();
    if (TIER_1_COUNTRIES.includes(c)) return { label: 'Tier 1 💰', class: 'text-green-700 bg-green-50 border-green-200 ring-1 ring-green-100' };
    if (TIER_2_COUNTRIES.includes(c)) return { label: 'Tier 2 📈', class: 'text-blue-700 bg-blue-50 border-blue-200' };
    return { label: 'Tier 3 🌏', class: 'text-slate-600 bg-slate-100 border-slate-200' };
}

function formatDuration(isoDuration) {
    if (!window.moment) return isoDuration;
    const duration = moment.duration(isoDuration);
    const hours = Math.floor(duration.asHours());
    const minutes = duration.minutes();
    const seconds = duration.seconds();
    return hours > 0 
        ? `${hours}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}` 
        : `${minutes}:${seconds.toString().padStart(2, '0')}`;
}

// --- DARK MODE LOGIC ---
function toggleDarkMode() {
    const html = document.documentElement;
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
}
// --- IMMORTAL SMART FETCH ---
async function smartFetch(urlTemplate) {
    if (!apiKeys || apiKeys.length === 0) throw new Error("Vui lòng nhập API Key trong phần Cài đặt!");

    let errors = [];
    let success = false;
    let finalResponse = null;

    for (let i = 0; i < apiKeys.length; i++) {
        const tryIndex = (currentKeyIndex + i) % apiKeys.length;
        const key = apiKeys[tryIndex];
        const cleanKey = key.trim().replace(/['"]/g, ''); 
        const url = urlTemplate.replace('{API_KEY}', cleanKey);

        try {
            const response = await fetch(url);
            
            if (response.ok) {
                if (tryIndex !== currentKeyIndex) currentKeyIndex = tryIndex;
                finalResponse = response;
                success = true;
                break;
            }

            let reason = response.statusText;
            try {
                const errJson = await response.json();
                reason = errJson.error?.errors?.[0]?.reason || errJson.error?.message || reason;
            } catch (e) {}
            console.warn(`Key #${tryIndex} Fail: ${reason}`);
            errors.push(`Key ${tryIndex}: ${reason}`);
            continue; 

        } catch (err) {
            console.warn(`Key #${tryIndex} Network Error`);
            errors.push(`Key ${tryIndex}: Network Error`);
            continue;
        }
    }

    if (success && finalResponse) return finalResponse;

    console.error("All keys failed:", errors);
    throw new Error(`Hết API Key khả dụng! Lỗi: ${errors[0] || 'Unknown'}`);
}

// --- INIT ---
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    updateApiKeyUI();
    const slider = document.getElementById('rpmSlider');
    if(slider) { slider.value = currentRpm; document.getElementById('rpmDisplay').innerText = '$' + currentRpm.toFixed(1); }
    
    document.getElementById('keyword').addEventListener('keypress', (e) => { if(e.key === 'Enter') analyzeKeywords(); });
    
    // Auto-update filters (Only local ones)
    ['minViews', 'minSubs', 'filterFormat'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.addEventListener('change', applyClientFilters);
    });
});

// --- UI HELPERS ---
function toggleFilters() {
    const el = document.getElementById('advancedFilters');
    const arrow = document.getElementById('filterArrow');
    el.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

function updateApiKeyUI() {
    const dot = document.getElementById('apiKeyDot');
    const text = document.getElementById('apiKeyBtnText');
    if (apiKeys.length > 0) {
        dot.classList.replace('bg-gray-300', 'bg-green-500');
        text.innerText = `Active (${apiKeys.length})`;
        text.classList.add('text-green-700');
    } else {
        dot.classList.replace('bg-green-500', 'bg-gray-300');
        text.innerText = 'Nhập API Key';
        text.classList.remove('text-green-700');
    }
}

function openSettings() { document.getElementById('inputApiKey').value = apiKeys.join('\n'); toggleModal('settingsModal', true); }
function closeSettings() { toggleModal('settingsModal', false); }
function toggleModal(modalId, show) {
    const modal = document.getElementById(modalId);
    if(show) { modal.classList.remove('invisible', 'pointer-events-none'); setTimeout(() => modal.classList.add('active'), 10); } 
    else { modal.classList.remove('active'); setTimeout(() => modal.classList.add('invisible', 'pointer-events-none'), 200); }
}

function saveApiKey() {
    const val = document.getElementById('inputApiKey').value.trim();
    if (val) {
        const keys = val.split(/[\n,]+/).map(k => k.replace(/['" ]/g, '').trim()).filter(k => k.length > 10);
        if (keys.length > 0) {
            apiKeys = keys;
            localStorage.setItem('yt_api_keys', JSON.stringify(apiKeys));
            updateApiKeyUI();
            showToast(`✅ Đã lưu ${keys.length} API Key!`, 'success');
            closeSettings();
        } else { showToast('❌ Key không hợp lệ', 'error'); }
    } else { showToast('⚠️ Vui lòng nhập Key', 'error'); }
}

function updateRpm(val) { currentRpm = parseFloat(val); document.getElementById('rpmDisplay').innerText = '$' + currentRpm.toFixed(1); if (filteredVideos.length > 0) renderVideoTable(); }

function showToast(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if(!container) return;
    const el = document.createElement('div');
    const color = type === 'error' ? 'border-red-500 text-red-600' : 'border-green-500 text-green-600';
    const icon = type === 'error' ? '<i class="fa-solid fa-triangle-exclamation"></i>' : '<i class="fa-solid fa-check-circle"></i>';
    el.className = `toast bg-white border-l-4 ${color} px-4 py-3 rounded shadow-xl flex items-start gap-3 min-w-[320px] max-w-md transform transition-all z-[10000]`;
    el.innerHTML = `${icon} <span class="font-bold text-sm text-slate-700 leading-snug pt-0.5">${msg}</span>`;
    container.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateX(100%)'; setTimeout(() => el.remove(), 300); }, 5000);
}

// --- MAIN LOGIC ---
async function analyzeKeywords() {
    if (apiKeys.length === 0) { openSettings(); return showToast('Chưa nhập API Key!', 'error'); }
    const keyword = document.getElementById('keyword').value.trim();
    if (!keyword) return showToast('Vui lòng nhập từ khóa!', 'error');

    // Get Inputs
    const region = document.getElementById('filterRegion').value;
    const maxResultsEl = document.getElementById('maxResults');
    const maxResults = Math.min(50, Math.max(1, parseInt(maxResultsEl.value) || 50)); 
    const isDeepScan = document.getElementById('deepScanToggle').checked;
    const timeFilter = document.getElementById('filterTime').value;

    // --- FIX: API-LEVEL TIME FILTER ---
    let publishedAfter = '';
    if (timeFilter !== 'any') {
        const now = moment();
        if (timeFilter === 'hour') publishedAfter = now.subtract(1, 'hours').toISOString();
        else if (timeFilter === 'today') publishedAfter = now.subtract(1, 'days').toISOString();
        else if (timeFilter === 'week') publishedAfter = now.subtract(7, 'days').toISOString();
        else if (timeFilter === 'month') publishedAfter = now.subtract(1, 'months').toISOString();
        else if (timeFilter === 'year') publishedAfter = now.subtract(1, 'years').toISOString();
    }
    // -----------------------------------------

    document.getElementById('resultsArea').classList.add('hidden');
    document.getElementById('loading').classList.remove('hidden');
    const loadingTextEl = document.getElementById('loadingText');
    const loadingTitleEl = document.getElementById('loadingTitle');
    
    loadingTitleEl.innerText = isDeepScan ? "🚀 Đang Deep Scan (5 Pages)..." : "📡 Đang quét dữ liệu...";
    document.getElementById('analyzeBtn').disabled = true;
    document.getElementById('analyzeBtn').innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';

    try {
        let collectedItems = [];
        let nextPageToken = '';
        let pagesToFetch = isDeepScan ? 5 : 1; 

        // 1. SEARCH LOOP
        for (let i = 0; i < pagesToFetch; i++) {
            if (isDeepScan && i > 0) loadingTextEl.innerText = `...Đang quét trang ${i + 1}/${pagesToFetch} | Đã có: ${collectedItems.length} video`;

            let searchUrl = `https://www.googleapis.com/youtube/v3/search?part=snippet&q=${encodeURIComponent(keyword)}&type=video&maxResults=${maxResults}&order=viewCount&key={API_KEY}`;
            
            // Append API Filters
            if (region !== 'GLOBAL') searchUrl += `&regionCode=${region}`;
            if (publishedAfter) searchUrl += `&publishedAfter=${publishedAfter}`; // Strict Time Filter
            if (nextPageToken) searchUrl += `&pageToken=${nextPageToken}`;

            const searchRes = await smartFetch(searchUrl);
            const searchData = await searchRes.json();
            
            if (!searchData.items || searchData.items.length === 0) break;
            collectedItems = collectedItems.concat(searchData.items);
            nextPageToken = searchData.nextPageToken;
            if (!nextPageToken) break;
        }

        if (collectedItems.length === 0) throw new Error('Không tìm thấy video nào (Thử đổi bộ lọc thời gian hoặc từ khóa).');

        // 2. VIDEO DETAILS
        loadingTextEl.innerText = `📊 Đang phân tích chỉ số ${collectedItems.length} video...`;
        const allVideoIds = collectedItems.map(i => i.id.videoId);
        const chunkSize = 50;
        let finalVideoItems = [];
        
        for (let i = 0; i < allVideoIds.length; i += chunkSize) {
            const chunkIds = allVideoIds.slice(i, i + chunkSize).join(',');
            const videoUrl = `https://www.googleapis.com/youtube/v3/videos?part=statistics,snippet,contentDetails&id=${chunkIds}&key={API_KEY}`;
            const videoRes = await smartFetch(videoUrl);
            const videoData = await videoRes.json();
            if (videoData.items) finalVideoItems = finalVideoItems.concat(videoData.items);
        }

        // 3. CHANNEL DETAILS
        loadingTextEl.innerText = `🌍 Đang check quốc gia kênh...`;
        const distinctChannelIds = [...new Set(finalVideoItems.map(i => i.snippet.channelId))];
        const channelMap = {};
        const chanChunkSize = 40; 
        
        for (let i = 0; i < distinctChannelIds.length; i += chanChunkSize) {
            const chunkIds = distinctChannelIds.slice(i, i + chanChunkSize).join(',');
            if(chunkIds) {
                try {
                    const channelUrl = `https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet&id=${chunkIds}&key={API_KEY}`;
                    const channelRes = await smartFetch(channelUrl);
                    const channelData = await channelRes.json();
                    if(channelData.items) {
                        channelData.items.forEach(c => {
                            channelMap[c.id] = {
                                subs: parseInt(c.statistics.subscriberCount) || 0,
                                thumb: c.snippet.thumbnails.default?.url,
                                country: c.snippet.country || 'N/A'
                            };
                        });
                    }
                } catch(err) {}
            }
        }

        // 4. MAP DATA
        globalVideos = finalVideoItems.map(item => {
            const views = parseInt(item.statistics.viewCount) || 0;
            const channelInfo = channelMap[item.snippet.channelId] || { subs: 0, country: 'N/A' };
            const subs = channelInfo.subs;
            const ratio = subs > 0 ? (views / subs) : (views > 10000 ? 5 : 1);
            const durationIso = item.contentDetails.duration;
            const durationSec = moment.duration(durationIso).asSeconds();
            const isShort = durationSec <= 60; 

            return {
                id: item.id,
                title: item.snippet.title,
                channel: item.snippet.channelTitle,
                channelId: item.snippet.channelId,
                country: channelInfo.country,
                views: views,
                subs: subs,
                ratio: ratio,
                tags: item.snippet.tags || [],
                publishedAt: new Date(item.snippet.publishedAt),
                thumbnail: item.snippet.thumbnails.medium.url,
                duration: item.contentDetails.duration,
                isShort: isShort
            };
        });

        applyClientFilters();

        document.getElementById('resultsArea').classList.remove('hidden');
        document.getElementById('resultsArea').scrollIntoView({ behavior: 'smooth' });
        showToast(`✅ Hoàn tất! Tìm thấy ${globalVideos.length} kết quả.`, 'success');

    } catch (e) {
        console.error(e);
        showToast(e.message, 'error');
    } finally {
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('analyzeBtn').disabled = false;
        document.getElementById('analyzeBtn').innerHTML = '<span>PHÂN TÍCH</span> <i class="fa-solid fa-radar"></i>';
    }
}

// --- CLIENT SIDE FILTERING (No Time Filter Here - Already done by API) ---
function applyClientFilters() {
    const minViews = parseInt(document.getElementById('minViews').value) || 0;
    const minSubs = parseInt(document.getElementById('minSubs').value) || 0;
    const formatFilter = document.getElementById('filterFormat').value;
    
    filteredVideos = globalVideos.filter(v => {
        // Format
        if (formatFilter === 'short' && !v.isShort) return false;
        if (formatFilter === 'video' && v.isShort) return false;
        
        // Metrics
        return v.views >= minViews && v.subs >= minSubs;
    });

    currentPage = 1; 
    renderStrategicVerdict();
    analyzeTopKeywords();
    analyzeMicroNiches();
    renderCompetitors();
    renderUploadTime();
    renderVideoTable();
    document.getElementById('resultCount').innerText = filteredVideos.length;
}

// 1. VERDICT
function renderStrategicVerdict() {
    if (filteredVideos.length === 0) return;

    // 1. Tính toán chỉ số
    const avgViews = filteredVideos.reduce((sum, v) => sum + v.views, 0) / filteredVideos.length;
    const uniqueChannels = new Set(filteredVideos.map(v => v.channelId)).size;
    
    // Thuật toán điểm số (Giữ nguyên logic của bạn)
    let score = 50; 
    if (avgViews > 500000) score += 20; else if (avgViews > 100000) score += 10;
    const saturation = (filteredVideos.length / uniqueChannels); 
    if (saturation > 2) score -= 15; else score += 15;
    
    // Clamp score 0-100
    const finalScore = Math.min(100, Math.max(0, score));

    // 2. Xác định Trạng thái & Màu sắc
    let verdictData = { text: "RẤT KHÓ KHĂN", color: "text-slate-500", desc: "Thị trường bão hòa, ít view.", hex: "#64748b" }; // Gray
    
    if(finalScore >= 75) {
        verdictData = { text: "🔥 SIÊU TIỀM NĂNG", color: "text-emerald-500", desc: "Cầu cao, cung thấp. Nên làm ngay!", hex: "#10b981" }; // Emerald
    } else if(finalScore >= 50) {
        verdictData = { text: "🚀 KHÁ ỔN ĐỊNH", color: "text-blue-500", desc: "Cần content chất lượng để cạnh tranh.", hex: "#3b82f6" }; // Blue
    }

    // 3. Update Text UI
    document.getElementById('statVolume').innerText = (avgViews/1000).toFixed(0) + 'K';
    document.getElementById('statComp').innerText = uniqueChannels;
    document.getElementById('statScoreBottom').innerText = finalScore + '/100';
    
    const vText = document.getElementById('verdictText');
    const vDesc = document.getElementById('verdictDesc');
    
    vText.innerText = verdictData.text;
    vText.className = `text-2xl font-black mb-1 ${verdictData.color}`;
    vDesc.innerText = verdictData.desc;

    // 4. RENDER SVG GAUGE (Vẽ biểu đồ mới)
    const gaugeContainer = document.getElementById('verdictGaugeArea');
    
    // Tính toán góc quay của kim (0 điểm = -90deg, 100 điểm = 90deg)
    const needleAngle = (finalScore / 100) * 180 - 90;

    gaugeContainer.innerHTML = `
        <div class="relative w-64 h-32 overflow-hidden select-none">
            <!-- SVG Gauge Background -->
            <svg viewBox="0 0 200 100" class="w-full h-full">
                <!-- Track Background (Gray) -->
                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="#e2e8f0" stroke-width="20" stroke-linecap="round" class="dark:stroke-slate-700" />
                
                <!-- Active Track (Gradient Color) -->
                <defs>
                    <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#ef4444" /> <!-- Red -->
                        <stop offset="50%" stop-color="#eab308" /> <!-- Yellow -->
                        <stop offset="100%" stop-color="#10b981" /> <!-- Green -->
                    </linearGradient>
                </defs>
                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="url(#gaugeGradient)" stroke-width="20" stroke-linecap="round" 
                      stroke-dasharray="251.2" stroke-dashoffset="${251.2 - (251.2 * finalScore / 100)}" 
                      class="transition-all duration-1000 ease-out" />
            </svg>

            <!-- Needle Container (Rotates) -->
            <div class="absolute bottom-0 left-1/2 w-full h-full flex justify-center items-end" 
                 style="transform-origin: bottom center; transform: rotate(${needleAngle}deg); transition: transform 1s cubic-bezier(0.4, 0, 0.2, 1);">
                <!-- The Needle -->
                <div class="w-1.5 h-24 bg-slate-800 rounded-full relative -bottom-1 shadow-lg dark:bg-white"></div>
            </div>
            
            <!-- Center Hub (Che chân kim) -->
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-8 h-8 bg-white border-4 border-slate-100 rounded-full shadow-md z-10 dark:bg-slate-800 dark:border-slate-600"></div>
        </div>

        <!-- Score Display -->
        <div class="text-center mt-4 z-20">
            <div class="text-4xl font-black ${verdictData.color} transition-all duration-700" id="bigScore">${0}</div>
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Điểm Tiềm Năng</div>
        </div>
    `;

    // Animation số nhảy (Count up effect)
    setTimeout(() => {
        const scoreEl = document.getElementById('bigScore');
        if(scoreEl) {
            let start = 0;
            const duration = 1000;
            const stepTime = Math.abs(Math.floor(duration / finalScore));
            const timer = setInterval(() => {
                start += 1;
                scoreEl.innerText = start;
                if (start >= finalScore) {
                    clearInterval(timer);
                    scoreEl.innerText = finalScore;
                }
            }, stepTime);
        }
    }, 100);
}

// 2. TOP KEYWORDS
function analyzeTopKeywords() {
    const wordCount = {};
    filteredVideos.forEach(video => {
        const cleanTitle = video.title.toLowerCase().replace(/[^\w\sàáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ]/g, ' ');
        const words = cleanTitle.split(/\s+/);
        words.forEach(word => {
            if (word.length > 2 && !STOP_WORDS.includes(word) && isNaN(word)) wordCount[word] = (wordCount[word] || 0) + 1;
        });
    });
    const sortedKeywords = Object.entries(wordCount).sort((a, b) => b[1] - a[1]).slice(0, 5);
    
    if (sortedKeywords.length > 0) {
        document.getElementById('kingKeyword').innerText = `"${sortedKeywords[0][0]}"`;
        document.getElementById('kingCount').innerText = sortedKeywords[0][1];
    } else {
        document.getElementById('kingKeyword').innerText = "---";
        document.getElementById('kingCount').innerText = "0";
    }

    const listContainer = document.getElementById('topKeywordsList');
    if(listContainer) {
        if (sortedKeywords.length > 1) {
            listContainer.innerHTML = sortedKeywords.slice(1).map(([word, count], index) => `
                <div class="flex justify-between items-center group cursor-default">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-orange-100 text-orange-600 text-[9px] font-bold flex items-center justify-center">${index + 2}</span>
                        <span class="font-medium text-slate-700 capitalize group-hover:text-orange-600 transition-colors">"${word}"</span>
                    </div>
                    <span class="text-xs font-mono text-slate-400 bg-white px-1.5 py-0.5 rounded border border-orange-100">${count}</span>
                </div>
            `).join('');
        } else {
            listContainer.innerHTML = '<div class="text-slate-400 text-xs italic">Chưa đủ dữ liệu...</div>';
        }
    }
}

// 3. MICRO NICHES
function analyzeMicroNiches() {
    const container = document.getElementById('microNicheContainer');
    const grid = document.getElementById('microNicheGrid');
    if (!container || !grid) return;
    const tagMap = {};
    filteredVideos.forEach(v => {
        let tags = v.tags.length > 0 ? v.tags : v.title.toLowerCase().split(/\s+/);
        const uniqueTags = new Set(tags.map(t => String(t).toLowerCase().trim()));
        uniqueTags.forEach(tag => {
            if (tag.length < 3 || STOP_WORDS.includes(tag) || /^\d+$/.test(tag)) return;
            if (!tagMap[tag]) { tagMap[tag] = { count: 0, totalRatio: 0, totalViews: 0, channels: new Set(), videoList: [] }; }
            tagMap[tag].count++; tagMap[tag].totalRatio += v.ratio; tagMap[tag].totalViews += v.views; tagMap[tag].channels.add(v.channel); tagMap[tag].videoList.push(v);
        });
    });
    let niches = Object.keys(tagMap).map(tag => {
        const d = tagMap[tag];
        return { tag: tag, count: d.count, avgRatio: d.count > 0 ? d.totalRatio / d.count : 0, avgViews: d.count > 0 ? d.totalViews / d.count : 0, competitors: Array.from(d.channels), videos: d.videoList };
    }).filter(n => n.count >= 2).sort((a, b) => b.avgViews - a.avgViews).slice(0, 6);
    currentNicheData = {}; niches.forEach(n => currentNicheData[n.tag] = n);
    if (niches.length === 0) { container.classList.add('hidden'); return; }
    container.classList.remove('hidden');
    grid.innerHTML = niches.map(n => {
        // ... strategy logic ...
        let status = "Tiềm Năng"; let colorClass = "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"; let icon = "fa-chess-pawn";
        if (n.avgRatio > 3 && n.competitors.length <= 3) { status = "💎 Siêu Ngách"; colorClass = "bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300"; icon = "fa-diamond"; }
        else if (n.avgViews > 50000) { status = "🔥 Viral"; colorClass = "bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300"; icon = "fa-fire"; }
        
        const ratioColor = n.avgRatio > 1 ? 'text-green-600 dark:text-green-400' : 'text-slate-600 dark:text-slate-400';
        const successRatePercent = (n.avgRatio * 100).toFixed(0) + '%';

        return `<div onclick="openNicheDetails('${n.tag}')" class="micro-niche-card bg-white p-5 rounded-xl border border-slate-200 shadow-sm hover:shadow-md cursor-pointer relative group overflow-hidden transition-all dark:bg-slate-800 dark:border-slate-700">
            <div class="absolute top-0 right-0 p-3 opacity-5 group-hover:opacity-10 transition-opacity"><i class="fa-solid ${icon} text-5xl text-indigo-600 micro-icon dark:text-indigo-400"></i></div>
            <div class="flex justify-between items-start mb-3 relative z-10">
                <h4 class="font-bold text-slate-800 text-sm uppercase tracking-wide break-words flex-1 min-w-0 pr-2 line-clamp-2 dark:text-white" title="${n.tag}">${n.tag}</h4>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold ${colorClass} shadow-sm whitespace-nowrap flex-shrink-0">${status}</span>
            </div>
            <div class="grid grid-cols-2 gap-2 mb-3 relative z-10">
                <div class="bg-slate-50 p-2 rounded border border-slate-100 dark:bg-slate-700/50 dark:border-slate-600">
                    <span class="block text-[10px] text-slate-400">Avg Views</span><span class="block text-xs font-bold text-slate-700 dark:text-slate-200">${(n.avgViews / 1000).toFixed(1)}K</span>
                </div>
                <div class="bg-slate-50 p-2 rounded border border-slate-100 dark:bg-slate-700/50 dark:border-slate-600">
                    <span class="block text-[10px] text-slate-400">Success Rate</span><span class="block text-xs font-bold ${ratioColor}">${successRatePercent}</span>
                </div>
            </div>
            <div class="relative z-10 pt-3 border-t border-slate-100 dark:border-slate-700">
                <div class="flex items-start gap-2">
                    <div class="mt-0.5"><i class="fa-solid fa-lightbulb text-yellow-500 text-xs"></i></div>
                    <div><span class="block text-[10px] text-slate-500 dark:text-slate-400">Chiến lược: <strong class="text-slate-700 dark:text-slate-300">...</strong></span><span class="block text-[9px] text-indigo-500 font-bold mt-1 dark:text-indigo-400">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></span></div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function openNicheDetails(tag) {
    const data = currentNicheData[tag];
    if (!data) return;
    const channelStats = {};
    data.videos.forEach(v => { if (!channelStats[v.channel]) channelStats[v.channel] = { views: 0, count: 0 }; channelStats[v.channel].views += v.views; channelStats[v.channel].count += 1; });
    const topChannelName = Object.keys(channelStats).sort((a, b) => channelStats[b].views - channelStats[a].views)[0];
    const topChannelStats = channelStats[topChannelName];
    const keyVideo = data.videos.sort((a, b) => b.views - a.views)[0];
    document.getElementById('modalNicheTitle').innerText = tag;
    let strategyText = "Tập trung nội dung chất lượng cao";
    if (data.avgRatio > 3 && data.competitors.length <= 3) strategyText = "Blue Ocean - Ít đối thủ, dễ lên top";
    else if (data.avgViews > 50000) strategyText = "Trend Viral - Cần làm video ngắn gọn, hấp dẫn";
    document.getElementById('modalStrategy').innerText = strategyText;
    document.getElementById('modalChannelList').innerHTML = data.competitors.slice(0, 8).map(c => `<span class="inline-block bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs mr-2 mb-2 border border-slate-200">${c}</span>`).join('');
    document.getElementById('modalTopChannel').innerHTML = `<div class="font-bold text-slate-800 text-lg">${topChannelName}</div><div class="text-xs text-slate-500">Đang sở hữu ${topChannelStats.count} video trong ngách này với tổng ${topChannelStats.views.toLocaleString()} views.</div>`;
    document.getElementById('modalKeyVideo').innerHTML = `<div class="flex gap-3 items-start p-3 bg-slate-50 rounded-lg border border-slate-100"><img src="${keyVideo.thumbnail}" class="w-24 h-16 object-cover rounded shadow-sm"><div><a href="https://youtu.be/${keyVideo.id}" target="_blank" class="font-bold text-sm text-slate-800 hover:text-red-600 line-clamp-2">${keyVideo.title}</a><div class="text-xs text-green-600 font-bold mt-1">${keyVideo.views.toLocaleString()} views</div></div></div>`;
    toggleModal('nicheDetailsModal', true);
}
function closeNicheModal() { toggleModal('nicheDetailsModal', false); }

function formatNumberSmart(num, hideSmall = false) {
    if (!num) return '0';
    if (num < 1000) {
        return hideSmall ? '' : num.toLocaleString(); 
    }
    if (num < 1000000) return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K'; 
    return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M'; 
}
// 4. COMPETITORS
function renderCompetitors() {
    const channels = {};
    filteredVideos.forEach(v => { 
        if(!channels[v.channelId]) channels[v.channelId] = { name: v.channel, count: 0, views: 0 }; 
        channels[v.channelId].count++; 
        channels[v.channelId].views += v.views; 
    });
    const sorted = Object.values(channels).sort((a,b) => b.views - a.views).slice(0, 5);
    
    document.getElementById('competitorList').innerHTML = sorted.map((c, i) => `
        <div class="group flex justify-between items-center p-2 rounded-lg 
                    border border-transparent 
                    hover:bg-slate-100 hover:border-slate-200 
                    transition-all duration-150 
                    text-sm 
                    dark:hover:bg-slate-700/60 dark:hover:border-slate-600">

            <div class="flex items-center gap-2 overflow-hidden">
                <!-- Index -->
                <span class="w-5 h-5 rounded-md 
                            bg-slate-200 text-slate-700 text-[10px] font-bold 
                            flex items-center justify-center flex-shrink-0 
                            group-hover:bg-slate-300
                            dark:bg-slate-600 dark:text-slate-100 dark:group-hover:bg-slate-500">
                    ${i+1}
                </span>

                <!-- Name -->
                <span class="font-semibold text-slate-700 truncate max-w-[140px]
                            group-hover:text-slate-900
                            dark:text-slate-200 dark:group-hover:text-white"
                    title="${c.name}">
                    ${c.name}
                </span>
            </div>

            <!-- Views -->
            <span class="text-[10px] font-mono text-slate-500 
                        group-hover:text-slate-600
                        dark:text-slate-400 dark:group-hover:text-slate-300">
                ${formatNumberSmart(c.views)} views
            </span>
        </div>
    `).join('');
}
// 5. UPLOAD TIME
function renderUploadTime() {
    const hours = new Array(24).fill(0).map(() => ({ count: 0, totalViews: 0, avg: 0 }));
    filteredVideos.forEach(v => { const h = v.publishedAt.getHours(); hours[h].count++; hours[h].totalViews += v.views; });
    let maxAvg = 0; hours.forEach(h => { if (h.count > 0) { h.avg = h.totalViews / h.count; if (h.avg > maxAvg) maxAvg = h.avg; } });
    let bestHourIndex = 0; let currentBestAvg = 0; hours.forEach((h, index) => { if (h.avg > currentBestAvg) { currentBestAvg = h.avg; bestHourIndex = index; } });
    const bestTimeEl = document.getElementById('bestTimeText');
    if(bestTimeEl) { bestTimeEl.innerText = maxAvg > 0 ? `${bestHourIndex}h:00 - ${bestHourIndex + 1}h:00` : "--"; bestTimeEl.className = maxAvg > 0 ? "text-xs text-green-700 font-bold bg-green-100 px-2 py-1 rounded border border-green-200 shadow-sm" : "text-xs text-slate-400 font-bold bg-slate-100 px-2 py-1 rounded"; }
    const container = document.getElementById('uploadHeatmap');
    if(!container) return;
    container.innerHTML = hours.map((d, h) => {
        const isEmpty = d.count === 0;
        const heightPercent = isEmpty ? 6 : (d.avg / maxAvg) * 100;

        const intensity =
            h === bestHourIndex ? "bg-emerald-600"
            : d.avg > maxAvg * 0.7 ? "bg-emerald-400"
            : d.avg > maxAvg * 0.4 ? "bg-amber-400"
            : "bg-rose-400";

        return `
        <div class="group w-full h-full flex items-end relative border-r border-slate-100 dark:border-slate-700 last:border-0">

            <!-- Tooltip -->
            <div class="absolute -top-7 left-1/2 -translate-x-1/2
                        text-[10px] px-2 py-0.5 rounded-md
                        bg-slate-900 text-white whitespace-nowrap
                        opacity-0 group-hover:opacity-100
                        transition pointer-events-none z-10">
                ${h}h – ${(d.avg/1000 || 0).toFixed(1)}K avg
            </div>

            <!-- Bar -->
            <div
                style="height: ${Math.max(8, heightPercent)}%"
                class="w-full mx-0.5 rounded-t-md
                    ${isEmpty ? "bg-slate-200 dark:bg-slate-700" : intensity}
                    transition-all duration-200
                    group-hover:brightness-110
                    group-hover:-translate-y-[1px]
                    origin-bottom">
            </div>
        </div>`;
    }).join('');
    // ===== Render dòng giờ vàng dưới biểu đồ =====
    const wrapper = container.parentElement;

    let goldenTimeEl = document.getElementById("goldenTimeText");

    if (!goldenTimeEl) {
        goldenTimeEl = document.createElement("div");
        goldenTimeEl.id = "goldenTimeText";
        goldenTimeEl.className = "text-center mt-3 text-sm";
        wrapper.appendChild(goldenTimeEl);
    }

    goldenTimeEl.innerHTML = maxAvg > 0
        ? `💡 <span class="font-semibold text-emerald-600">Giờ vàng đăng bài:</span> 
        <span class="font-bold">${bestHourIndex}h - ${bestHourIndex + 1}h</span>
        <span class="text-slate-500">(Dựa trên ${filteredVideos.length} videos)</span>`
        : `<span class="text-slate-400">Chưa đủ dữ liệu để phân tích</span>`;
}

// 6. VIDEO TABLE
function renderVideoTable() {
    const tbody = document.getElementById('videoTableBody');
    const mobileList = document.getElementById('mobileVideoList');
    
    const totalPages = Math.ceil(filteredVideos.length / itemsPerPage);
    const start = (currentPage - 1) * itemsPerPage;
    const pageItems = filteredVideos.slice(start, start + itemsPerPage);

    // Empty State
    if (pageItems.length === 0) {
        const emptyHtml = `<tr><td colspan="8" class="p-8 text-center text-slate-500">Không có dữ liệu.</td></tr>`;
        tbody.innerHTML = emptyHtml;
        mobileList.innerHTML = `<div class="p-8 text-center text-slate-500">Không có dữ liệu.</div>`;
        document.getElementById('paginationControls').classList.add('hidden');
        return;
    }
    
    document.getElementById('paginationControls').classList.remove('hidden');

    // Render Table (Desktop)
    tbody.innerHTML = pageItems.map((v, index) => {
        const dateStr = moment(v.publishedAt).format('DD/MM/YYYY');
        const timeAgo = moment(v.publishedAt).fromNow();
        const durationStr = formatDuration(v.duration);
        const formatBadge = v.isShort 
            ? `<span class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-[10px] font-bold border border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800"><i class="fa-solid fa-bolt"></i> Shorts</span>` 
            : `<span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-[10px] font-bold border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">Video</span>`;
        const flag = getFlag(v.country);
        const tier = getTierInfo(v.country);
        const tagsString = v.tags ? v.tags.join(', ') : '';

        // Subs Logic
        let subsDisplayHtml = '';

        if (v.subs != null && v.subs !== undefined) {
            const subsText = formatNumberSmart(v.subs);
            subsDisplayHtml = `<i class="fa-solid fa-users text-[9px]"></i> ${subsText}`;
        } else {
            subsDisplayHtml = `<span class="text-slate-300 text-[10px] italic dark:text-slate-600">Hidden</span>`;
        }
        
        return `
        <tr class="hover:bg-slate-50 transition border-b border-slate-100 last:border-0 group dark:border-slate-700 dark:hover:bg-slate-700/50">
            <td class="p-4 font-mono text-xs text-slate-400 dark:text-slate-500">${start + index + 1}</td>
            <td class="p-4 max-w-[250px]">
                <div class="flex gap-3 items-start">
                    <div class="relative flex-shrink-0 group/thumb">
                        <img src="${v.thumbnail}" class="w-24 h-14 object-cover rounded-lg shadow-sm border border-slate-100 group-hover:scale-105 transition-transform dark:border-slate-600">
                        <span class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1 rounded font-mono">${durationStr}</span>
                    </div>
                    <div class="min-w-0">
                        <a href="https://youtu.be/${v.id}" target="_blank" class="font-bold text-sm text-slate-800 line-clamp-2 hover:text-red-600 transition break-words dark:text-slate-200 dark:hover:text-red-400" title="${v.title}">${v.title}</a>
                        ${v.tags && v.tags.length > 0 ? `<button onclick="navigator.clipboard.writeText('${tagsString}'); showToast('Đã copy tags!', 'success')" class="text-[10px] mt-1 text-slate-400 hover:text-indigo-600 flex items-center gap-1 transition-colors cursor-pointer dark:text-slate-500 dark:hover:text-indigo-400"><i class="fa-solid fa-tags"></i> Copy Tags</button>` : ''}
                    </div>
                </div>
            </td>
            <td class="p-4 text-right">
                <div class="flex flex-col items-end gap-1">
                    <div class="flex items-center gap-1.5" title="${v.country}"><span class="text-base">${flag}</span><span class="text-xs font-bold text-slate-700 dark:text-slate-300">${v.country}</span></div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold border ${tier.class}">${tier.label}</span>
                </div>
            </td>
            <td class="p-4 text-right">${formatBadge}</td>
            <td class="p-4 text-right"><div class="text-xs font-bold text-slate-700 dark:text-slate-300">${dateStr}</div><div class="text-[10px] text-slate-400 dark:text-slate-500">${timeAgo}</div></td>
            <td class="p-4 text-right font-mono text-sm text-slate-600 font-bold dark:text-slate-300">${v.views.toLocaleString()}</td>
            <td class="p-4 text-right">
                <div class="text-xs font-bold text-slate-700 truncate max-w-[100px] dark:text-slate-300" title="${v.channel}">${v.channel}</div>
                <div class="text-[10px] text-slate-500 dark:text-slate-400">${subsDisplayHtml}</div>
            </td>
            <td class="p-4 text-right font-bold text-green-600 text-sm dark:text-green-400">$${(v.views/1000*currentRpm).toFixed(2)}</td>
        </tr>`;
    }).join('');

    // Render Cards (Mobile)
    mobileList.innerHTML = pageItems.map((v, index) => {
        let subsTextMobile = (v.subs >= 1000) ? `${formatNumberSmart(v.subs)} Subs` : '';
        const revenue = (v.views / 1000) * currentRpm;
        const timeAgo = moment(v.publishedAt).fromNow();
        const durationStr = formatDuration(v.duration);
        const flag = getFlag(v.country);
        const tier = getTierInfo(v.country);

        return `
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm flex flex-col overflow-hidden dark:bg-slate-800 dark:border-slate-700">
            <!-- Full Width Thumbnail -->
            <div class="relative w-full aspect-video group">
                 <img src="${v.thumbnail}" class="w-full h-full object-cover">
                 <span class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] px-1.5 py-0.5 rounded font-mono font-bold">${durationStr}</span>
                 <div class="absolute top-2 left-2 flex gap-1">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/90 border ${tier.class} shadow-sm dark:bg-slate-900/90 dark:border-slate-600">${tier.label} ${flag}</span>
                 </div>
            </div>
            
            <div class="p-4 flex flex-col gap-3">
                <div class="flex-1 min-w-0">
                    <a href="https://youtu.be/${v.id}" target="_blank" class="font-bold text-sm text-slate-800 line-clamp-2 leading-snug mb-1 hover:text-red-600 transition dark:text-slate-200 dark:hover:text-red-400">${v.title}</a>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mt-1 dark:text-slate-400">
                        <span><i class="fa-solid fa-eye text-slate-400 dark:text-slate-500"></i> ${v.views.toLocaleString()}</span>
                        <span>• ${timeAgo}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs flex-shrink-0 dark:bg-slate-700 dark:text-slate-400">
                             <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="overflow-hidden min-w-0">
                            <div class="text-xs font-bold text-slate-700 truncate dark:text-slate-300">${v.channel}</div>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400">${subsTextMobile}</div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end justify-center">
                        <div class="text-xs font-bold text-green-600 dark:text-green-400">$${revenue.toFixed(2)}</div>
                         ${v.isShort 
                            ? `<span class="text-[9px] font-bold text-red-600 bg-red-50 px-1.5 rounded border border-red-100 mt-0.5 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800"><i class="fa-solid fa-bolt"></i> Shorts</span>`
                            : `<span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 rounded border border-blue-100 mt-0.5 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">Video</span>`
                        }
                    </div>
                </div>
            </div>
        </div>
        `;
    }).join('');

    renderPaginationControls(totalPages);
}

function renderPaginationControls(totalPages) {
    const container = document.getElementById('paginationBtns');
    document.getElementById('pageInfo').innerText = `Trang ${currentPage} / ${totalPages}`;
    let html = '';
    html += `<button onclick="changePage(${currentPage - 1})" class="pagination-btn ${currentPage === 1 ? 'disabled' : 'hover:bg-slate-100'}"><i class="fa-solid fa-chevron-left"></i></button>`;
    for(let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button onclick="changePage(${i})" class="pagination-btn ${i === currentPage ? 'active' : 'bg-white hover:bg-slate-50 border-slate-200'}">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) { html += `<span class="px-2 text-slate-400">...</span>`; }
    }
    html += `<button onclick="changePage(${currentPage + 1})" class="pagination-btn ${currentPage === totalPages ? 'disabled' : 'hover:bg-slate-100'}"><i class="fa-solid fa-chevron-right"></i></button>`;
    container.innerHTML = html;
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    renderVideoTable();
    document.querySelector('table').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function exportCSV() {
    if(filteredVideos.length === 0) return showToast('Không có dữ liệu!', 'error');
    const header = ["Title", "Channel", "Country", "Format", "Subscribers", "Published Date", "Views", "Revenue ($)", "Link"];
    const rows = filteredVideos.map(v => [ `"${v.title.replace(/"/g, '""')}"`, `"${v.channel.replace(/"/g, '""')}"`, v.country, v.isShort ? "Shorts" : "Video", v.subs, moment(v.publishedAt).format('YYYY-MM-DD'), v.views, ((v.views/1000)*currentRpm).toFixed(2), `"https://youtu.be/${v.id}"` ]);
    const csvContent = "data:text/csv;charset=utf-8,\uFEFF" + [header.join(","), ...rows.map(r => r.join(","))].join("\n");
    const link = document.createElement("a");
    link.href = encodeURI(csvContent);
    link.download = `YouTube_Analysis_${moment().format('YYYYMMDD_HHmm')}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}