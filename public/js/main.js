/**
 * NDGroup Analytics Pro - Ultimate Version V3.8 (Added Niche Trends)
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

// --- NICHE TRENDS VARIABLES (NEW) ---
// const nicheKeywordsData = [
//     { keyword: "Silent Vlog", growth: "+125%", volume: "High", competition: "Low", channels: ["Sueddu", "Hamimommy", "Nao"] },
//     { keyword: "AI Music Video", growth: "+89%", volume: "Med", competition: "Med", channels: ["Kaiber", "RunwayML", "Pika"] },
//     { keyword: "ASMR Cleaning", growth: "+64%", volume: "High", competition: "High", channels: ["CleanWithMe", "Honeyjubu", "Aurikatariina"] },
//     { keyword: "Faceless Cash Cow", growth: "+42%", volume: "Med", competition: "Low", channels: ["10X Income", "CashCowMakers", "TubeMastery"] },
//     { keyword: "Retro Gaming Tech", growth: "+38%", volume: "Low", competition: "Low", channels: ["LGR", "8-Bit Guy", "Modern Vintage Gamer"] },
//     { keyword: "Study with me pomodoro", growth: "+22%", volume: "High", competition: "High", channels: ["Abao", "Merve", "The Sherry Formula"] },
//     { keyword: "Home Cafe", growth: "+156%", volume: "High", competition: "Med", channels: ["Hanbit", "Y.na Homecafe", "Café Vlog"] },
//     { keyword: "Coding ASMR", growth: "+45%", volume: "Low", competition: "Low", channels: ["Ben Awad", "Traversy Media", "CodeAesthetic"] }
// ];

// --- INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
    initTheme(); // Fix: Load Dark Mode immediately
    loadSettings();
    renderRegions();
    updateKeyCountUI(); // Fix: Show badge count
    
    // Auto load Trends if keys exist
    if (apiKeys.length > 0) {
        fetchRealNicheTrends();
    }
    
    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') startAnalysis();
    });
});
// --- REAL TRENDS LOGIC (UPDATED API V3) ---
async function fetchRealNicheTrends() {
    const list = document.getElementById('nicheKeywordsList');
    const region = document.getElementById('regionSelect').value || 'US';
    const key = getApiKey();

    if (!key) {
        list.innerHTML = '<div class="text-center py-4 text-slate-500 text-xs italic">Cần API Key để hiển thị</div>';
        return;
    }

    list.innerHTML = `
        <div class="animate-pulse space-y-3">
            <div class="h-10 bg-gray-200 dark:bg-white/5 rounded-xl"></div>
            <div class="h-10 bg-gray-200 dark:bg-white/5 rounded-xl"></div>
            <div class="h-10 bg-gray-200 dark:bg-white/5 rounded-xl"></div>
        </div>
    `;

    try {
        // Fetch Top Trending Videos
        const url = `https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&chart=mostPopular&regionCode=${region}&maxResults=5&key=${key}`;
        const response = await fetch(url);
        const data = await response.json();

        if (data.error) throw new Error(data.error.message);

        const trends = data.items.map(item => {
            const views = parseInt(item.statistics.viewCount);
            // Estimate competition: Higher views on trending = usually High Comp, but opportunities exist in 'rising' stars
            let comp = "High";
            if (views < 100000) comp = "Low";
            else if (views < 1000000) comp = "Med";

            return {
                id: item.id,
                keyword: item.snippet.title,
                channel: item.snippet.channelTitle,
                volume: formatCompactNumber(views),
                competition: comp,
                tags: item.snippet.tags ? item.snippet.tags : [], // Pass full array
                publishedAt: item.snippet.publishedAt
            };
        });

        renderRealTrends(trends);

    } catch (error) {
        console.error("Trend API Error:", error);
        list.innerHTML = `<div class="text-center text-red-400 text-xs py-2">Lỗi: ${error.message}</div>`;
    }
}

function renderRealTrends(trends) {
    const list = document.getElementById('nicheKeywordsList');
    list.innerHTML = trends.map((item, index) => {
        // Prepare data for onclick (escape quotes)
        const safeTitle = item.keyword.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        const safeChannel = item.channel.replace(/'/g, "\\'");
        const tagsStr = item.tags.join(',');
        
        return `
        <div onclick="openNicheModal('${safeTitle}', '${safeChannel}', '${item.volume}', '${item.competition}', '${tagsStr}', '${item.publishedAt}')" class="group flex items-center justify-between p-3 rounded-xl bg-white dark:bg-white/5 hover:bg-gray-50 dark:hover:bg-white/10 border border-gray-100 dark:border-white/5 hover:border-gray-200 dark:hover:border-white/10 cursor-pointer transition-all shadow-sm">
            <div class="flex items-center gap-3 overflow-hidden">
                <span class="text-lg font-bold text-slate-400 dark:text-slate-600 group-hover:text-red-500 transition-colors">#${index + 1}</span>
                <div class="min-w-0">
                    <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors truncate" title="${item.keyword}">${item.keyword}</div>
                    <div class="text-[10px] text-slate-500 flex gap-2">
                        <span><i class="fa-solid fa-eye text-slate-400"></i> ${item.volume}</span>
                        <span><i class="fa-solid fa-user text-slate-400"></i> ${item.channel}</span>
                    </div>
                </div>
            </div>
            <div class="text-right flex-shrink-0 ml-2">
                <div class="text-[10px] font-bold ${item.competition === 'Low' ? 'text-green-500 bg-green-100 dark:bg-green-500/10' : 'text-orange-500 bg-orange-100 dark:bg-orange-500/10'} px-1.5 py-0.5 rounded">
                    ${item.competition}
                </div>
            </div>
        </div>
    `}).join('');
}

// --- THEME MANAGEMENT (FIXED) ---
function initTheme() {
    // Check localStorage or System preference
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
        document.getElementById('themeIcon').className = 'fa-solid fa-sun text-yellow-400';
    } else {
        document.documentElement.classList.remove('dark');
        document.getElementById('themeIcon').className = 'fa-solid fa-moon text-slate-600';
    }
}

function toggleTheme() {
    if (document.documentElement.classList.contains('dark')) {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
        document.getElementById('themeIcon').className = 'fa-solid fa-moon text-slate-600';
    } else {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
        document.getElementById('themeIcon').className = 'fa-solid fa-sun text-yellow-400';
    }
}

// --- API KEY MANAGEMENT & BADGE (FIXED) ---
function updateKeyCountUI() {
    const badge = document.getElementById('activeKeyCountBadge');
    const validKeys = apiKeys.filter(k => k.trim().length > 10);
    
    if (validKeys.length > 0) {
        badge.innerText = validKeys.length;
        badge.classList.remove('hidden');
        document.getElementById('apiKeyStatus').innerText = `${validKeys.length} keys sẵn sàng`;
        document.getElementById('apiKeyStatus').className = 'text-[10px] text-green-500 font-bold';
    } else {
        badge.classList.add('hidden');
        document.getElementById('apiKeyStatus').innerText = `Chưa nhập key`;
        document.getElementById('apiKeyStatus').className = 'text-[10px] text-slate-500';
    }
}

// --- TOGGLE API KEY VISIBILITY ---
function toggleApiKeyVisibility() {
    const input = document.getElementById('apiKeyInput');
    const icon = document.getElementById('apiKeyToggleIcon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// --- EXTENDED REGIONS & TIME ---
function renderRegions() {
    const select = document.getElementById('regionSelect');
    const regions = [
        { code: 'US', name: '🇺🇸 United States' },
        { code: 'VN', name: '🇻🇳 Vietnam' },
        { code: 'JP', name: '🇯🇵 Japan' },
        { code: 'KR', name: '🇰🇷 Korea' },
        { code: 'GB', name: '🇬🇧 United Kingdom' },
        { code: 'CA', name: '🇨🇦 Canada' },
        { code: 'AU', name: '🇦🇺 Australia' },
        { code: 'DE', name: '🇩🇪 Germany' },
        { code: 'FR', name: '🇫🇷 France' },
        { code: 'IN', name: '🇮🇳 India' },
        { code: 'BR', name: '🇧🇷 Brazil' },
        { code: 'RU', name: '🇷🇺 Russia' },
        { code: 'TH', name: '🇹🇭 Thailand' },
        { code: 'ID', name: '🇮🇩 Indonesia' },
        { code: 'PH', name: '🇵🇭 Philippines' },
        { code: 'TW', name: '🇹🇼 Taiwan' }
    ];

    // Keep existing options if any, or rebuild
    select.innerHTML = regions.map(r => `<option value="${r.code}">${r.name}</option>`).join('');
    
    // Update Time Filters manually in HTML or here if needed (Code below assumes HTML update or dynamic injection)
    const timeSelect = document.getElementById('timeFilter');
    if (timeSelect.options.length <= 3) {
        timeSelect.innerHTML = `
            <option value="now">⚡ 24 giờ qua</option>
            <option value="week">📅 7 ngày qua</option>
            <option value="month">🗓 30 ngày qua</option>
            <option value="year">📆 1 năm qua</option>
            <option value="all">∞ Mọi lúc</option>
        `;
    }
}

// --- NICHE TRENDS FUNCTIONS (NEW) ---
function renderNicheKeywords() {
    const list = document.getElementById('nicheKeywordsList');
    if (!list) return;
    
    // Randomize slightly for effect
    const displayData = [...nicheKeywordsData].sort(() => 0.5 - Math.random()).slice(0, 5);

    list.innerHTML = displayData.map((item, index) => `
        <div onclick="openNicheModal('${item.keyword}', '${item.channels[0]}', '${item.volume}', '${item.competition}', '${item.channels.join(',')}')" class="group flex items-center justify-between p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 hover:border-white/10 cursor-pointer transition-all">
            <div class="flex items-center gap-3">
                <span class="text-lg font-bold text-slate-600 group-hover:text-red-500 transition-colors">#${index + 1}</span>
                <div>
                    <div class="text-sm font-medium text-white group-hover:text-red-400 transition-colors">${item.keyword}</div>
                    <div class="text-[10px] text-slate-500 flex gap-2">
                        <span><i class="fa-solid fa-signal text-slate-600"></i> ${item.volume} Vol</span>
                        <span><i class="fa-solid fa-shield-halved text-slate-600"></i> ${item.competition} Comp</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold text-green-400 bg-green-400/10 px-1.5 py-0.5 rounded border border-green-400/20">${item.growth}</div>
            </div>
        </div>
    `).join('');
}

function refreshNicheKeywords() {
    const list = document.getElementById('nicheKeywordsList');
    if (!list) return;
    
    list.innerHTML = `
        <div class="animate-pulse space-y-3">
            <div class="h-12 bg-white/5 rounded-xl"></div>
            <div class="h-12 bg-white/5 rounded-xl"></div>
            <div class="h-12 bg-white/5 rounded-xl"></div>
        </div>
    `;
    setTimeout(() => {
        renderNicheKeywords();
    }, 800);
}
// --- STRATEGY GENERATOR (THE BRAIN) ---
function generateStrategyInsights(title, channel, tags, publishedAt) {
    // 1. Micro-Niche: Combine Title Keyword + Main Category Tag
    const cleanTitle = title.replace(/[^\w\s]/gi, '').split(' ');
    const mainTopic = cleanTitle.length > 2 ? cleanTitle.slice(0, 2).join(' ') : title;
    let microNiche = mainTopic;
    if (tags.length > 0) {
        // Find a tag that is NOT the same as words in title
        const uniqueTag = tags.find(t => !title.toLowerCase().includes(t.toLowerCase()));
        if (uniqueTag) microNiche = `${mainTopic} for ${uniqueTag}`;
        else microNiche = `${mainTopic} Lovers`;
    }
    document.getElementById('strategyMicroNiche').innerText = microNiche;

    // 2. Cluster Content: Suggest patterns
    const patterns = [
        `Tại sao ${mainTopic} lại hot?`,
        `Top 5 sai lầm khi làm ${mainTopic}`,
        `Hướng dẫn ${mainTopic} cho người mới`,
        `Reaction: ${title.substring(0, 20)}...`
    ];
    document.getElementById('strategyCluster').innerHTML = `
        <ul class="list-disc pl-4 space-y-1 text-slate-600 dark:text-slate-300">
            <li>${patterns[0]}</li>
            <li>${patterns[1]}</li>
        </ul>
    `;

    // 3. Golden Time: Parse publishedAt
    const pubDate = new Date(publishedAt);
    const hour = pubDate.getHours();
    let timeSlot = `${hour}:00 - ${hour+1}:00`;
    if (hour >= 6 && hour < 12) timeSlot += " (Sáng)";
    else if (hour >= 12 && hour < 18) timeSlot += " (Chiều)";
    else timeSlot += " (Tối)";
    document.getElementById('strategyBestTime').innerText = timeSlot;

    // 4. King Keyword: Most repeated word in tags OR first tag
    let kingKw = tags.length > 0 ? tags[0] : mainTopic;
    // Simple frequency check
    if (tags.length > 5) {
        const allWords = tags.join(' ').toLowerCase().split(' ');
        const frequency = {};
        let maxFreq = 0;
        allWords.forEach(w => {
            if (w.length > 3) {
                frequency[w] = (frequency[w] || 0) + 1;
                if (frequency[w] > maxFreq) { maxFreq = frequency[w]; kingKw = w; }
            }
        });
    }
    document.getElementById('strategyKingKeyword').innerText = kingKw.toUpperCase();

    // 5. Dominant Channel
    document.getElementById('modalTopChannel').innerText = channel;
}

function openNicheModal(title, channel, volume, comp, tagsStr, publishedAt) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalVolume').innerText = volume;
    document.getElementById('modalCompetition').innerText = comp;
    document.getElementById('modalKeyVideo').innerText = title;
    
    // Render Tags
    const tags = tagsStr ? tagsStr.split(',').filter(t => t.trim() !== '') : [];
    const container = document.getElementById('modalChannelList');
    
    if (tags.length > 0) {
        container.innerHTML = tags.slice(0, 10).map(tag => 
            `<span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 rounded text-xs text-gray-700 dark:text-white border border-gray-200 dark:border-slate-600 cursor-pointer hover:bg-red-50 dark:hover:bg-red-900/30" onclick="searchTag('${tag}')">${tag}</span>`
        ).join('');
    } else {
        container.innerHTML = '<span class="text-xs text-slate-500 italic">Không tìm thấy tags (Kênh ẩn tags)</span>';
    }

    // Run Strategy Brain
    generateStrategyInsights(title, channel, tags, publishedAt);

    document.getElementById('nicheModal').classList.remove('hidden');
}
function searchTag(tag) {
    document.getElementById('searchInput').value = tag;
    closeNicheModal();
    startAnalysis();
}
function closeNicheModal() {
    document.getElementById('nicheModal').classList.add('hidden');
}

function startAnalysisFromNiche() {
    const keyword = document.getElementById('strategyMicroNiche').innerText;
    document.getElementById('searchInput').value = keyword;
    closeNicheModal();
    startAnalysis();
}

// --- STANDARD FUNCTIONS (Existing logic preserved) ---
function getApiKey() {
    if (apiKeys.length === 0) return null;
    currentKeyIndex = (currentKeyIndex + 1) % apiKeys.length;
    return apiKeys[currentKeyIndex];
}

function saveApiKey() {
    const key = document.getElementById('apiKeyInput').value.trim();
    if (key && key.length > 10) {
        if (!apiKeys.includes(key)) {
            apiKeys.push(key);
            localStorage.setItem('yt_api_keys', JSON.stringify(apiKeys));
            updateKeyCountUI();
            showToast('API Key đã được lưu thành công!', 'success');
            fetchRealNicheTrends(); // Auto refresh trends
        } else {
            showToast('Key này đã tồn tại trong danh sách!', 'warning');
        }
    } else {
        showToast('Vui lòng nhập API Key hợp lệ!', 'error');
    }
}

function loadSettings() {
    const savedRpm = localStorage.getItem('yt_rpm');
    if (savedRpm) {
        currentRpm = parseFloat(savedRpm);
        document.getElementById('rpmSlider').value = currentRpm;
        document.getElementById('rpmValue').innerText = '$' + currentRpm;
    }
    if (apiKeys.length > 0) {
        document.getElementById('apiKeyList').value = apiKeys.join('\n');
    }
}

function toggleSettingsModal() {
    const modal = document.getElementById('settingsModal');
    if (modal.classList.contains('active')) {
        modal.classList.remove('active');
        modal.classList.add('invisible', 'opacity-0');
    } else {
        modal.classList.remove('invisible', 'opacity-0');
        modal.classList.add('active');
    }
}

function saveSettings() {
    const rpm = parseFloat(document.getElementById('rpmSlider').value);
    currentRpm = rpm;
    localStorage.setItem('yt_rpm', rpm);
    
    const keysText = document.getElementById('apiKeyList').value;
    apiKeys = keysText.split('\n').map(k => k.trim()).filter(k => k.length > 10);
    localStorage.setItem('yt_api_keys', JSON.stringify(apiKeys));
    
    updateKeyCountUI();
    toggleSettingsModal();
    showToast('Cài đặt đã được lưu!', 'success');
    if (apiKeys.length > 0) fetchRealNicheTrends();
}

// Slider event
document.getElementById('rpmSlider').addEventListener('input', function(e) {
    document.getElementById('rpmValue').innerText = '$' + e.target.value;
});

// --- API & ANALYSIS ---

async function startAnalysis() {
    const query = document.getElementById('searchInput').value;
    const region = document.getElementById('regionSelect').value;
    const time = document.getElementById('timeFilter').value;
    const key = getApiKey();

    if (!key) return showToast('Chưa có API Key! Hãy nhập ở mục cài đặt.', 'error');
    if (!query) return showToast('Vui lòng nhập từ khóa!', 'warning');

    const btn = document.getElementById('analyzeBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang quét...';
    btn.disabled = true;
    document.getElementById('resultsBody').innerHTML = '<tr><td colspan="5" class="p-8 text-center"><div class="animate-pulse flex justify-center"><div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-1/2"></div></div></td></tr>';

    try {
        let publishedAfter = new Date();
        if (time === 'now') publishedAfter.setDate(publishedAfter.getDate() - 1);
        else if (time === 'week') publishedAfter.setDate(publishedAfter.getDate() - 7);
        else if (time === 'month') publishedAfter.setDate(publishedAfter.getDate() - 30);
        const rfc3339 = publishedAfter.toISOString();

        // 1. Search
        const searchUrl = `https://www.googleapis.com/youtube/v3/search?part=snippet&q=${encodeURIComponent(query)}&type=video&regionCode=${region}&publishedAfter=${rfc3339}&maxResults=50&order=viewCount&key=${key}`;
        const searchRes = await fetch(searchUrl);
        const searchData = await searchRes.json();
        if (searchData.error) throw new Error(searchData.error.message);
        const videoIds = searchData.items.map(item => item.id.videoId).join(',');
        
        // 2. Stats
        const statsUrl = `https://www.googleapis.com/youtube/v3/videos?part=statistics,snippet,contentDetails&id=${videoIds}&key=${key}`;
        const statsRes = await fetch(statsUrl);
        const statsData = await statsRes.json();

        // 3. Channels
        const channelIds = [...new Set(statsData.items.map(v => v.snippet.channelId))].join(',');
        const channelsUrl = `https://www.googleapis.com/youtube/v3/channels?part=statistics&id=${channelIds}&key=${key}`;
        const channelsRes = await fetch(channelsUrl);
        const channelsData = await channelsRes.json();
        const channelSubsMap = {};
        channelsData.items.forEach(c => { channelSubsMap[c.id] = parseCount(c.statistics.subscriberCount); });

        globalVideos = statsData.items.map(item => {
            const views = parseInt(item.statistics.viewCount) || 0;
            const subs = channelSubsMap[item.snippet.channelId] || 0;
            const publishedAt = new Date(item.snippet.publishedAt);
            const hoursAgo = Math.max(0.1, (new Date() - publishedAt) / (1000 * 60 * 60));
            const viewsPerHour = Math.round(views / hoursAgo);
            
            return {
                id: item.id,
                title: item.snippet.title,
                channel: item.snippet.channelTitle,
                thumbnail: item.snippet.thumbnails.medium.url,
                publishedAt: item.snippet.publishedAt,
                views: views,
                viewsPerHour: viewsPerHour,
                subs: formatCompactNumber(subs),
                subsRaw: subs,
                duration: item.contentDetails.duration
            };
        });

        filteredVideos = globalVideos;
        currentPage = 1;
        updateStats();
        renderVideoTable();
        showToast(`Tìm thấy ${globalVideos.length} video!`, 'success');
    } catch (error) {
        showToast('Lỗi: ' + error.message, 'error');
        document.getElementById('resultsBody').innerHTML = `<tr><td colspan="5" class="p-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>`;
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// --- RENDERING & UTILS ---

function renderVideoTable() {
    const container = document.getElementById('resultsBody');
    container.innerHTML = '';
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredVideos.slice(start, end);

    if (pageData.length === 0) { container.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-slate-500">Không có dữ liệu</td></tr>'; return; }

    pageData.forEach(video => {
        const timeAgo = moment(video.publishedAt).fromNow();
        const revenue = ((video.views / 1000) * currentRpm).toFixed(2);
        
        const row = `
            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group border-b border-gray-100 dark:border-white/5 last:border-0">
                <td class="p-4">
                    <div class="flex gap-3">
                        <div class="w-24 h-14 rounded-lg bg-gray-200 dark:bg-slate-700 flex-shrink-0 overflow-hidden relative group-hover:ring-2 ring-red-500/50 transition-all cursor-pointer" onclick="window.open('https://youtu.be/${video.id}', '_blank')">
                            <img src="${video.thumbnail}" class="w-full h-full object-cover">
                            <div class="absolute bottom-1 right-1 bg-black/80 text-[10px] text-white px-1 rounded font-mono">${formatDuration(video.duration)}</div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors cursor-pointer" onclick="window.open('https://youtu.be/${video.id}', '_blank')">${video.title}</div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-slate-400 dark:text-slate-600 text-[10px]"></i> ${video.channel} • ${video.subs} subs
                            </div>
                        </div>
                    </div>
                </td>
                <td class="p-4 text-right">
                    <div class="font-bold text-gray-900 dark:text-white">${formatCompactNumber(video.views)}</div>
                    <div class="text-xs text-green-600 dark:text-green-400 font-medium">+${formatCompactNumber(video.viewsPerHour)}/h</div>
                </td>
                <td class="p-4 text-right">
                    <div class="font-bold text-yellow-600 dark:text-yellow-400">$${revenue}</div>
                    <div class="text-xs text-slate-500">Est.</div>
                </td>
                <td class="p-4 text-right text-slate-500 dark:text-slate-400 text-xs">${timeAgo}</td>
                <td class="p-4 text-center">
                    <button onclick="window.open('https://youtu.be/${video.id}', '_blank')" class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-white/5 hover:bg-red-500 hover:text-white dark:hover:bg-red-500 text-slate-500 dark:text-slate-400 transition-all">
                        <i class="fa-brands fa-youtube"></i>
                    </button>
                </td>
            </tr>
        `;
        container.insertAdjacentHTML('beforeend', row);
    });
    renderPagination();
}

function renderPagination() {
    const container = document.getElementById('pagination');
    const totalPages = Math.ceil(filteredVideos.length / itemsPerPage);
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = `<button onclick="changePage(${currentPage - 1})" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-white disabled:opacity-50" ${currentPage === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>`;
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button onclick="changePage(${i})" class="w-8 h-8 rounded-lg ${currentPage === i ? 'bg-red-600 text-white font-bold' : 'bg-white/5 hover:bg-white/10 text-slate-400'}">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="text-slate-600 px-1">...</span>`;
        }
    }

    html += `<button onclick="changePage(${currentPage + 1})" class="w-8 h-8 rounded-lg bg-white/5 hover:bg-white/10 text-white disabled:opacity-50" ${currentPage === totalPages ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button>`;
    
    container.innerHTML = html;
}

function changePage(page) {
    if (page < 1 || page > Math.ceil(filteredVideos.length / itemsPerPage)) return;
    currentPage = page;
    renderVideoTable();
}

function updateStats() {
    const totalViews = filteredVideos.reduce((acc, curr) => acc + curr.views, 0);
    const avgViews = filteredVideos.length ? Math.round(totalViews / filteredVideos.length) : 0;
    const avgScore = filteredVideos.length ? (filteredVideos.reduce((acc, curr) => acc + curr.score, 0) / filteredVideos.length).toFixed(1) : 0;
    const totalEstRev = ((totalViews / 1000) * currentRpm).toFixed(0);

    animateValue("statTotalVideos", 0, filteredVideos.length, 1000);
    document.getElementById("statAvgViews").innerText = formatCompactNumber(avgViews);
    document.getElementById("statOpportunity").innerText = avgScore + "/10";
    document.getElementById("statEstRevenue").innerText = "$" + formatCompactNumber(totalEstRev);
}

function sortVideos(criteria) {
    if (filteredVideos.length === 0) return;
    switch (criteria) {
        case 'views_desc': filteredVideos.sort((a, b) => b.views - a.views); break;
        case 'views_asc': filteredVideos.sort((a, b) => a.views - b.views); break;
        case 'date_desc': filteredVideos.sort((a, b) => new Date(b.publishedAt) - new Date(a.publishedAt)); break;
        case 'subs_asc': filteredVideos.sort((a, b) => a.subsRaw - b.subsRaw); break;
    }
    currentPage = 1;
    renderVideoTable();
}

function exportCSV() {
    if (filteredVideos.length === 0) return showToast('Không có dữ liệu để xuất!', 'warning');
    
    const headers = ["Title", "Channel", "Subscribers", "Views", "Views/Hour", "Published", "Link", "Est Revenue"];
    const rows = filteredVideos.map(v => [
        `"${v.title.replace(/"/g, '""')}"`,
        `"${v.channel}"`,
        v.subsRaw,
        v.views,
        v.viewsPerHour,
        moment(v.publishedAt).format('YYYY-MM-DD'),
        `https://youtu.be/${v.id}`,
        ((v.views/1000) * currentRpm).toFixed(2)
    ]);

    const csvContent = [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", "ztgroup_analytics_export.csv");
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// --- UTILS ---

function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    let colors = 'bg-slate-800 text-white border-slate-700';
    let icon = '<i class="fa-solid fa-info-circle"></i>';
    
    if (type === 'success') {
        colors = 'bg-green-900/90 text-green-100 border-green-700';
        icon = '<i class="fa-solid fa-check-circle text-green-400"></i>';
    } else if (type === 'error') {
        colors = 'bg-red-900/90 text-red-100 border-red-700';
        icon = '<i class="fa-solid fa-triangle-exclamation text-red-400"></i>';
    } else if (type === 'warning') {
        colors = 'bg-yellow-900/90 text-yellow-100 border-yellow-700';
        icon = '<i class="fa-solid fa-exclamation-circle text-yellow-400"></i>';
    }

    toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl border ${colors} mb-3 animate__animated animate__fadeInRight`;
    toast.innerHTML = `${icon} <span class="text-sm font-medium">${message}</span>`;
    
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('animate__fadeInRight');
        toast.classList.add('animate__fadeOutRight');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

function parseCount(str) { return parseInt(str) || 0; }

function formatCompactNumber(number) {
    return Intl.NumberFormat('en-US', {
        notation: "compact",
        maximumFractionDigits: 1
    }).format(number);
}

function formatDuration(isoDuration) {
    if (!isoDuration) return "00:00";
    const match = isoDuration.match(/PT(\d+H)?(\d+M)?(\d+S)?/);
    if (!match) return "00:00";
    
    const hours = (parseInt(match[1]) || 0);
    const minutes = (parseInt(match[2]) || 0);
    const seconds = (parseInt(match[3]) || 0);
    
    let result = "";
    if (hours > 0) result += hours + ":";
    result += (minutes < 10 && hours > 0 ? "0" : "") + minutes + ":";
    result += (seconds < 10 ? "0" : "") + seconds;
    return result;
}

function animateValue(id, start, end, duration) {
    if (start === end) return;
    const range = end - start;
    let current = start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    const obj = document.getElementById(id);
    if (!obj) return;
    
    const timer = setInterval(function() {
        current += increment;
        obj.innerHTML = formatCompactNumber(current);
        if (current == end) {
            clearInterval(timer);
        }
    }, Math.max(stepTime, 50)); // Cap min speed
}