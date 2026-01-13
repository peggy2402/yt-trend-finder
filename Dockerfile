FROM richarvey/nginx-php-fpm:latest

# 1. Copy TOÀN BỘ thư mục hiện tại vào trong container
COPY . /var/www/html

# 2. Config Environment
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV COMPOSER_ALLOW_SUPERUSER=1

# 3. Di chuyển file cấu hình vào đúng chỗ bằng lệnh Linux (cp)
# Vì file đã được COPY ở bước 1, nên lệnh cp này chắc chắn chạy được
RUN cp /var/www/html/nginx-site.conf /var/www/html/conf/nginx/nginx-site.conf
RUN cp /var/www/html/00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# 4. Cài đặt dependencies
RUN composer install --no-dev --working-dir=/var/www/html