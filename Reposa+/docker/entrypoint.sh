#!/bin/bash
set -e

# Asegura que el fichero SQLite existe (solo si se usa SQLite)
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch /var/www/html/database/database.sqlite
fi

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
