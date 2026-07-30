# syntax=docker/dockerfile:1

ARG PHP_VERSION=8.3
ARG NODE_VERSION=22

# =============================================================================
# Stage 1 — Frontend assets
# =============================================================================
FROM node:${NODE_VERSION}-alpine AS frontend

WORKDIR /build

COPY package.json package-lock.json .npmrc ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/

RUN npm run build

# =============================================================================
# Stage 2 — Composer binary
# =============================================================================
FROM composer:2 AS composer

# =============================================================================
# Stage 3 — PHP dependencies + extensions
# =============================================================================
FROM php:${PHP_VERSION}-fpm-alpine AS build

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache \
    autoconf \
    g++ \
    make \
    linux-headers \
    libzip-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    postgresql-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        zip \
        bcmath \
        mbstring \
        intl \
        opcache \
        pcntl

RUN pecl install redis \
    && docker-php-ext-enable redis

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && composer run-script post-autoload-dump

# =============================================================================
# Stage 3 — Production image
# =============================================================================
FROM php:${PHP_VERSION}-fpm-alpine AS production

ARG APP_ENV=production
ARG BUILD_DATE

LABEL org.opencontainers.image.title="GRM" \
      org.opencontainers.image.description="Gestion des Réclamations" \
      org.opencontainers.image.vendor="GRM" \
      org.opencontainers.image.created="${BUILD_DATE}" \
      org.opencontainers.image.version="1.0.0" \
      org.opencontainers.image.source="https://github.com/example/grm"

RUN apk add --no-cache \
    nginx supervisor curl \
    libzip-dev oniguruma-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev icu-dev postgresql-dev

COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/docker-php-ext-*.ini /usr/local/etc/php/conf.d/
COPY --from=build --chown=www:www /app /var/www/html
COPY --from=frontend --chown=www:www /build/public/build /var/www/html/public/build

RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www

WORKDIR /var/www/html

RUN mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
    && chown -R www:www storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/php.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && sed -i 's/^user .*/user www www;/' /etc/nginx/nginx.conf \
    && mkdir -p /var/log/supervisor /var/run/nginx /var/run/php \
    && chown -R www:www /var/log/supervisor /var/run/nginx /var/run/php

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
