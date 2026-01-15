/**
 * LAYER 4: PRESENTATION - RENDERER (MOBILE RESPONSIVE UPDATE)
 * Cập nhật: Tối ưu hiển thị bảng trên Mobile (Responsive Tables)
 */
const Renderer = (() => {
    let _intelligence = null; 

    function renderRegions(regions) {
        const select = document.getElementById('regionSelect');
        if (!select) return;
        const saved = localStorage.getItem(AppConfig.STORAGE_KEYS.REGION) || 'VN';
        
        select.innerHTML = regions.map(r => 
            `<option value="${r.code}" ${r.code === saved ? 'selected' : ''}>${r.flag} ${r.name}</option>`
        ).join('');
    }
    // --- 1. RENDER MAIN TABLE (Responsive Card/Table Hybrid) ---
    function renderTable(videos, rpm, startIndex = 0) {
        const tbody = document.getElementById('resultsBody');
        
        // Empty State
        if (!videos || videos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="p-12 text-center text-slate-400 italic">Không tìm thấy dữ liệu trên trang này.</td></tr>';
            return;
        }

        tbody.innerHTML = videos.map((v, index) => {
            const stt = startIndex + index + 1; // Tính STT: Bắt đầu từ 1 + offset trang
            const revenue = ((v.views / 1000) * rpm).toFixed(2);
            
            // Badge màu sắc cho V/S Ratio
            let vsBadge = 'bg-gray-100 text-gray-600';
            if (v.vsRatio >= 5) vsBadge = 'bg-green-100 text-green-700 border-green-200';
            else if (v.vsRatio >= 2) vsBadge = 'bg-blue-100 text-blue-700 border-blue-200';
            else if (v.vsRatio >= 1) vsBadge = 'bg-yellow-100 text-yellow-700 border-yellow-200';

            const flagDisplay = v.flag ? `<span class="ml-1" title="${v.country}">${v.flag}</span>` : '';
            
            return `
            <tr class="group bg-white dark:bg-white/5 border-b border-gray-100 dark:border-white/5 last:border-0 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors
                       flex flex-col md:table-row mb-4 md:mb-0 rounded-2xl md:rounded-none shadow-sm md:shadow-none border md:border-b-0 p-4 md:p-0">
                
                <!-- 0. STT (Desktop Column / Mobile Badge) -->
                <td class="p-0 md:p-4 block md:table-cell w-full md:w-auto mb-2 md:mb-0">
                    <!-- Mobile View: STT nằm cùng hàng với title/thumb -->
                    <div class="md:hidden flex items-center gap-2 mb-2">
                        <span class="bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400 text-[10px] font-black px-2 py-0.5 rounded">#${stt}</span>
                    </div>
                    <!-- Desktop View: STT column -->
                    <div class="hidden md:block text-center font-bold text-gray-400 dark:text-gray-500 text-xs">
                        ${stt}
                    </div>
                </td>

                <!-- 1. VIDEO INFO -->
                <td class="p-0 md:p-4 block md:table-cell w-full md:w-auto mb-3 md:mb-0">
                    <div class="flex gap-4">
                        <div class="relative flex-shrink-0 group-hover:scale-105 transition-transform duration-300">
                            <img src="${v.thumbnail}" class="w-24 h-[54px] md:w-28 md:h-16 rounded-lg object-cover shadow-sm cursor-pointer" onclick="window.open('https://youtu.be/${v.id}')">
                            <div class="absolute bottom-1 right-1 bg-black/80 text-white text-[9px] px-1.5 py-0.5 rounded font-bold md:hidden">
                                ${moment(v.publishedAt).fromNow(true)}
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[13px] md:text-sm font-bold text-gray-900 dark:text-gray-100 line-clamp-2 mb-1.5 cursor-pointer hover:text-indigo-600 dark:hover:text-indigo-400 leading-snug" onclick="window.open('https://youtu.be/${v.id}')" title="${v.title}">
                                ${v.title}
                            </div>
                            <div class="text-[10px] text-slate-500 font-bold uppercase flex items-center truncate gap-1">
                                <span class="truncate max-w-[100px]">${v.channel}</span> 
                                <span class="text-slate-300">•</span> 
                                ${formatNumber(v.subs)} subs ${flagDisplay}
                            </div>
                        </div>
                    </div>
                </td>
                
                <!-- 2. METRICS GRID (FIXED: Views only on Desktop) -->
                <td class="p-0 md:p-4 block md:table-cell w-full md:w-auto">
                    <!-- Mobile: Grid 3 items / Desktop: Only Views Item remains visible -->
                    <div class="grid grid-cols-3 gap-2 md:block">
                        
                        <!-- Views (Visible Mobile & Desktop) -->
                        <div class="bg-gray-50 dark:bg-white/5 md:bg-transparent p-2 md:p-0 rounded-lg md:text-right">
                            <div class="text-[9px] text-slate-400 font-bold uppercase md:hidden mb-1">Views</div>
                            <div class="text-xs md:text-sm font-black text-gray-800 dark:text-gray-200">${formatNumber(v.views)}</div>
                        </div>

                        <!-- V/S Score (Visible Mobile / HIDDEN Desktop) -->
                        <div class="bg-gray-50 dark:bg-white/5 md:bg-transparent p-2 md:p-0 rounded-lg md:text-right md:hidden">
                            <div class="text-[9px] text-slate-400 font-bold uppercase md:hidden mb-1">Hiệu suất</div>
                            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black border ${vsBadge}">
                                ${v.vsRatio}x
                            </div>
                        </div>

                        <!-- Revenue (Visible Mobile / HIDDEN Desktop) -->
                        <div class="bg-gray-50 dark:bg-white/5 md:bg-transparent p-2 md:p-0 rounded-lg md:text-right md:hidden">
                            <div class="text-[9px] text-slate-400 font-bold uppercase md:hidden mb-1">Est. $$</div>
                            <div class="text-xs font-black text-yellow-600 dark:text-yellow-500">$${revenue}</div>
                        </div>
                    </div>
                </td>

                <!-- 3. Desktop Columns (V/S, Revenue, Time) -->
                <!-- V/S Desktop -->
                <td class="p-4 text-right hidden md:table-cell">
                    <span class="px-2 py-1 rounded text-xs font-black border ${vsBadge}">${v.vsRatio}x</span>
                </td>
                <!-- Revenue Desktop -->
                <td class="p-4 text-right hidden md:table-cell text-xs font-bold text-yellow-600">$${revenue}</td>

                <!-- Time -->
                <td class="p-0 md:p-4 text-right hidden md:table-cell text-xs font-medium text-slate-500">
                    ${moment(v.publishedAt).fromNow()}
                </td>
                
                <!-- Action -->
                <td class="p-3 md:p-4 text-center block md:table-cell border-t md:border-0 border-gray-100 dark:border-white/5 mt-3 md:mt-0 pt-3 md:pt-0">
                    <a href="https://youtu.be/${v.id}" target="_blank" class="w-full md:w-9 h-8 md:h-9 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white transition-all text-xs font-bold uppercase gap-2">
                        <i class="fa-brands fa-youtube"></i> <span class="md:hidden">Xem trên YouTube</span>
                    </a>
                </td>
            </tr>`;
        }).join('');
    }

    function renderStrategy(intelligence, seedKeyword) {
        _intelligence = intelligence;
        const section = document.getElementById('strategySection');
        section.classList.remove('hidden');
        section.classList.add('animate__fadeInUp');

        // --- Render Hot Niche Card (Responsive) ---
        const nicheContainer = document.getElementById('strategyMicroNiche');
        if (nicheContainer) {
            let badgeClass = 'bg-gray-100 text-gray-600 border-gray-200';
            if(intelligence.score >= 80) badgeClass = 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-500/30';
            else if(intelligence.score >= 60) badgeClass = 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-500/30';
            else badgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-500/30';

            nicheContainer.innerHTML = `
                <div class="flex flex-col h-full justify-between group cursor-pointer" onclick="Renderer.openNicheModal()">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">💎 MICRO-NICHE ĐỀ XUẤT</span>
                            <i class="fa-solid fa-circle-info text-indigo-400 animate-pulse text-xs"></i>
                        </div>
                        <div class="text-xl md:text-2xl font-black text-gray-900 dark:text-white leading-tight mb-3 break-words group-hover:text-indigo-500 transition-colors">
                            ${intelligence.microNiche.toUpperCase()}
                        </div>
                        <div class="flex flex-wrap gap-2 mb-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold border ${badgeClass} uppercase tracking-wider shadow-sm">
                                <i class="fa-solid fa-star mr-1"></i> ${intelligence.rating}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold bg-white dark:bg-white/5 text-slate-500 dark:text-slate-400 border border-gray-200 dark:border-white/10 uppercase tracking-wider">
                                Score: ${intelligence.score}/100
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 text-[10px] text-slate-400 font-medium flex items-center gap-1 opacity-70 group-hover:opacity-100 transition-opacity">
                        <span class="underline">Bấm để xem phân tích chi tiết</span> <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            `;
        }

        // --- Render Metrics ---
        document.getElementById('strategyKeywordBase').innerText = seedKeyword;
        document.getElementById('strategyKingKeyword').innerText = intelligence.kingKeyword.toUpperCase();
        if(document.getElementById('nicheScoreValue')) document.getElementById('nicheScoreValue').innerText = intelligence.score;
        const ratingTextEl = document.getElementById('nicheRatingText');
        if(ratingTextEl) {
            ratingTextEl.innerText = intelligence.rating;
            ratingTextEl.className = `text-sm font-bold ${intelligence.ratingColor}`;
        }
        if(document.getElementById('bestUploadTime')) document.getElementById('bestUploadTime').innerText = intelligence.bestTime;

        // --- Render Clusters Grid (Responsive) ---
        const clusterEl = document.getElementById('strategyCluster');
        if (intelligence.clusters.length > 0) {
            const initialCount = 8;
            const visibleClusters = intelligence.clusters.slice(0, initialCount);
            const hiddenClusters = intelligence.clusters.slice(initialCount);
            
            intelligence.clusters.forEach((c, i) => c.originalIndex = i);

            const createClusterCard = (c, idx) => {
                const smartTitles = Analyzer.generateSmartTitles(c, c.videos);
                const previewTitle = smartTitles[0] ? smartTitles[0].text : 'Đang phân tích...';
                return `
                <div class="relative flex flex-col justify-between p-3 rounded-xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 cursor-pointer h-full group" onclick="Renderer.openClusterModal(${c.originalIndex})">
                    <div class="flex items-start justify-between mb-2">
                        <div class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-tight line-clamp-2 pr-2 leading-snug group-hover:text-indigo-500 transition-colors">
                            ${c.name}
                        </div>
                        <span class="flex-shrink-0 px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                            ${c.videos.length}
                        </span>
                    </div>
                    <div class="mt-auto pt-2 border-t border-gray-100 dark:border-white/5">
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-500 dark:text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">
                            <i class="fa-solid fa-lightbulb text-[9px] opacity-70"></i>
                            <span class="truncate font-medium italic">"${previewTitle}"</span>
                        </div>
                    </div>
                </div>`;
            };

            let html = `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 animate__animated animate__fadeIn">`;
            html += visibleClusters.map((c, i) => createClusterCard(c, i)).join('');
            
            if (hiddenClusters.length > 0) {
                html += `</div><div id="hiddenClusters" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-3 animate__animated animate__fadeIn">`;
                html += hiddenClusters.map((c, i) => createClusterCard(c, i)).join('');
                html += `</div>`;
                html += `
                <div class="mt-5 text-center">
                    <button id="toggleClustersBtn" onclick="window.toggleClusters()" class="group inline-flex items-center gap-2 px-5 py-2 rounded-full bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition-all border border-gray-200 dark:border-white/10 uppercase tracking-wider">
                        <span>Xem thêm ${hiddenClusters.length} chủ đề khác</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 group-hover:translate-y-0.5" id="toggleClusterIcon"></i>
                    </button>
                </div>`;
            } else {
                html += `</div>`;
            }
            clusterEl.innerHTML = html;
            
            if (!window.toggleClusters) {
                window.toggleClusters = () => {
                    const hiddenDiv = document.getElementById('hiddenClusters');
                    const btnIcon = document.getElementById('toggleClusterIcon');
                    const btnSpan = document.querySelector('#toggleClustersBtn span');
                    if (hiddenDiv.classList.contains('hidden')) {
                        hiddenDiv.classList.remove('hidden');
                        btnIcon.style.transform = 'rotate(180deg)';
                        btnSpan.innerText = 'Thu gọn danh sách';
                    } else {
                        hiddenDiv.classList.add('hidden');
                        btnIcon.style.transform = 'rotate(0deg)';
                        btnSpan.innerText = `Xem thêm ${hiddenClusters.length} chủ đề khác`;
                        document.getElementById('strategyCluster').scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                };
            }
        } else {
            clusterEl.innerHTML = `<div class="text-center text-xs italic opacity-50 p-4">Cần thêm dữ liệu để phân tích Topic Cluster.</div>`;
        }
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // --- Render Top Channels (Responsive Update) ---
    function renderTopChannels(videos) {
        const chanMap = {};
        videos.forEach(v => {
            if(!chanMap[v.channelId]) chanMap[v.channelId] = { name: v.channel, subs: v.subs, views: 0 };
            chanMap[v.channelId].views += v.views;
        });
        const sorted = Object.values(chanMap).sort((a,b) => b.views - a.views).slice(0, 5);
        const tbody = document.getElementById('topChannelsBody');
        
        tbody.innerHTML = sorted.map(c => `
            <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors border-b dark:border-white/5 last:border-0">
                <td class="p-4 font-bold text-gray-800 dark:text-gray-200">
                    <div class="truncate max-w-[150px] sm:max-w-xs">${c.name}</div>
                    <div class="text-[9px] text-slate-400 font-normal sm:hidden mt-0.5">${c.subs.toLocaleString()} subs</div>
                </td>
                <!-- Subs: Ẩn trên Mobile -->
                <td class="p-4 text-right text-slate-500 font-bold hidden sm:table-cell">${c.subs.toLocaleString()}</td>
                <!-- Views: Luôn hiện -->
                <td class="p-4 text-right text-indigo-500 font-black">${c.views.toLocaleString()}</td>
                <!-- Rank: Ẩn trên Mobile -->
                <td class="p-4 text-center hidden sm:table-cell"><span class="text-[9px] bg-green-100 text-green-700 px-2 py-1 rounded font-bold uppercase">Top Tier</span></td>
            </tr>
        `).join('');
    }

    function openNicheModal() {
        if (!_intelligence) return;
        const i = _intelligence;
        const content = `
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white text-center shadow-lg">
                    <div class="text-xs font-bold uppercase opacity-80 mb-2">Tổng điểm tiềm năng</div>
                    <div class="text-6xl font-black mb-2">${i.score}</div>
                    <div class="text-xl font-bold uppercase tracking-wider">${i.rating}</div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl border dark:border-white/5">
                        <div class="text-[10px] text-slate-500 uppercase font-black mb-1">Từ khóa Vua</div>
                        <div class="text-lg font-bold text-indigo-600 dark:text-indigo-400 break-words">${i.kingKeyword.toUpperCase()}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-xl border dark:border-white/5">
                        <div class="text-[10px] text-slate-500 uppercase font-black mb-1">Giờ đăng vàng</div>
                        <div class="text-lg font-bold text-orange-500 break-words">${i.bestTime}</div>
                    </div>
                </div>
                <div class="prose dark:prose-invert max-w-none text-sm text-slate-600 dark:text-slate-300">
                    <p class="font-bold mb-2">💡 Tại sao chọn ngách này?</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Hiệu suất V/S (View/Sub) đạt mức cao, cho thấy nội dung viral tự nhiên tốt.</li>
                        <li>Cụm từ khóa <strong>"${i.kingKeyword}"</strong> xuất hiện lặp lại trong các video top đầu.</li>
                        <li>Có nhiều khoảng trống nội dung (Content Gap) chưa được khai thác hết.</li>
                    </ul>
                </div>
            </div>`;
        _showModal(`PHÂN TÍCH: ${i.microNiche.toUpperCase()}`, content);
    }

    function openClusterModal(idx) {
        if (!_intelligence || !_intelligence.clusters[idx]) return;
        const c = _intelligence.clusters[idx];
        const smartTitles = Analyzer.generateSmartTitles(c, c.videos);
        const content = `
            <div class="space-y-8">
                <div class="flex items-center gap-4 bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl border border-indigo-100 dark:border-indigo-500/20">
                    <div class="flex-1">
                        <div class="text-[10px] text-indigo-500 uppercase font-black mb-1">Tên Cluster</div>
                        <div class="text-lg font-black text-gray-900 dark:text-white">${c.name}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-[10px] text-indigo-500 uppercase font-black mb-1">Score</div>
                        <div class="text-2xl font-black text-indigo-600 dark:text-indigo-400">${Math.round(c.score)}</div>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-pink-500"></i> 5 Ý Tưởng Tiêu Đề Viral
                    </h4>
                    <div class="space-y-2">
                        ${smartTitles.map(t => `
                            <div class="group relative bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 p-3 rounded-lg hover:border-pink-500 transition-all cursor-pointer" onclick="navigator.clipboard.writeText('${t.text}'); App.toast('Đã copy tiêu đề!', 'success')">
                                <div class="text-[9px] text-slate-400 font-bold uppercase mb-1">${t.type}</div>
                                <div class="text-sm font-bold text-gray-800 dark:text-gray-200 pr-8">"${t.text}"</div>
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center text-slate-400 group-hover:text-pink-500 group-hover:bg-pink-50 transition-colors"><i class="fa-regular fa-copy"></i></div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase mb-3 flex items-center gap-2">
                        <i class="fa-brands fa-youtube text-red-600"></i> Video Minh Chứng (${c.videos.length})
                    </h4>
                    <div class="space-y-3">
                        ${c.videos.slice(0, 3).map(v => `
                            <div class="flex gap-3 items-start p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-white/5 transition-colors cursor-pointer" onclick="window.open('https://youtu.be/${v.id}')">
                                <img src="${v.thumbnail}" class="w-24 h-14 rounded object-cover shadow-sm flex-shrink-0">
                                <div>
                                    <div class="text-xs font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug mb-1">${v.title}</div>
                                    <div class="text-[10px] text-slate-500 font-bold">${parseInt(v.views).toLocaleString()} views • ${v.vsRatio}x V/S</div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>`;
        _showModal(`CLUSTER: ${c.name.toUpperCase()}`, content);
    }

    function _showModal(title, content) {
        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('detailModalContent');
        const titleEl = document.getElementById('modalTitle');
        const bodyEl = document.getElementById('modalBody');
        titleEl.innerText = title;
        bodyEl.innerHTML = content;
        modal.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
        modalContent.classList.remove('translate-y-full', 'scale-95');
        modalContent.classList.add('translate-y-0', 'scale-100');
    }

    function closeModal() {
        const modal = document.getElementById('detailModal');
        const modalContent = document.getElementById('detailModalContent');
        modalContent.classList.remove('translate-y-0', 'scale-100');
        modalContent.classList.add('translate-y-full', 'scale-95');
        setTimeout(() => { modal.classList.add('invisible', 'opacity-0', 'pointer-events-none'); }, 200);
    }

    function renderStats(videos, rpm) {
        const totalV = videos.reduce((sum, v) => sum + v.views, 0);
        const avgVS = videos.length ? (videos.reduce((sum, v) => sum + v.vsRatio, 0) / videos.length).toFixed(2) : 0;
        document.getElementById('statTotalVideos').innerText = videos.length;
        document.getElementById('statAvgViews').innerText = (totalV / (videos.length || 1)).toLocaleString();
        document.getElementById('statOpportunity').innerText = avgVS + 'x';
        document.getElementById('statEstRevenue').innerText = '$' + ((totalV / 1000) * rpm).toLocaleString();
    }

    function _renderPageSizeSelect(currentSize) {
        return `
            <div class="flex items-center gap-2">
                <span class="hidden md:inline">Hiển thị:</span>
                <select onchange="App.setPageSize(this.value)" class="bg-white dark:bg-black/20 border border-gray-200 dark:border-white/10 text-xs font-bold rounded-lg py-1.5 pl-2 pr-6 focus:outline-none focus:border-indigo-500 cursor-pointer">
                    <option value="10" ${currentSize == 10 ? 'selected' : ''}>10</option>
                    <option value="20" ${currentSize == 20 ? 'selected' : ''}>20</option>
                    <option value="50" ${currentSize == 50 ? 'selected' : ''}>50</option>
                    <option value="100" ${currentSize == 100 ? 'selected' : ''}>100</option>
                </select>
            </div>
        `;
    }
    // --- 2. ADVANCED PAGINATION UI ---
    function renderPagination(totalItems, currentPage, pageSize) {
        const container = document.getElementById('pagination');
        if (!container) return;

        const totalPages = Math.ceil(totalItems / pageSize);
        
        // Hide if no pages
        if (totalPages <= 1) {
            container.innerHTML = `
                <div class="flex justify-between items-center text-xs text-slate-400 w-full px-4">
                    <span>Hiển thị ${totalItems} kết quả</span>
                    ${_renderPageSizeSelect(pageSize)}
                </div>`;
            return;
        }

        // Logic tạo dãy số trang thông minh (1 ... 4 5 6 ... 10)
        let delta = 1; // Số trang hiển thị 2 bên trang hiện tại
        let range = [];
        let rangeWithDots = [];
        let l;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
                range.push(i);
            }
        }

        for (let i of range) {
            if (l) {
                if (i - l === 2) rangeWithDots.push(l + 1);
                else if (i - l !== 1) rangeWithDots.push('...');
            }
            rangeWithDots.push(i);
            l = i;
        }

        // Render HTML
        container.innerHTML = `
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 w-full bg-gray-50/50 dark:bg-white/5 p-4 border-t border-gray-200 dark:border-white/5">
                
                <!-- Left: Info & Size Select -->
                <div class="flex items-center gap-4 text-xs text-slate-500 order-2 md:order-1 w-full md:w-auto justify-between md:justify-start">
                    <span class="hidden md:inline">Hiển thị ${(currentPage - 1) * pageSize + 1} - ${Math.min(currentPage * pageSize, totalItems)} trong ${totalItems}</span>
                    <span class="md:hidden">Tổng ${totalItems} video</span>
                    ${_renderPageSizeSelect(pageSize)}
                </div>

                <!-- Right: Pagination Controls -->
                <div class="flex items-center gap-1.5 order-1 md:order-2">
                    
                    <!-- First & Prev -->
                    <button onclick="App.setPage(1)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-white/5 border dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors ${currentPage === 1 ? 'opacity-30 pointer-events-none' : ''}">
                        <i class="fa-solid fa-angles-left text-[10px]"></i>
                    </button>
                    <button onclick="App.setPage(${currentPage - 1})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-white/5 border dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors ${currentPage === 1 ? 'opacity-30 pointer-events-none' : ''}">
                        <i class="fa-solid fa-chevron-left text-[10px]"></i>
                    </button>

                    <!-- Number Links -->
                    <div class="hidden sm:flex items-center gap-1.5 mx-1">
                        ${rangeWithDots.map(p => p === '...' ? 
                            `<span class="text-xs text-slate-400 w-6 text-center">...</span>` : 
                            `<button onclick="App.setPage(${p})" class="w-8 h-8 rounded-lg text-xs font-bold transition-all ${currentPage === p ? 'bg-red-600 text-white shadow-lg shadow-red-500/30 border border-red-600' : 'bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 border dark:border-white/10 hover:bg-gray-50' }">${p}</button>`
                        ).join('')}
                    </div>
                    
                    <!-- Mobile Current Page Display -->
                    <span class="sm:hidden text-xs font-bold px-2 text-slate-600 dark:text-slate-300">
                        Trang ${currentPage} / ${totalPages}
                    </span>

                    <!-- Next & Last -->
                    <button onclick="App.setPage(${currentPage + 1})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-white/5 border dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors ${currentPage === totalPages ? 'opacity-30 pointer-events-none' : ''}">
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </button>
                    <button onclick="App.setPage(${totalPages})" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-white/5 border dark:border-white/10 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors ${currentPage === totalPages ? 'opacity-30 pointer-events-none' : ''}">
                        <i class="fa-solid fa-angles-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        `;
    }
    function formatNumber(num) {
        return Intl.NumberFormat('en-US', { notation: "compact", maximumFractionDigits: 1 }).format(num);
    }

    return { 
        renderRegions, 
        renderTable, 
        renderPagination, // EXPORTED HERE
        renderStrategy: renderStrategy || ((i,k) => {}), 
        renderStats: renderStats || ((v,r) => {}),
        renderTopChannels: renderTopChannels || ((v) => {}),
        openNicheModal: openNicheModal || (() => {}),
        openClusterModal: openClusterModal || (() => {}),
        closeModal: closeModal || (() => {})
    };
})();