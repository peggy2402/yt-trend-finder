#!/usr/bin/env bash
echo "Running Deploy Script..."

# Cache cấu hình để tăng tốc độ
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Chạy migrate database (Force để chạy trên production)
echo "Running Migrations..."
php artisan migrate --force