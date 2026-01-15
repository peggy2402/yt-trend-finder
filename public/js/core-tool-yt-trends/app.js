/**
 * LAYER 5: APPLICATION CONTROLLER (ENHANCED)
 * Cập nhật: Logic Check Key Health UI & Status Display Update
 */
const App = (() => {
    // let globalVideos = [];
    const State = {
        globalVideos: [],     // Dữ liệu gốc từ API
        filteredVideos: [],   // Dữ liệu sau khi sort/filter (nếu có)
        currentPage: 1,
        pageSize: 10,
        rpm: 0.3
    };
    function init() {
        console.log("[App] Initializing...");
        try {
            // Setup listener trước khi init KeyManager để bắt event initial update
            _setupKeyListener();
            
            KeyManager.init();
            Renderer.renderRegions(AppConfig.REGIONS);
            _bindEvents();
            _loadSettings();
            _exposeGlobals();
            console.log("[App] Ready.");
        } catch (e) { console.error(e); }
    }

    function _exposeGlobals() {
        window.saveSettings = _saveSettings;
        window.saveApiKey = _quickSaveKey;
        window.toggleSettingsModal = _toggleModal;
        window.closeSettingsModal = _closeModal;
        window.updateRpmLive = _updateRpmLive;
        window.startAnalysis = _runAnalysis;
        window.sortVideos = _sortVideos;
        window.checkKeysHealth = _checkKeysHealth;
        
        // --- NEW PAGINATION API ---
        window.App = window.App || {};
        window.App.setPage = _setPage;
        window.App.setPageSize = _setPageSize;
    }
    // --- Pagination Logic ---
    function _setPage(page) {
        const totalPages = Math.ceil(State.filteredVideos.length / State.pageSize);
        if (page < 1 || page > totalPages) return;
        
        State.currentPage = parseInt(page);
        _renderCurrentView();
        
        // Scroll nhẹ lên đầu bảng
        const tableHeader = document.querySelector('#resultsBody').parentElement.previousElementSibling;
        if(tableHeader) tableHeader.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function _setPageSize(size) {
        State.pageSize = parseInt(size);
        State.currentPage = 1; // Reset về trang 1 khi đổi size
        _renderCurrentView();
    }

    function _renderCurrentView() {
        // 1. Slice Data
        const start = (State.currentPage - 1) * State.pageSize;
        const end = start + State.pageSize;
        const pageData = State.filteredVideos.slice(start, end);

        // 2. Render Table với tham số start để tính STT
        Renderer.renderTable(pageData, State.rpm, start); // THÊM start VÀO ĐÂY

        // 3. Render Pagination Controls
        Renderer.renderPagination(State.filteredVideos.length, State.currentPage, State.pageSize);
    }
    function _updateRpmLive(val) {
        State.rpm = parseFloat(val);
        document.getElementById('rpmValue').innerText = '$' + State.rpm;
        if (State.globalVideos.length > 0) {
            Renderer.renderStats(State.globalVideos, State.rpm); // Update tổng quan
            _renderCurrentView(); // Update bảng hiện tại
        }
    }
    // --- NEW: Key Health Check Logic ---
    async function _checkKeysHealth() {
        const input = document.getElementById('apiKeyList');
        const resultContainer = document.getElementById('keyCheckResult');
        const btn = document.getElementById('btnCheckKeys');
        
        if (!input || !input.value.trim()) return toast('Danh sách Key trống!', 'warning');

        // Parse keys từ input hiện tại (chưa cần save)
        const keysToCheck = input.value.split(/\r?\n/).map(k => k.trim()).filter(k => k.length > 20);
        
        if (keysToCheck.length === 0) return toast('Không tìm thấy Key hợp lệ!', 'error');

        // UI Loading state
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Checking...';
        btn.disabled = true;
        resultContainer.classList.remove('hidden');
        resultContainer.innerHTML = '<div class="text-center text-xs text-slate-500 italic py-2">Đang kết nối đến Google Server...</div>';

        let html = '';
        let validCount = 0;

        // Chạy kiểm tra từng key (Parallel requests for speed)
        const promises = keysToCheck.map(async (key, index) => {
            const status = await KeyManager.validateKey(key);
            const isGood = status.valid;
            if (isGood) validCount++;
            
            // Format hiển thị: Key ẩn bớt ký tự + Trạng thái
            const maskedKey = key.substring(0, 8) + '...' + key.substring(key.length - 6);
            const icon = isGood ? '<i class="fa-solid fa-check-circle text-green-500"></i>' : '<i class="fa-solid fa-circle-xmark text-red-500"></i>';
            const textClass = isGood ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400 line-through opacity-70';
            const msg = isGood ? 'Hoạt động tốt' : (status.reason || 'Lỗi');

            return `
            <div class="flex items-center justify-between text-[10px] p-2 rounded bg-white dark:bg-white/5 border border-transparent ${isGood ? 'border-green-100 dark:border-green-900/30' : 'border-red-100 dark:border-red-900/30'}">
                <div class="flex items-center gap-2 font-mono ${textClass}">
                    ${icon} <span class="font-bold">Key #${index + 1}:</span> ${maskedKey}
                </div>
                <div class="font-bold uppercase ${isGood ? 'text-green-600' : 'text-red-500'}">${msg}</div>
            </div>`;
        });

        const results = await Promise.all(promises);
        resultContainer.innerHTML = results.join('');

        // Restore UI
        btn.innerHTML = originalText;
        btn.disabled = false;
        
        if (validCount === keysToCheck.length) toast('Tuyệt vời! Tất cả Keys đều hoạt động.', 'success');
        else toast(`Phát hiện ${keysToCheck.length - validCount} Key lỗi. Vui lòng xóa chúng!`, 'warning');
    }

    function _bindEvents() {
        const analyzeBtn = document.getElementById('analyzeBtn');
        if (analyzeBtn) analyzeBtn.addEventListener('click', _runAnalysis);
        
        document.getElementById('regionSelect')?.addEventListener('change', (e) => {
            localStorage.setItem(AppConfig.STORAGE_KEYS.REGION, e.target.value);
        });
        document.getElementById('searchInput')?.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') _runAnalysis();
        });

        const modal = document.getElementById('settingsModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target.id === 'settingsModal' || e.target.closest('.bg-black/80')) {
                    _closeModal();
                }
            });
        }
    }

    function _setupKeyListener() {
        document.addEventListener('zt:keyUpdate', (e) => {
            const { total, alive } = e.detail;
            const badge = document.getElementById('activeKeyCountBadge');
            const status = document.getElementById('apiKeyStatus');

            if (badge) {
                // Update Badge format: Alive/Total
                badge.innerText = `${alive}/${total}`;
                badge.classList.toggle('hidden', total === 0);
                badge.classList.toggle('bg-red-500', alive === 0);
                badge.classList.toggle('bg-green-500', alive > 0);
                // Thêm class w-auto px-1 để badge tự giãn nếu số lớn (ví dụ 10/10)
                badge.classList.add('w-auto', 'px-1.5');
                badge.classList.remove('w-5'); 
            }
            if (status) {
                // Update Status Text format: "X/Y Key Ready"
                status.innerHTML = alive > 0 
                    ? `<span class="text-green-600 font-bold"><i class="fa-solid fa-server mr-1"></i> ${alive} Đang sử dụng</span>` 
                    : `<span class="text-red-500 font-bold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Chưa cập nhật API Keys</span>`;
            }
        });
    }

    function _loadSettings() {
        const savedRpm = localStorage.getItem(AppConfig.STORAGE_KEYS.RPM);
        if(savedRpm) {
            const slider = document.getElementById('rpmSlider');
            if(slider) slider.value = savedRpm;
            const val = document.getElementById('rpmValue');
            if(val) val.innerText = '$' + savedRpm;
        }
    }

    // --- AUTO-CLEAN SAVE SETTINGS (IMPROVED UX) ---
    async function _saveSettings() {
        const apiKeyInput = document.getElementById('apiKeyList');
        const rpmInput = document.getElementById('rpmSlider');
        const saveBtn = document.querySelector('button[onclick="saveSettings()"]');
        
        if (!apiKeyInput || !rpmInput) return toast("Lỗi giao diện!", 'error');

        // 1. Parse Input
        const rawKeys = apiKeyInput.value.split(/\r?\n/).map(k => k.trim()).filter(k => k.length > 20);
        if (rawKeys.length === 0) return toast("Vui lòng nhập ít nhất 1 Key hợp lệ!", 'warning');

        // 2. UI Loading State
        const originalText = saveBtn.innerText;
        saveBtn.innerText = "ĐANG KIỂM TRA & LÀM SẠCH...";
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-75', 'cursor-wait');

        try {
            // 3. Parallel Validation
            const validationResults = await Promise.all(rawKeys.map(async (key) => {
                const check = await KeyManager.validateKey(key);
                return { key, valid: check.valid, reason: check.reason };
            }));

            // 4. Filter Logic
            const goodKeys = [];
            let removedCount = 0;

            validationResults.forEach(item => {
                if (item.valid || item.reason === 'Network Error') {
                    goodKeys.push(item.key);
                } else {
                    removedCount++;
                    console.warn(`[Auto-Clean] Removing dead key: ${item.key.substring(0, 10)}... Reason: ${item.reason}`);
                }
            });

            // 5. Save Clean List
            const finalString = goodKeys.join('\n');
            const savedCount = KeyManager.save(finalString);
            
            apiKeyInput.value = finalString; 
            localStorage.setItem(AppConfig.STORAGE_KEYS.RPM, rpmInput.value);

            // 6. Notify User
            if (removedCount > 0) {
                toast(`Đã lưu ${savedCount} Key. Tự động xóa ${removedCount} Key lỗi!`, 'warning');
            } else {
                toast(`Đã lưu thành công ${savedCount} API Keys chuẩn!`, 'success');
            }
            
            setTimeout(() => _closeModal(), 1000);

        } catch (e) {
            console.error(e);
            toast("Lỗi khi lưu: " + e.message, 'error');
        } finally {
            saveBtn.innerText = originalText;
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-75', 'cursor-wait');
        }
    }
    
    function _quickSaveKey() {
        try {
            const input = document.getElementById('apiKeyInput');
            if (!input) return;
            const newKey = input.value.trim();
            if(newKey.length < 20) return toast('Key không hợp lệ', 'error');
            const currentRaw = KeyManager.getRawList();
            KeyManager.save(currentRaw + '\n' + newKey);
            input.value = '';
            toast('Đã thêm key mới!', 'success');
        } catch (e) { toast(e.message, 'error'); }
    }

    function _toggleModal() {
        const m = document.getElementById('settingsModal');
        if (m && m.classList.contains('invisible')) _openModal(); else _closeModal();
    }
    function _openModal() {
        const m = document.getElementById('settingsModal');
        const c = document.getElementById('modalContainer');
        const txt = document.getElementById('apiKeyList');
        if (m && c && txt) {
            txt.value = KeyManager.getRawList();
            document.getElementById('keyCheckResult').classList.add('hidden');
            document.getElementById('keyCheckResult').innerHTML = '';
            m.classList.remove('invisible', 'opacity-0', 'pointer-events-none');
            c.classList.replace('scale-95', 'scale-100');
        }
    }
    function _closeModal() {
        const m = document.getElementById('settingsModal');
        const c = document.getElementById('modalContainer');
        if (m && c) {
            m.classList.add('invisible', 'opacity-0', 'pointer-events-none');
            c.classList.replace('scale-100', 'scale-95');
        }
    }
    
    function _sortVideos(criteria) {
        // Check kỹ dữ liệu trong State
        if (!State.filteredVideos || State.filteredVideos.length === 0) {
            console.warn("Sort failed: No data found in State.filteredVideos");
            return toast("Chưa có dữ liệu để sắp xếp!", "warning");
        }
        
        console.log(`Sorting by ${criteria}... Total items: ${State.filteredVideos.length}`);

        // Clone array to avoid mutating globalVideos directly if we want to keep original order
        let sorted = [...State.filteredVideos];

        switch (criteria) {
            case 'views_desc': sorted.sort((a, b) => b.views - a.views); break;
            case 'views_asc': sorted.sort((a, b) => a.views - b.views); break;
            case 'vs_desc': sorted.sort((a, b) => b.vsRatio - a.vsRatio); break;
            case 'rpm_desc': sorted.sort((a, b) => b.views - a.views); break;
            case 'date_new': sorted.sort((a, b) => new Date(b.publishedAt) - new Date(a.publishedAt)); break;
            default: break;
        }

        // CẬP NHẬT STATE VÀ RENDER LẠI
        State.filteredVideos = sorted;
        State.currentPage = 1; // Reset trang về 1 khi sort để user thấy kết quả top đầu
        _renderCurrentView();
        
        toast("Đã sắp xếp danh sách!", "success");
    }

    // --- Core Action: Run Analysis ---
    async function _runAnalysis() {
        const query = document.getElementById('searchInput').value.trim();
        if (!query) return toast('Vui lòng nhập từ khóa!', 'warning');

        const regionCode = document.getElementById('regionSelect')?.value;
        const timeRange = document.getElementById('timeFilter')?.value || 'all';
        const maxRes = parseInt(document.getElementById('maxResultsFilter')?.value || 50);

        let publishedAfter = null;
        if (timeRange !== 'all') {
            const now = moment();
            const map = { '1h': [1, 'hours'], '24h': [24, 'hours'], '7d': [7, 'days'], '30d': [30, 'days'] };
            if (map[timeRange]) publishedAfter = now.subtract(...map[timeRange]).toISOString();
        }

        const btn = document.getElementById('analyzeBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> ĐANG QUÉT...';
        btn.disabled = true;
        
        // Reset UI
        document.getElementById('resultsBody').innerHTML = '<tr><td colspan="6" class="p-12 text-center text-slate-500 animate-pulse font-bold tracking-widest">ĐANG KẾT NỐI VỆ TINH DỮ LIỆU...</td></tr>';
        document.getElementById('pagination').innerHTML = ''; // Clear old pagination
        document.getElementById('strategySection').classList.add('hidden');

        try {
            // ... (Logic gọi API giữ nguyên từ file cũ của bạn) ...
            // Tôi giả định logic fetch ở đây giống các bước trước
            const searchParams = { part: 'snippet', q: query, type: 'video', maxResults: maxRes, order: 'viewCount' };
            if (regionCode) searchParams.regionCode = regionCode;
            if (publishedAfter) searchParams.publishedAfter = publishedAfter;

            const searchData = await YTService.fetch('search', searchParams);
            if (!searchData.items?.length) throw new Error("Không tìm thấy video nào.");

            const videoIds = searchData.items.map(i => i.id.videoId).join(',');
            const videoStats = await YTService.fetch('videos', { part: 'snippet,statistics,contentDetails', id: videoIds });

            const channelIds = [...new Set(videoStats.items.map(v => v.snippet.channelId))].slice(0, 50).join(',');
            const channelData = await YTService.fetch('channels', { part: 'snippet,statistics', id: channelIds });
            
            const channelMap = {};
            channelData.items.forEach(c => {
                channelMap[c.id] = { subs: c.statistics.subscriberCount, country: c.snippet.country || '' };
            });

            // --- DATA PROCESSING COMPLETE ---
            const processedVideos = Analyzer.processVideos(videoStats.items, channelMap);
            const intelligence = Analyzer.buildIntelligence(processedVideos, query);
            
            // --- UPDATE STATE ---
            State.globalVideos = processedVideos;
            State.filteredVideos = processedVideos; // Mặc định chưa filter
            State.currentPage = 1;
            State.rpm = parseFloat(document.getElementById('rpmSlider')?.value || 0.3);

            // --- RENDER ALL ---
            _renderCurrentView(); // Render Table + Pagination
            Renderer.renderStats(State.globalVideos, State.rpm);
            Renderer.renderStrategy(intelligence, query);
            Renderer.renderTopChannels(State.globalVideos);

            toast(`Quét thành công ${State.globalVideos.length} videos!`, 'success');

        } catch (error) {
            console.error(error);
            const msg = error.message === "NO_KEYS_AVAILABLE" ? "Vui lòng nhập API Key!" : 
                        error.message === "ALL_KEYS_EXHAUSTED" ? "Tất cả API Key đã hết hạn mức!" : error.message;
            toast(msg, 'error');
            document.getElementById('resultsBody').innerHTML = `<tr><td colspan="6" class="p-12 text-center text-red-500 font-bold">${msg}</td></tr>`;
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    function toast(msg, type = 'info') {
        const c = document.getElementById('toast-container');
        if (!c) return;
        const d = document.createElement('div');
        const colors = { success: 'bg-green-600', error: 'bg-red-600', warning: 'bg-yellow-500 text-black', info: 'bg-slate-700' };
        d.className = `p-4 mb-3 rounded-xl shadow-2xl text-white font-bold text-xs uppercase tracking-wide border border-white/10 animate__animated animate__fadeInRight ${colors[type] || colors.info}`;
        d.innerText = msg;
        c.appendChild(d);
        setTimeout(() => { d.classList.replace('animate__fadeInRight', 'animate__fadeOutRight'); setTimeout(() => d.remove(), 500); }, 3000);
    }

    return { init, toast, setPage: _setPage, setPageSize: _setPageSize };
})();

document.addEventListener('DOMContentLoaded', App.init);