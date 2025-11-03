# syntax=docker/dockerfile:1.7

# ---------- Base images ----------
FROM composer:2 AS composer_base
FROM node:20-alpine AS node_base

# ---------- Build stage ----------
FROM php:8.3-fpm-alpine AS build

# Install system deps
RUN apk add --no-cache git zip unzip icu-dev oniguruma-dev libzip-dev libpng-dev libjpeg-turbo-dev libwebp-dev libpq-dev bash

# PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
  && docker-php-ext-install -j$(nproc) intl mbstring zip gd pdo pdo_pgsql opcache

# Copy composer and node
COPY --from=composer_base /usr/bin/composer /usr/bin/composer
COPY --from=node_base /usr/local/bin/node /usr/local/bin/node
COPY --from=node_base /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
  && ln -s /usr/local/lib/node_modules/corepack/dist/corepack.js /usr/local/bin/corepack \
  && corepack enable

WORKDIR /var/www/html

# Copy composer files and install deps
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-ansi --no-interaction --no-progress --no-scripts --optimize-autoloader

# Copy app source
COPY . .

# Build assets with vite (if present)
RUN if [ -f package.json ]; then \
      npm ci --no-audit --no-fund; \
      npm run build; \
    fi

# Laravel optimize (no .env needed for these compile steps)
RUN php artisan vendor:publish --tag=laravel-assets --force || true \
 && php artisan view:cache || true \
 && php artisan route:cache || true \
 && php artisan config:cache || true

# ---------- Runtime stage ----------
FROM nginx:1.27-alpine AS runtime

# Install PHP-FPM runtime
RUN apk add --no-cache php83 php83-fpm php83-opcache php83-pdo pgsql-client php83-pdo_pgsql php83-mbstring php83-intl php83-zip php83-gd php83-session php83-xml php83-fileinfo bash curl

# Configure PHP-FPM
RUN mkdir -p /run/php
COPY --from=build /usr/local/etc/php/conf.d /etc/php83/conf.d

# Copy application from build
WORKDIR /var/www/html
COPY --from=build /var/www/html /var/www/html

# Nginx config
COPY nginx/nginx.conf /etc/nginx/nginx.conf

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Ensure correct permissions for storage and bootstrap/cache
RUN chown -R nginx:nginx storage bootstrap/cache \
 && chmod -R ug+rwX storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    PHP_FPM_SOCK=/run/php/php-fpm.sock \
    PORT=8080

USER nginx

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
CMD ["sh", "-c", \
  "php-fpm83 --nodaemonize --fpm-config /etc/php83/php-fpm.conf & nginx -g 'daemon off;'" \
]
