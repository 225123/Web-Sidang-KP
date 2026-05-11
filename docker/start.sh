#!/bin/sh
set -e

cd /var/www/html

echo "==> Running migrations (FRESH) & Seeding..."
php artisan migrate:fresh --seed --force

echo "==> Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting services via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
