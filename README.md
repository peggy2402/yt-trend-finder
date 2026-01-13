# ZTGroup Analytics Pro - YouTube Market Hunter

Chào mừng bạn đến với **ZTGroup Analytics Pro** — công cụ phân tích thị trường YouTube chuyên sâu giúp bạn tìm kiếm **"Long mạch" nội dung**, soi đối thủ và phát hiện các ngách tiềm năng (**Micro-Niches**).

---

## 🚀 Tính năng nổi bật

### 🔑 Đa API Key & Failover
- Hỗ trợ nhập nhiều API Key cùng lúc.
- Tự động chuyển Key khi gặp lỗi quota (403) hoặc lỗi mạng.
- Đảm bảo quá trình quét dữ liệu không bị gián đoạn.

### 🔍 Deep Scan (Quét sâu)
- Quét tới **5 trang kết quả tìm kiếm liên tiếp**.
- Thu thập tối đa **250 video** cho mỗi lần tìm kiếm.
- Cho cái nhìn toàn diện và chính xác hơn về thị trường.

### 🎯 Bộ lọc thông minh
- Lọc theo **Quốc gia**: Tier 1, Tier 2, Châu Á, Âu, Mỹ...
- Lọc theo **Định dạng**: Video dài / Shorts.
- Lọc theo **Thời gian**: Giờ vàng, Ngày, Tuần, Tháng...

### 📊 Phân tích chuyên sâu
- **King Keyword**: Xác định từ khóa xuất hiện nhiều nhất trong ngách.
- **Micro-Niche**: Tự động gom nhóm các chủ đề nhỏ tiềm năng.
- **Upload Heatmap**: Biểu đồ nhiệt hiển thị khung giờ đăng video hiệu quả.
- **Competitor Spy**: Danh sách các kênh đang thống trị trong ngách.

### 🖥️ Giao diện trực quan
- Bảng dữ liệu chi tiết, dễ đọc.
- Hiển thị **cờ quốc gia**, phân loại **Tier**.
- Đầy đủ chỉ số: views, subscribers, tỷ lệ tăng trưởng...

---

## 🛠️ Cài đặt & Sử dụng

### 1. Cấu hình ban đầu

Để sử dụng công cụ, bạn cần ít nhất một **YouTube Data API Key v3** từ Google Cloud Console.

Các bước thực hiện:

1. Mở ứng dụng (truy cập đường dẫn `/` trên trình duyệt).
2. Nhấn nút **"Nhập API Key"** ở góc trên bên phải.
3. Nhập danh sách API Key (mỗi Key một dòng).
4. Nhấn **"Lưu Cấu Hình"**.

> 💡 **Mẹo:** Nên tạo từ **3–5 API Key** từ các dự án Google Cloud khác nhau để tránh hết quota.

---

### 2. Cách tìm kiếm "Long mạch"

1. **Nhập từ khóa**  
   Ví dụ: `street food`, `asmr`, `crypto`...

2. **Chọn thị trường**  
   Ví dụ: US, VN, Global...

3. **Tùy chỉnh bộ lọc (không bắt buộc)**  
   - Nhấn **"Tùy chỉnh Bộ lọc & Thời gian"**
   - Chọn khoảng thời gian (VD: Tháng này)
   - Chọn định dạng (Shorts / Video dài)
   - Bật **Deep Scan** nếu cần nhiều dữ liệu hơn (tốn quota hơn)

4. Nhấn nút **PHÂN TÍCH** và chờ kết quả.

---

## 📖 Cách đọc hiểu các chỉ số

### 🌍 Tier Quốc gia
Phân loại dựa trên mức độ giàu có (RPM quảng cáo):

- **Tier 1 💰**: Mỹ, Anh, Úc, Canada... → Nên ưu tiên nếu muốn tối đa hóa thu nhập.
- **Tier 2 📈**: Pháp, Nhật, Hàn... → Tiềm năng tốt.
- **Tier 3 🌏**: Các quốc gia còn lại.

---

### 📈 Success Rate (V/S Ratio)

Công thức:  
> **Views / Subscribers**

Ý nghĩa:
- Nếu **> 100% (hoặc > 1x)** → Video đang viral vượt trội so với lượng fan hiện có.
- Đây là tín hiệu cực tốt để:
  - Nhận diện chủ đề tiềm năng
  - Phân tích và học theo cấu trúc nội dung

---

### 🧩 Micro-Niche

- Hiển thị dưới dạng các thẻ màu xanh/đỏ ở khu vực giữa giao diện.
- Bấm vào từng Micro-Niche để xem:
  - Các video nổi bật
  - Các kênh đang làm tốt trong ngách nhỏ đó

→ Đây chính là nơi giúp bạn tìm ra **"mỏ vàng nội dung"** mà đối thủ chưa khai thác triệt để.

---

## ⚠️ Lưu ý quan trọng

### 🔋 API Quota
- Mỗi API Key miễn phí: **10.000 đơn vị/ngày**.
- Mỗi lần bật **Deep Scan** tiêu tốn khoảng **500–600 đơn vị**.
- Nếu dùng thường xuyên, hãy chuẩn bị nhiều API Key.

### ❌ Lỗi 403
Nếu gặp lỗi 403:
- Kiểm tra xem bạn đã bật thư viện **YouTube Data API v3** trong Google Cloud Console chưa.

### 📡 Dữ liệu
- Tất cả dữ liệu được lấy **thời gian thực từ YouTube**.
- Không qua lưu trữ trung gian.

---

## 👨‍💻 Tác giả

Phát triển bởi **ZTGroup Analytics Team**

> Nếu bạn thấy công cụ hữu ích, hãy ⭐ project và chia sẻ cho cộng đồng!

