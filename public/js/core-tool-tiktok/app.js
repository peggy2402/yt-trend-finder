const TikTokApp = {
    elements: {
        regionSelect: document.getElementById('regionSelect'),
        analyzeBtn: document.getElementById('analyzeBtn'),
        resultsBody: document.getElementById('resultsBody'),
        emptyState: document.getElementById('emptyState'),
        errorMsg: document.getElementById('errorMsg'),
        statLongVideos: document.getElementById('statLongVideos'),
        statRevenue: document.getElementById('statRevenue'),
        usageBadge: document.getElementById('usageBadge'), // Thêm badge hiển thị usage
    },

    init() {
        if (!this.elements.analyzeBtn) return;
        this.elements.analyzeBtn.addEventListener('click', () => this.runAnalysis());
    },

    async runAnalysis() {
        const region = this.elements.regionSelect.value;
        this.setLoading(true);
        this.hideError();
        this.clearResults();

        try {
            const url = `/tool/tiktok-beta/search?region=${region}&filter=long_only`;
            const response = await fetch(url);

            // Xử lý lỗi 429 (Hết lượt) hoặc lỗi khác
            if (!response.ok) {
                const errorData = await response.json();
                if (response.status === 429) {
                    throw new Error(errorData.error || 'Đã hết lượt quét trong ngày.');
                }
                throw new Error(`Server Error: ${response.status}`);
            }

            const data = await response.json();

            if (data.error) throw new Error(data.error);

            if (data.videos && data.videos.length > 0) {
                this.renderResults(data.videos);
                this.updateStats(data.meta);

                // Cập nhật hiển thị Usage nếu có
                if (data.meta.usage) {
                    this.updateUsageDisplay(data.meta.usage);
                }
            } else {
                this.showError("Không tìm thấy video nào.");
            }

        } catch (error) {
            console.error(error);
            this.showError(error.message || "Lỗi kết nối không xác định");
        } finally {
            this.setLoading(false);
        }
    },

    renderResults(videos) {
        this.elements.resultsBody.innerHTML = videos.map((v) => {
            const durationClass = v.is_beta ? 'text-tiktokCyan font-bold' : 'text-slate-500';
            const revenueClass = v.is_beta ? 'text-green-400 font-bold' : 'text-slate-600';
            const fmt = (n) => new Intl.NumberFormat('en-US', { notation: "compact" }).format(n);

            return `
            <tr class="hover:bg-white/5 transition-colors">
                <td class="p-4">
                    <div class="flex items-center gap-3">
                        <img src="${v.cover}" class="w-10 h-14 object-cover rounded bg-slate-800" onerror="this.src='https://placehold.co/40x60'">
                        <div class="max-w-[200px]">
                            <div class="line-clamp-2 text-white text-xs mb-1" title="${v.desc}">${v.desc}</div>
                            <div class="flex items-center gap-1 text-[10px] text-slate-500">
                                <img src="${v.author.avatar}" class="w-3 h-3 rounded-full"> ${v.author.uniqueId}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="p-4 text-center ${durationClass}">${this.fmtTime(v.duration)}</td>
                <td class="p-4 text-center text-xs text-slate-400">
                    <div><i class="fa-solid fa-play"></i> ${fmt(v.stats.views)}</div>
                    <div><i class="fa-solid fa-heart"></i> ${fmt(v.stats.likes)}</div>
                </td>
                <td class="p-4 text-right ${revenueClass}">
                    $${v.beta_analysis.est_revenue}
                </td>
                <td class="p-4 text-center">
                    <a href="${v.link}" target="_blank" class="text-tiktokCyan hover:text-white"><i class="fa-solid fa-external-link"></i></a>
                </td>
            </tr>`;
        }).join('');
    },

    updateStats(meta) {
        this.elements.statLongVideos.innerText = meta.beta_found;
        this.elements.statRevenue.innerText = '$' + new Intl.NumberFormat().format(meta.total_revenue);
    },

    updateUsageDisplay(usage) {
        if (this.elements.usageBadge) {
            this.elements.usageBadge.innerText = `Usage: ${usage.used}/${usage.limit} (${usage.plan})`;
            // Đổi màu nếu sắp hết lượt
            if (usage.used >= usage.limit) {
                this.elements.usageBadge.classList.add('bg-red-500/20', 'text-red-400');
            }
        }
    },

    fmtTime(s) {
        const min = Math.floor(s / 60);
        const sec = s % 60;
        return `${min}:${sec.toString().padStart(2, '0')}`;
    },

    setLoading(isLoading) {
        const btn = this.elements.analyzeBtn;
        if (isLoading) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';
            this.elements.resultsBody.classList.add('opacity-50');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-bolt"></i> QUÉT TRENDING NGAY';
            this.elements.resultsBody.classList.remove('opacity-50');
        }
    },

    clearResults() {
        this.elements.resultsBody.innerHTML = '';
    },

    showError(msg) {
        this.elements.errorMsg.innerText = msg;
        this.elements.errorMsg.classList.remove('hidden');
    },

    hideError() {
        this.elements.errorMsg.classList.add('hidden');
    }
};

document.addEventListener('DOMContentLoaded', () => TikTokApp.init());
