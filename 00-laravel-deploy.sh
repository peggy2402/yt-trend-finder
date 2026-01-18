#!/usr/bin/env bash
echo "Running Deploy Script..."

# Set environment
export APP_ENV=production
export APP_DEBUG=false

# Fix permissions
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear cache cũ
echo "Clearing old cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Chạy migrations
echo "Running Migrations..."
php artisan migrate --force

# Tạo key nếu chưa có
if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env | cut -d= -f2)" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Optimize cho production
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Tạo symbolic link cho storage (nếu cần)
echo "Creating storage link..."
php artisan storage:link --force

# Tạo bảng sessions và cache nếu chưa tồn tại
echo "Creating session and cache tables if not exist..."
php artisan session:table --no-interaction 2>/dev/null || true
php artisan cache:table --no-interaction 2>/dev/null || true

# Chạy migration lại để đảm bảo bảng sessions được tạo
php artisan migrate --force

# Restart queue worker (nếu đang chạy)
echo "Restarting queue workers..."
php artisan queue:restart 2>/dev/null || true

echo "Deployment completed!"