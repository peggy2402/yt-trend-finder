FROM richarvey/nginx-php-fpm:latest

# Copy toàn bộ code vào container
COPY . /var/www/html

# Cấu hình đường dẫn và các biến môi trường cơ bản cho Image
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy cấu hình Nginx riêng
COPY nginx-site.conf /var/www/html/conf/nginx/nginx-site.conf

# Copy và cấp quyền chạy cho script deploy
COPY 00-laravel-deploy.sh /var/www/html/scripts/00-laravel-deploy.sh
RUN chmod +x /var/www/html/scripts/00-laravel-deploy.sh

# Cài đặt các thư viện PHP
RUN composer install --no-dev --working-dir=/var/www/html