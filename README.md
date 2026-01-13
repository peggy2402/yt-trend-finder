# ZTGroup Analytics Beta v0.1.3 — YouTube Market Hunter

Công cụ phân tích thị trường YouTube chuyên sâu giúp bạn tìm ra **"long mạch" nội dung**, soi đối thủ, và phát hiện **micro-niche tiềm năng** để tối ưu chiến lược tăng trưởng kênh.

> Phù hợp cho: Creator, Affiliate Marketer, SEO YouTube, Growth Hacker, Researcher nội dung.

---

## 🚀 Tính năng nổi bật

### 🔑 Đa API Key & Failover thông minh

-   Nhập **nhiều YouTube Data API Key** cùng lúc
-   Tự động **chuyển key khi gặp lỗi quota (403) hoặc lỗi mạng**
-   Giúp quá trình quét dữ liệu **không bị gián đoạn**

### 🔍 Deep Scan (Quét sâu)

-   Lấy dữ liệu từ **tối đa 5 trang kết quả tìm kiếm (~250 video)**
-   Cho cái nhìn toàn diện hơn về thị trường
-   Phù hợp khi nghiên cứu niche nghiêm túc

### 🎯 Bộ lọc thông minh

-   Lọc theo **Quốc gia / Khu vực** (Tier 1, Tier 2, Châu Á, Âu, Mỹ…)
-   Lọc theo **Định dạng** (Video dài / Shorts)
-   Lọc theo **Thời gian đăng** (Giờ vàng, Ngày, Tuần, Tháng…)

### 📊 Phân tích chuyên sâu

-   **King Keyword** – tìm từ khóa xuất hiện nhiều nhất
-   **Micro-Niche Finder** – tự động gom nhóm chủ đề nhỏ tiềm năng
-   **Upload Heatmap** – biểu đồ nhiệt khung giờ đăng hiệu quả
-   **Competitor Spy** – danh sách các kênh đang thống trị ngách

### 🧠 Giao diện trực quan

-   Bảng dữ liệu chi tiết
-   Hiển thị **cờ quốc gia**, phân loại **Tier**
-   Chỉ số rõ ràng: views, subs, tỉ lệ viral…

---

## 🛠️ Cài đặt & Sử dụng

### 1. Chuẩn bị API Key

Bạn cần ít nhất **1 YouTube Data API v3 Key** từ Google Cloud Console.

> Gợi ý: Nên tạo **3–5 API Key từ nhiều project khác nhau** để tránh hết quota.

---

### 2. Cấu hình ban đầu

1. Mở ứng dụng tại đường dẫn `/`
2. Nhấn **"Nhập API Key"** (góc trên phải)
3. Dán danh sách API Key (mỗi dòng 1 key)
4. Nhấn **"Lưu cấu hình"**

---

### 3. Cách tìm "Long mạch" nội dung

1. Nhập **từ khóa ngách** (vd: `street food`, `crypto`, `asmr`…)
2. Chọn **thị trường mục tiêu** (US, VN, Global…)
3. (Tuỳ chọn) Nhấn **"Tùy chỉnh bộ lọc & thời gian"**:
    - Chọn khoảng thời gian (tuần/tháng gần nhất)
    - Chọn định dạng (Shorts / Video dài)
    - Bật **Deep Scan** nếu muốn phân tích sâu hơn
4. Nhấn **PHÂN TÍCH** và chờ kết quả

---

## 📚 Cách đọc hiểu dữ liệu

### 🌍 Tier quốc gia

| Tier      | Ý nghĩa           | Ví dụ               |
| --------- | ----------------- | ------------------- |
| Tier 1 💰 | RPM cao – ưu tiên | Mỹ, Anh, Úc, Canada |
| Tier 2 📈 | RPM khá           | Nhật, Hàn, Pháp     |
| Tier 3 🌏 | RPM thấp hơn      | Các nước còn lại    |

> Nếu mục tiêu là kiếm tiền → nên tập trung Tier 1

---

### 📈 Success Rate (V/S Ratio)

Công thức:

```
Views / Subscribers
```

| Tỷ lệ  | Ý nghĩa                                           |
| ------ | ------------------------------------------------- |
| > 1.0x | Video đang viral vượt fanbase → rất đáng học theo |
| < 1.0x | Nội dung chưa đủ bứt phá                          |

---

### 🧩 Micro-Niche

Các thẻ màu ở giữa giao diện đại diện cho các **ngách nhỏ tiềm năng**.

Bạn có thể:

-   Click để xem chi tiết video
-   Phân tích kênh đang làm tốt trong ngách đó
-   Dùng làm **ý tưởng nội dung hoặc chiến lược clone**

---

## ⚠️ Lưu ý quan trọng

### 🔋 API Quota

-   Mỗi API Key miễn phí: **10.000 units/ngày**
-   1 lượt Deep Scan ≈ **500–600 units**
-   Dùng thường xuyên → nên có nhiều key

### ❌ Lỗi 403

Nếu gặp lỗi 403:

-   Kiểm tra đã bật **YouTube Data API v3** trong Google Cloud Console chưa

### 🔒 Quyền riêng tư

-   Tất cả dữ liệu được lấy **trực tiếp từ YouTube realtime**
-   Hệ thống **không lưu trữ dữ liệu người dùng**

---

## 🧑‍💻 Định hướng phát triển (Roadmap)

-   [ ] Export báo cáo PDF
-   [ ] Lưu lịch sử phân tích
-   [ ] Gợi ý tiêu đề AI
-   [ ] Phân tích CTR thumbnail
-   [ ] Chrome Extension

---

## 👨‍💻 Phát triển bởi

**ZTGroup Analytics Team**  
YouTube Market Intelligence Platform

> Nếu bạn đang nghiêm túc với YouTube – đây không phải tool chơi cho vui 😉
