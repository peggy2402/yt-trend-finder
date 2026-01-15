/**
 * LAYER 3: DOMAIN LOGIC - ANALYZER (AI DRIVEN)
 * Advanced NLP for Semantic Clustering & Viral Title Generation
 */
const Analyzer = (() => {

    // Stop words cho cả Tiếng Việt và Tiếng Anh để lọc nhiễu
    const STOP_WORDS = new Set([
        // Vietnamese
        'cách', 'làm', 'hướng', 'dẫn', 'của', 'những', 'các', 'là', 'gì', 'trong', 'với', 'cho', 'tại', 
        'người', 'video', 'review', 'vlog', 'mới', 'nhất', 'top', 'official', 'full', 'hd', '4k',
        // English
        'the', 'and', 'for', 'with', 'you', 'this', 'that', 'how', 'what', 'why', 'video', 'full', 
        'official', 'best', 'top', 'review', 'tutorial', '2024', '2025', 'vs', 'versus', 'shorts'
    ]);

    const POWER_WORDS = {
        shock: ['Shocked', 'Terrifying', 'Insane', 'Scary', 'Banned', 'Exposed', 'Kinh hoàng', 'Cấm', 'Sự thật'],
        curiosity: ['Secret', 'Mystery', 'Unknown', 'Hidden', 'Nobody knew', 'Bí mật', 'Ít ai biết'],
        urgent: ['Don\'t', 'Stop', 'Warning', 'Never', 'Immediately', 'Cảnh báo', 'Dừng lại'],
        emotional: ['Crying', 'Heartbreaking', 'Emotional', 'Touching', 'Regret', 'Xúc động', 'Hối hận']
    };

    // --- 1. NLP HELPER FUNCTIONS ---

    function _normalize(text) {
        return text.toLowerCase()
            .replace(/[^\w\s\u00C0-\u1EFF]/g, '') // Giữ lại chữ cái VN
            .replace(/\s+/g, ' ')
            .trim();
    }

    // Tạo N-grams (Cụm từ 2 hoặc 3 chữ)
    function _generateNGrams(text, n) {
        const words = _normalize(text).split(' ').filter(w => !STOP_WORDS.has(w) && w.length > 2);
        if (words.length < n) return [];
        const ngrams = [];
        for (let i = 0; i < words.length - n + 1; i++) {
            ngrams.push(words.slice(i, i + n).join(' '));
        }
        return ngrams;
    }

    // --- 2. CORE: SEMANTIC CLUSTERING ---

    /**
     * Xây dựng Cluster dựa trên ngữ nghĩa và hiệu suất video
     * Thay vì đếm từ khóa, ta tìm các "Cụm chủ đề" có view/like cao nhất
     */
    function buildSemanticClusters(videos) {
        const scoreMap = new Map(); // Map lưu điểm của từng N-gram
        
        videos.forEach(v => {
            // Tính điểm chất lượng của video (View + Tương tác)
            // Log scale view để tránh video triệu view nuốt hết video nhỏ tiềm năng
            const qualityScore = (Math.log10(v.views + 1) * 2) + (v.vsRatio * 5); 
            
            // Kết hợp Title + Tags quan trọng
            const textSource = `${v.title} ${v.tags.slice(0, 5).join(' ')}`;
            
            // Tạo Bi-grams (2 từ) và Tri-grams (3 từ)
            const ngrams = [
                ..._generateNGrams(textSource, 2),
                ..._generateNGrams(textSource, 3)
            ];

            ngrams.forEach(gram => {
                if (!scoreMap.has(gram)) {
                    scoreMap.set(gram, { 
                        word: gram, 
                        score: 0, 
                        count: 0, 
                        sampleVideos: [],
                        entities: new Set() // Lưu các thực thể liên quan (VD: tên con vật, tên game)
                    });
                }
                const entry = scoreMap.get(gram);
                entry.score += qualityScore;
                entry.count++;
                entry.sampleVideos.push(v);
                
                // Trích xuất entity đơn từ tags để dùng cho việc sinh tiêu đề sau này
                v.tags.slice(0, 3).forEach(t => entry.entities.add(t));
            });
        });

        // Lọc và sắp xếp Cluster
        let sortedClusters = Array.from(scoreMap.values())
            .filter(c => c.count >= 2) // Phải xuất hiện ít nhất trong 2 video
            .sort((a, b) => b.score - a.score);

        // Khử trùng lặp (VD: "Gorilla Tag" và "Gorilla Tag Horror" -> Lấy cái dài hơn nếu điểm cao)
        const uniqueClusters = [];
        const seenWords = new Set();

        sortedClusters.forEach(c => {
            // Kiểm tra xem cụm từ này có bị bao hàm bởi cụm từ đã chọn không
            const isDuplicate = Array.from(seenWords).some(seen => seen.includes(c.word) || c.word.includes(seen));
            if (!isDuplicate && uniqueClusters.length < 5) {
                uniqueClusters.push(c);
                seenWords.add(c.word);
            }
        });

        return uniqueClusters.map(c => ({
            name: _capitalize(c.word), // Tên cluster tự nhiên: "Ai Animal Story"
            score: c.score,
            videos: c.sampleVideos,
            entities: Array.from(c.entities) // Dùng để điền vào template
        }));
    }

    // --- 3. CORE: TITLE SUGGESTION ENGINE ---

    /**
     * Sinh tiêu đề thông minh dựa trên Cluster và Pattern
     */
    function generateSmartTitles(cluster, videos) {
        const titles = [];
        const entities = cluster.entities.length > 0 ? cluster.entities : [cluster.name];
        const mainSubject = _capitalize(cluster.name);
        const subSubject = _capitalize(entities[Math.floor(Math.random() * entities.length)] || mainSubject);

        // 1. Phân tích sentiment của cluster (Sợ hãi, Hài hước, Kiến thức?)
        // Dựa vào việc check từ khóa trong video mẫu
        const isHorror = videos.some(v => v.title.match(/scary|horror|ghost|run|creepy|ma|kinh dị/i));
        const isTutorial = videos.some(v => v.title.match(/how to|guide|tips|cách|hướng dẫn/i));
        const isStory = videos.some(v => v.title.match(/story|history|happened|kể|chuyện/i));

        // 2. CÔNG THỨC VIRAL (TITLE RECIPES)
        // Thay vì string tĩnh, dùng hàm builder để random hóa cấu trúc

        // A. The "Twist" Structure (Storytelling)
        if (isHorror || isStory) {
            titles.push({
                type: 'Story Twist',
                text: `I Played ${mainSubject} At 3AM... And This Happened`
            });
            titles.push({
                type: 'Emotional Hook',
                text: `The Truth About ${mainSubject} Will Break Your Heart`
            });
            titles.push({
                type: 'Negative Warning',
                text: `DO NOT Try ${mainSubject} Unless You Watch This`
            });
        }

        // B. The "Transformation/Result" Structure
        titles.push({
            type: 'Extreme Outcome',
            text: `I Simulated ${mainSubject} for 100 Days: Here's The Result`
        });

        // C. The "Authority/Secret" Structure
        titles.push({
            type: 'Insider Secret',
            text: `Why Everyone is Wrong About ${mainSubject}`
        });

        // D. The "Specific Detail" Structure (High Click Through Rate)
        // Lấy 1 tiêu đề thật làm mẫu nhưng viết lại
        if (videos.length > 0) {
            const seedTitle = videos[0].title;
            const context = seedTitle.split(' ').slice(0, 3).join(' '); // Lấy ngữ cảnh đầu câu
            titles.push({
                type: 'Context Rewrite',
                text: `${context} ... But It's Actually ${subSubject}`
            });
        }

        // E. The "Comparison" Structure
        if (entities.length >= 2) {
            titles.push({
                type: 'Face-off',
                text: `${_capitalize(entities[0])} vs ${mainSubject}: The Winner Surprised Me`
            });
        } else {
            titles.push({
                type: 'Evolution',
                text: `${mainSubject}: Then vs Now (Insane Difference)`
            });
        }

        return titles.slice(0, 5); // Trả về 5 tiêu đề tốt nhất
    }

    // --- 4. PUBLIC METHODS ---

    function processVideos(rawVideos, channelStats) {
        return rawVideos.map(v => {
            const views = parseInt(v.statistics.viewCount) || 0;
            const chanInfo = channelStats[v.snippet.channelId] || { subs: 0, country: '' };
            const subs = parseInt(chanInfo.subs) || 0;
            const vsRatio = subs > 0 ? parseFloat((views / subs).toFixed(2)) : 0;
            
            return {
                id: v.id,
                title: v.snippet.title,
                channel: v.snippet.channelTitle,
                channelId: v.snippet.channelId,
                publishedAt: v.snippet.publishedAt,
                thumbnail: v.snippet.thumbnails.medium.url,
                views: views,
                subs: subs,
                country: chanInfo.country,
                flag: _getFlagEmoji(chanInfo.country),
                vsRatio: vsRatio,
                likeCount: parseInt(v.statistics.likeCount) || 0,
                commentCount: parseInt(v.statistics.commentCount) || 0,
                tags: v.snippet.tags || []
            };
        });
    }

    function buildIntelligence(videos, seedKeyword) {
        // 1. Phân cụm thông minh (Thay thế logic cũ)
        const semanticClusters = buildSemanticClusters(videos);

        // 2. Tính toán các chỉ số phụ
        const scoreMetrics = _calculateNicheScore(videos);
        const bestTime = _findBestUploadTime(videos);
        
        // 3. Tìm King Keyword
        const kingKeyword = semanticClusters.length > 0 ? semanticClusters[0].name : seedKeyword;

        // 4. Tạo Micro Niche text
        const microNiche = semanticClusters[0] 
            ? `${semanticClusters[0].name} + ${semanticClusters[0].entities[0] || 'Viral'}` 
            : 'Đang thu thập dữ liệu...';

        return {
            clusters: semanticClusters, // Trả về object cluster đầy đủ (gồm videos, entities)
            kingKeyword: kingKeyword,
            microNiche: microNiche,
            score: scoreMetrics.score,
            rating: scoreMetrics.rating,
            ratingColor: scoreMetrics.color,
            bestTime: bestTime
        };
    }

    // --- Helpers nội bộ ---
    function _getFlagEmoji(countryCode) {
        if (!countryCode) return '';
        const codePoints = countryCode.toUpperCase().split('').map(char => 127397 + char.charCodeAt());
        return String.fromCodePoint(...codePoints);
    }

    function _calculateNicheScore(videos) {
        if (!videos.length) return { score: 0, rating: 'N/A', color: 'text-slate-500' };
        const avgVS = videos.reduce((sum, v) => sum + v.vsRatio, 0) / videos.length;
        const avgViews = videos.reduce((sum, v) => sum + v.views, 0) / videos.length;
        let vsScore = Math.min(100, (avgVS / 5) * 100); 
        let viewScore = Math.min(100, (avgViews / 50000) * 100); 
        const finalScore = Math.round((vsScore * 0.5) + (viewScore * 0.5));
        let rating = '', color = '';
        if (finalScore >= 80) { rating = 'Cực kỳ tiềm năng 🔥'; color = 'text-green-500'; }
        else if (finalScore >= 60) { rating = 'Khá ổn ✅'; color = 'text-blue-500'; }
        else if (finalScore >= 40) { rating = 'Cạnh tranh cao ⚔️'; color = 'text-yellow-500'; }
        else { rating = 'Rất khó khăn 💀'; color = 'text-red-500'; }
        return { score: finalScore, rating, color };
    }

    function _findBestUploadTime(videos) {
        const hours = new Array(24).fill(0);
        videos.forEach(v => {
            const h = new Date(v.publishedAt).getHours();
            hours[h]++;
        });
        const maxVal = Math.max(...hours);
        if (maxVal === 0) return 'N/A';
        const bestHour = hours.indexOf(maxVal);
        return `${bestHour.toString().padStart(2, '0')}:00 - ${(bestHour + 2).toString().padStart(2, '0')}:00`;
    }

    function _capitalize(str) { return str.replace(/\b\w/g, l => l.toUpperCase()); }

    // Expose public methods
    return { processVideos, buildIntelligence, generateSmartTitles };
})();