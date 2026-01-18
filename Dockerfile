FROM richarvey/nginx-php-fpm:latest

# Thiết lập working directory
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn
COPY . /var/www/html

# Copy file cấu hình nginx
RUN mkdir -p /var/www/html/conf/nginx
RUN cp /var/www/html/nginx-site.conf /var/www/html/conf/nginx/nginx-site.conf

# Copy và cấp quyền cho script startup
RUN mkdir -p /var/www/html/scripts
RUN cp /var/www/html/00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# Cấp quyền cho storage và cache
RUN chmod -R 775 storage/
RUN chmod -R 775 bootstrap/cache/

# Fix lỗi composer (thay vì cố gỡ prestissimo, chúng ta sẽ skip nó)
RUN composer clear-cache

# Cài đặt dependencies (sửa lỗi platform nếu có)
RUN composer install --no-interaction --optimize-autoloader --ignore-platform-reqs

# Tạo bảng sessions và cache nếu chưa có
RUN php artisan session:table --no-interaction || true
RUN php artisan cache:table --no-interaction || true

# Optimize cho production
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Cấu hình supervisor để chạy queue worker (nếu cần)
RUN mkdir -p /etc/supervisor/conf.d
RUN echo '[program:queue-worker]' > /etc/supervisor/conf.d/queue-worker.conf
RUN echo 'command=php /var/www/html/artisan queue:work --sleep=3 --tries=3' >> /etc/supervisor/conf.d/queue-worker.conf
RUN echo 'autostart=true' >> /etc/supervisor/conf.d/queue-worker.conf
RUN echo 'autorestart=true' >> /etc/supervisor/conf.d/queue-worker.conf
RUN echo 'user=www-data' >> /etc/supervisor/conf.d/queue-worker.conf
RUN echo 'redirect_stderr=true' >> /etc/supervisor/conf.d/queue-worker.conf

# Expose port 80 và 443
EXPOSE 80 443