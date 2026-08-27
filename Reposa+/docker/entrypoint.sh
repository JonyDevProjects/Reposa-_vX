#!/bin/bash
set -e

echo "==> Waiting for MySQL..."
until mysqladmin ping -h mysql -u root -proot --skip-ssl --silent 2>/dev/null; do
  sleep 2
done
echo "==> MySQL is ready."

# Clear stale config cache so fresh .env values take effect
php artisan config:clear 2>/dev/null || true

if [ -z "$APP_KEY" ]; then
  echo "==> Generating APP_KEY..."
  php artisan key:generate --force
fi

echo "==> Running migrations and seeding..."
php artisan migrate:fresh --seed --force

echo "==> Creating storage link..."
php artisan storage:link --force 2>/dev/null || true

echo "==> Caching config and routes..."
php artisan config:cache
php artisan route:cache

echo "==> Starting PHP-FPM..."
exec php-fpm
