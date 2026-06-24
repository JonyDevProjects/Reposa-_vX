# ---------- Etapa 1: build de assets front-end (Vite + Bootstrap/SCSS) ----------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# ---------- Etapa 2: aplicación (PHP-FPM + Nginx + Supervisor) ----------
FROM php:8.4-fpm AS app

RUN apt-get update && apt-get install -y --no-install-recommends \
        libsqlite3-dev libzip-dev libicu-dev \
        zip unzip git nginx supervisor \
    && docker-php-ext-install pdo_sqlite bcmath intl zip \
    && docker-php-ext-enable opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Capa cacheable: solo dependencias PHP
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Resto del código de la aplicación
COPY . .
RUN rm -f public/hot

# Assets ya compilados en la etapa "frontend"
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
       storage/logs bootstrap/cache database \
    && touch database/database.sqlite \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/app.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/app.conf"]
