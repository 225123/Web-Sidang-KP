#!/bin/sh
set -e

cd /var/www/html

# Paksa ganti port 8080 dengan port dari Railway ($PORT)
# Gunakan 8080 sebagai default jika $PORT kosong
export ACTUAL_PORT=${PORT:-8080}
echo "==> Configuring Nginx to listen on port $ACTUAL_PORT..."
sed -i "s/listen 8080;/listen $ACTUAL_PORT;/" /etc/nginx/nginx.conf

echo "==> Running migrations (FRESH) & Seeding..."
php artisan migrate:fresh --seed --force

echo "==> Clearing cache to ensure fresh environment..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting services via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
