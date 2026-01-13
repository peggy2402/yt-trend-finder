#!/usr/bin/env bash
echo "Running Deploy Script..."

# Xóa sạch cache cũ (fix lỗi 404 trang con)
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache lại những thứ an toàn
php artisan config:cache
php artisan view:cache

# Migrate database
echo "Running Migrations..."
php artisan migrate --force