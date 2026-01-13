#!/bin/bash

# Cache config để chạy nhanh hơn
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Chạy Nginx & PHP-FPM
nginx
php-fpm