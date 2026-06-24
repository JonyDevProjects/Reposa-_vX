#!/bin/sh
set -e

# Asegura que el fichero SQLite existe (vive en el volumen persistente)
touch /var/www/html/database/database.sqlite

# Genera APP_KEY solo si no se ha definido por entorno
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(php artisan key:generate --show --no-ansi)
    export APP_KEY
fi

# Migraciones: idempotentes, Laravel registra cuáles ya se ejecutaron
php artisan migrate --force

# Cacheo de configuración y rutas (seguro de repetir en cada arranque)
php artisan config:cache
php artisan route:cache

exec "$@"
