FROM mcr.microsoft.com/devcontainers/php:1-8.4-bookworm

# Node.js 20 (via devcontainer feature or manual install)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Remove broken Yarn repo from base image (missing GPG key)
RUN rm -f /etc/apt/sources.list.d/yarn.list

# PHP extensions needed by Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libzip-dev libicu-dev libonig-dev libxml2-dev \
        unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring xml curl zip dom gd bcmath intl sockets \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
