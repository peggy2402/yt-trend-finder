# 1. Dùng Image PHP 8.2 FPM chính chủ
FROM php:8.2-fpm

# 2. Cài đặt các thư viện hệ thống cần thiết và Nginx
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# 3. Xóa cache apt để giảm dung lượng image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 4. Cài đặt các PHP Extensions cần cho Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 5. Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Thiết lập thư mục làm việc
WORKDIR /var/www/yt-trend-finder

# 7. Copy toàn bộ code vào container
COPY . .

# 8. Cài đặt các gói thư viện Laravel (Dependencies)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# 9. Copy cấu hình Nginx vào đúng chỗ
COPY nginx.conf /etc/nginx/conf.d/default.conf

# 10. Cấp quyền cho script khởi động và thư mục storage
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 11. Mở cổng 80
EXPOSE 80

# 12. Chạy script khởi động
CMD ["/usr/local/bin/start.sh"]