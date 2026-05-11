#!/bin/sh
set -e

cd /var/www/html

echo "==> Running migrations (FRESH) & Seeding..."
php artisan migrate:fresh --seed --force

echo "==> Caching config & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creating storage symlink..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting services via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
