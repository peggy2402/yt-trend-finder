/**
 * ZT Group Analytics Pro - Ultimate Version V0.5.3
 * Fixed Strategy Display & Deep Scan
 */
// --- SECURITY & PROTECTION (NEW) ---
(function() {
    // 1. Disable Right Click
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    // 2. Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
    document.addEventListener('keydown', function(e) {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+I (DevTools)
        if (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'i' || e.keyCode === 73)) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+J (Console)
        if (e.ctrlKey && e.shiftKey && (e.key === 'J' || e.key === 'j' || e.keyCode === 74)) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+C (Inspect Element)
        if (e.ctrlKey && e.shiftKey && (e.key === 'C' || e.key === 'c' || e.keyCode === 67)) {
            e.preventDefault();
            return false;
        }
        // Ctrl+U (View Source)
        if (e.ctrlKey && (e.key === 'U' || e.key === 'u' || e.keyCode === 85)) {
            e.preventDefault();
            return false;
        }
    });

    // 3. Detect DevTools Open (Optional - Advanced)
    // Cảnh báo nếu phát hiện cửa sổ console mở (dựa trên kích thước thay đổi đột ngột hoặc debugger)
    setInterval(function() {
        const widthThreshold = window.outerWidth - window.innerWidth > 160;
        const heightThreshold = window.outerHeight - window.innerHeight > 160;
        
        // Kiểm tra cơ bản dựa trên chênh lệch kích thước cửa sổ (thường xảy ra khi bật DevTools docked)
        // Hoặc kiểm tra các thuộc tính console đặc biệt (tùy trình duyệt)
        if ((widthThreshold || heightThreshold) && (window.firebug || (window.console && (window.console.firebug || window.console.exception)))) {
            showDevToolsWarning();
        }
        
        // Kiểm tra bổ sung bằng cách đo thời gian thực thi (DevTools làm chậm debugger)
        const start = new Date();
        debugger; // Nếu DevTools mở, nó sẽ dừng ở đây hoặc chạy chậm lại
        const end = new Date();
        if (end - start > 100) {
             showDevToolsWarning();
        }
    }, 1000);

    function showDevToolsWarning() {
        if (!document.getElementById('dev-warning')) {
            document.body.innerHTML = '';
            document.body.style.backgroundColor = '#000';
            document.body.style.color = 'red';
            document.body.style.display = 'flex';
            document.body.style.justifyContent = 'center';
            document.body.style.alignItems = 'center';
            document.body.style.height = '100vh';
            document.body.style.textAlign = 'center';
            document.body.style.fontFamily = 'Arial, sans-serif';
            document.body.innerHTML = `
                <div id="dev-warning">
                    <h1 style="font-size: 24px; margin-bottom: 20px;">PHÁT HIỆN NGƯỜI DÙNG ĐANG BẬT CHỨC NĂNG DEV TOOL.</h1>
                    <h2 style="font-size: 18px;">VUI LÒNG ĐÓNG ĐỂ TIẾP TỤC SỬ DỤNG</h2>
                </div>
            `;
        }
    }
})();
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
let currentPage = 1;
const itemsPerPage = 10;

// --- INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
    initTheme(); 
    loadSettings();
    renderRegions();
    updateKeyCountUI(); 
    
    if (apiKeys.length > 0) fetchRealNicheTrends();
    
    document.getElementById('searchInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') startAnalysis();
    });
});

// --- THEME ---
function initTheme() {
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

// --- API KEY UI ---
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

function saveApiKey() {
    const key = document.getElementById('apiKeyInput').value.trim();
    if (key && key.length > 10) {
        if (!apiKeys.includes(key)) {
            apiKeys.push(key);
            localStorage.setItem('yt_api_keys', JSON.stringify(apiKeys));
            updateKeyCountUI();
            showToast('API Key đã được lưu!', 'success');
            fetchRealNicheTrends();
        } else {
            showToast('Key này đã tồn tại!', 'warning');
        }
    } else {
        showToast('Key không hợp lệ!', 'error');
    }
}

// --- REGIONS WITH ICONS (REQ #5, #7) ---
function renderRegions() {
    const select = document.getElementById('regionSelect');
    const regions = [
        { code: 'US', name: '🇺🇸 United States' }, { code: 'VN', name: '🇻🇳 Vietnam' },
        { code: 'JP', name: '🇯🇵 Japan' }, { code: 'KR', name: '🇰🇷 Korea' },
        { code: 'GB', name: '🇬🇧 United Kingdom' }, { code: 'DE', name: '🇩🇪 Germany' },
        { code: 'FR', name: '🇫🇷 France' }, { code: 'IN', name: '🇮🇳 India' },
        { code: 'BR', name: '🇧🇷 Brazil' }, { code: 'RU', name: '🇷🇺 Russia' },
        { code: 'CA', name: '🇨🇦 Canada' }, { code: 'AU', name: '🇦🇺 Australia' },
        { code: 'ID', name: '🇮🇩 Indonesia' }, { code: 'TH', name: '🇹🇭 Thailand' },
        { code: 'PH', name: '🇵🇭 Philippines' }, { code: 'TW', name: '🇹🇼 Taiwan' },
        { code: 'SG', name: '🇸🇬 Singapore' }, { code: 'MY', name: '🇲🇾 Malaysia' }
    ];
    select.innerHTML = regions.map(r => `<option value="${r.code}">${r.name}</option>`).join('');
}

function getFlag(code) {
    const flags = {
        'US': '🇺🇸', 'VN': '🇻🇳', 'JP': '🇯🇵', 'KR': '🇰🇷', 'GB': '🇬🇧', 'DE': '🇩🇪',
        'FR': '🇫🇷', 'IN': '🇮🇳', 'BR': '🇧🇷', 'RU': '🇷🇺', 'CA': '🇨🇦', 'AU': '🇦🇺',
        'ID': '🇮🇩', 'TH': '🇹🇭', 'PH': '🇵🇭', 'TW': '🇹🇼', 'SG': '🇸🇬', 'MY': '🇲🇾'
    };
    return flags[code] || '🌐';
}

// --- ANALYSIS LOGIC ---
function getApiKey() {
    if (apiKeys.length === 0) return null;
    currentKeyIndex = (currentKeyIndex + 1) % apiKeys.length;
    return apiKeys[currentKeyIndex];
}

async function startAnalysis() {
    const query = document.getElementById('searchInput').value;
    const region = document.getElementById('regionSelect').value;
    const time = document.getElementById('timeFilter').value;
    const maxResults = parseInt(document.getElementById('maxResultsFilter').value) || 50;
    const key = getApiKey();

    if (!key) return showToast('Chưa có API Key!', 'error');
    if (!query) return showToast('Vui lòng nhập từ khóa!', 'warning');

    const btn = document.getElementById('analyzeBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang quét...';
    btn.disabled = true;
    
    // Reset Views
    document.getElementById('resultsBody').innerHTML = '<tr><td colspan="5" class="p-8 text-center"><div class="animate-pulse flex justify-center"><div class="h-4 bg-slate-200 dark:bg-slate-700 rounded w-1/2"></div></div></td></tr>';
    document.getElementById('strategySection').classList.add('hidden'); // Hide strategy initially

    try {
        let publishedAfter = new Date();
        // REQ #6: 1 Hour Filter logic
        if (time === 'hour') publishedAfter.setHours(publishedAfter.getHours() - 1);
        else if (time === 'today') publishedAfter.setDate(publishedAfter.getDate() - 1);
        else if (time === 'week') publishedAfter.setDate(publishedAfter.getDate() - 7);
        else if (time === 'month') publishedAfter.setDate(publishedAfter.getDate() - 30);
        else if (time === 'year') publishedAfter.setFullYear(publishedAfter.getFullYear() - 1);
        
        const rfc3339 = publishedAfter.toISOString();
        let allItems = [];
        let nextPageToken = '';
        let fetchedCount = 0;
        
        // LOOP DEEP SCAN (REQ #4)
        while (fetchedCount < maxResults) {
            const limit = Math.min(50, maxResults - fetchedCount);
            const searchUrl = `https://www.googleapis.com/youtube/v3/search?part=snippet&q=${encodeURIComponent(query)}&type=video&regionCode=${region}&publishedAfter=${rfc3339}&maxResults=${limit}&order=viewCount&key=${key}&pageToken=${nextPageToken}`;
            const searchRes = await fetch(searchUrl);
            const searchData = await searchRes.json();

            if (searchData.error) throw new Error(searchData.error.message);
            if (!searchData.items || searchData.items.length === 0) break;

            allItems = allItems.concat(searchData.items);
            fetchedCount += searchData.items.length;
            nextPageToken = searchData.nextPageToken;
            
            if (!nextPageToken) break;
        }

        if (allItems.length === 0) throw new Error("Không tìm thấy video nào.");

        // Get Details
        const videoIds = allItems.map(item => item.id.videoId).join(',');
        // Batching for stats (API limit is 50 ids)
        let allStats = [];
        for (let i = 0; i < allItems.length; i += 50) {
            const batchIds = allItems.slice(i, i + 50).map(item => item.id.videoId).join(',');
            const statsUrl = `https://www.googleapis.com/youtube/v3/videos?part=statistics,snippet,contentDetails&id=${batchIds}&key=${key}`;
            const res = await fetch(statsUrl);
            const data = await res.json();
            if (data.items) allStats = allStats.concat(data.items);
        }

        // Get Channels (Simplified)
        const channelIds = [...new Set(allStats.map(v => v.snippet.channelId))].slice(0, 50).join(',');
        const channelsUrl = `https://www.googleapis.com/youtube/v3/channels?part=statistics&id=${channelIds}&key=${key}`;
        const channelsRes = await fetch(channelsUrl);
        const channelsData = await channelsRes.json();
        const channelSubsMap = {};
        if (channelsData.items) channelsData.items.forEach(c => { channelSubsMap[c.id] = parseCount(c.statistics.subscriberCount); });

        // Map Data
        globalVideos = allStats.map(item => {
            const views = parseInt(item.statistics.viewCount) || 0;
            const subs = channelSubsMap[item.snippet.channelId] || 0;
            const publishedAt = new Date(item.snippet.publishedAt);
            const hoursAgo = Math.max(0.1, (new Date() - publishedAt) / (1000 * 60 * 60));
            const viewsPerHour = Math.round(views / hoursAgo);
            
            let score = 0;
            if (subs > 0) {
                const ratio = views / subs;
                score = Math.min(10, Math.round(ratio * 2)); 
            } else { score = views > 10000 ? 8 : 5; }

            return {
                id: item.id,
                title: item.snippet.title,
                channel: item.snippet.channelTitle,
                channelId: item.snippet.channelId,
                thumbnail: item.snippet.thumbnails.medium.url,
                publishedAt: item.snippet.publishedAt,
                views: views,
                viewsPerHour: viewsPerHour,
                subs: formatCompactNumber(subs),
                subsRaw: subs,
                duration: item.contentDetails.duration,
                score: score,
                country: region,
                tags: item.snippet.tags || []
            };
        });

        filteredVideos = globalVideos;
        currentPage = 1;
        updateStats();
        renderVideoTable();
        
        // --- TRIGGER STRATEGY DISPLAY (REQ #1, #2, #3) ---
        displayStrategySection(query, globalVideos);
        
        showToast(`Đã quét ${globalVideos.length} videos!`, 'success');

    } catch (error) {
        showToast('Lỗi: ' + error.message, 'error');
        document.getElementById('resultsBody').innerHTML = `<tr><td colspan="5" class="p-8 text-center text-red-500">Lỗi: ${error.message}</td></tr>`;
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// --- STRATEGY & INSIGHTS DISPLAY FUNCTION (NEW) ---
function displayStrategySection(query, videos) {
    if (!videos || videos.length === 0) return;

    const section = document.getElementById('strategySection');
    section.classList.remove('hidden');

    // 1. Calculate King Keyword (Most repeated in top 20 videos)
    const allTags = videos.slice(0, 20).flatMap(v => v.tags).filter(t => t.toLowerCase() !== query.toLowerCase());
    const tagFreq = {};
    let kingKw = "Không có";
    let maxFreq = 0;
    
    allTags.forEach(t => {
        const key = t.toLowerCase();
        tagFreq[key] = (tagFreq[key] || 0) + 1;
        if (tagFreq[key] > maxFreq) { maxFreq = tagFreq[key]; kingKw = t; }
    });
    
    // 2. Calculate Best Time (Mode of publish hour in top 20)
    const hourFreq = new Array(24).fill(0);
    videos.slice(0, 30).forEach(v => {
        const h = new Date(v.publishedAt).getHours();
        hourFreq[h]++;
    });
    const bestHour = hourFreq.indexOf(Math.max(...hourFreq));
    const timeString = `${bestHour}:00 - ${bestHour+1}:00`;

    // 3. Dominant Channels (Top 3 by views in list)
    const topChannels = Object.values(videos.reduce((acc, v) => {
        if (!acc[v.channelId] || v.views > acc[v.channelId].views) {
            acc[v.channelId] = { name: v.channel, views: v.views, subs: v.subs, id: v.channelId };
        }
        return acc;
    }, {})).sort((a,b) => b.views - a.views).slice(0, 3);

    // 4. Update UI
    document.getElementById('strategyKeywordBase').innerText = query;
    document.getElementById('strategyMicroNiche').innerText = `${query} + ${kingKw}`;
    document.getElementById('strategyCluster').innerHTML = `
        <ul class="list-disc pl-4">
            <li>Top 5 sai lầm khi làm ${query}</li>
            <li>Hướng dẫn ${query} cho người mới bắt đầu</li>
            <li>Sự thật về ${kingKw} trong ngách ${query}</li>
        </ul>
    `;
    document.getElementById('strategyKingKeyword').innerText = kingKw.toUpperCase();
    document.getElementById('strategyBestTime').innerText = timeString;
    document.getElementById('strategyCompetition').innerText = videos.length > 50 ? "Cao" : "Thấp";

    // 5. Render Top Channels Table
    const channelBody = document.getElementById('topChannelsBody');
    channelBody.innerHTML = topChannels.map(c => `
        <tr class="border-b border-gray-100 dark:border-white/5 last:border-0">
            <td class="p-3 font-medium text-gray-900 dark:text-white">${c.name}</td>
            <td class="p-3 text-right text-slate-500">${c.subs}</td>
            <td class="p-3 text-right font-bold text-indigo-500">${formatCompactNumber(c.views)}</td>
            <td class="p-3 text-center">
                <a href="https://youtube.com/channel/${c.id}" target="_blank" class="text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-1 rounded hover:underline">Xem</a>
            </td>
        </tr>
    `).join('');
    
    // Scroll to section
    section.scrollIntoView({ behavior: 'smooth' });
}

// --- RENDER TABLES & UTILS ---
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
        const flag = getFlag(video.country); // REQ #4: Country Icon
        
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
                                <i class="fa-solid fa-circle-check text-slate-400 dark:text-slate-600 text-[10px]"></i> ${video.channel} • ${video.subs} subs • ${flag}
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
    if (totalPages <= 1) { container.innerHTML = ''; return; }
    let html = `<button onclick="changePage(${currentPage - 1})" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-white disabled:opacity-50" ${currentPage === 1 ? 'disabled' : ''}><i class="fa-solid fa-chevron-left"></i></button>`;
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html += `<button onclick="changePage(${i})" class="w-8 h-8 rounded-lg ${currentPage === i ? 'bg-red-600 text-white font-bold' : 'bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-slate-500 dark:text-slate-400'}">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) { html += `<span class="text-slate-400 px-1">...</span>`; }
    }
    html += `<button onclick="changePage(${currentPage + 1})" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-white disabled:opacity-50" ${currentPage === totalPages ? 'disabled' : ''}><i class="fa-solid fa-chevron-right"></i></button>`;
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

    document.getElementById("statTotalVideos").innerText = filteredVideos.length;
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
    if (filteredVideos.length === 0) return showToast('Không có dữ liệu!', 'warning');
    const headers = ["Title", "Channel", "Subscribers", "Views", "Views/Hour", "Published", "Link", "Est Revenue", "Country"];
    const rows = filteredVideos.map(v => [ `"${v.title.replace(/"/g, '""')}"`, `"${v.channel}"`, v.subsRaw, v.views, v.viewsPerHour, moment(v.publishedAt).format('YYYY-MM-DD'), `https://youtu.be/${v.id}`, ((v.views/1000) * currentRpm).toFixed(2), v.country ]);
    const csvContent = [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);
    link.setAttribute("download", "ztgroup_analytics_export.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// --- FETCH REAL TRENDS FOR LEFT SIDEBAR ---
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
        const url = `https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics&chart=mostPopular&regionCode=${region}&maxResults=5&key=${key}`;
        const response = await fetch(url);
        const data = await response.json();

        if (data.error) throw new Error(data.error.message);

        const trends = data.items.map(item => {
            const views = parseInt(item.statistics.viewCount);
            let comp = "High";
            if (views < 100000) comp = "Low"; else if (views < 1000000) comp = "Med";
            return {
                keyword: item.snippet.title,
                channel: item.snippet.channelTitle,
                volume: formatCompactNumber(views),
                competition: comp,
                tags: item.snippet.tags ? item.snippet.tags : [],
                publishedAt: item.snippet.publishedAt
            };
        });

        const listContainer = document.getElementById('nicheKeywordsList');
        listContainer.innerHTML = trends.map((item, index) => {
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
            </div>`;
        }).join('');

    } catch (error) {
        list.innerHTML = `<div class="text-center text-red-400 text-xs py-2">Lỗi: ${error.message}</div>`;
    }
}

// Modal functions for left sidebar trends
function openNicheModal(title, channel, volume, comp, tagsStr, publishedAt) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalTopChannel').innerText = channel;
    document.getElementById('modalVolume').innerText = volume;
    document.getElementById('modalKeyVideo').innerText = title;
    
    // Fill tags for modal (simplified)
    const tags = tagsStr ? tagsStr.split(',').filter(t => t.trim()) : [];
    document.getElementById('modalChannelList').innerHTML = tags.slice(0,5).map(t => `<span class="px-2 py-1 bg-gray-100 dark:bg-slate-700 rounded text-xs">${t}</span>`).join('');
    
    document.getElementById('nicheModal').classList.remove('hidden');
}
function closeNicheModal() { document.getElementById('nicheModal').classList.add('hidden'); }
function startAnalysisFromNiche() { closeNicheModal(); startAnalysis(); }

// --- HELPERS ---
function loadSettings() {
    const savedRpm = localStorage.getItem('yt_rpm');
    if (savedRpm) {
        currentRpm = parseFloat(savedRpm);
        document.getElementById('rpmSlider').value = currentRpm;
        document.getElementById('rpmValue').innerText = '$' + currentRpm;
    }
    if (apiKeys.length > 0) document.getElementById('apiKeyList').value = apiKeys.join('\n');
}
function toggleSettingsModal() {
    const modal = document.getElementById('settingsModal');
    if (modal.classList.contains('active')) { modal.classList.remove('active'); modal.classList.add('invisible', 'opacity-0'); }
    else { modal.classList.remove('invisible', 'opacity-0'); modal.classList.add('active'); }
}
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    let colors = 'bg-slate-800 text-white border-slate-700';
    if (type === 'success') colors = 'bg-green-600 text-white border-green-500';
    else if (type === 'error') colors = 'bg-red-600 text-white border-red-500';
    else if (type === 'warning') colors = 'bg-yellow-600 text-white border-yellow-500';
    toast.className = `flex items-center gap-3 px-4 py-3 rounded-lg shadow-xl border ${colors} mb-3 animate__animated animate__fadeInRight`;
    toast.innerHTML = `<span class="text-sm font-medium">${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.remove('animate__fadeInRight'); toast.classList.add('animate__fadeOutRight'); setTimeout(() => toast.remove(), 500); }, 3000);
}
function parseCount(str) { return parseInt(str) || 0; }
function formatCompactNumber(num) { return Intl.NumberFormat('en-US', { notation: "compact", maximumFractionDigits: 1 }).format(num); }
function formatDuration(iso) {
    if (!iso) return "00:00";
    const match = iso.match(/PT(\d+H)?(\d+M)?(\d+S)?/);
    if (!match) return "00:00";
    const h = (parseInt(match[1]) || 0);
    const m = (parseInt(match[2]) || 0);
    const s = (parseInt(match[3]) || 0);
    let r = "";
    if (h > 0) r += h + ":";
    r += (m < 10 && h > 0 ? "0" : "") + m + ":";
    r += (s < 10 ? "0" : "") + s;
    return r;
}