# =============================================================================
# Stage 1: Build frontend assets
# =============================================================================
FROM node:22-alpine AS frontend
WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci --no-audit
COPY vite.config.js tailwind.config.js postcss.config.js .npmrc ./
COPY resources/ resources/
RUN npm run build

# =============================================================================
# Stage 2: Install PHP dependencies + compile extensions
# =============================================================================
FROM php:8.3-fpm-alpine AS php-deps
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN apk add --no-cache \
    libzip-dev oniguruma-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev icu-dev postgresql-dev \
    linux-headers autoconf g++ make
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql pgsql zip bcmath mbstring intl opcache pcntl
ADD https://github.com/phpredis/phpredis/archive/refs/tags/6.1.0.tar.gz /tmp/redis.tar.gz
RUN tar -xzf /tmp/redis.tar.gz -C /tmp \
    && cd /tmp/phpredis-6.1.0 \
    && phpize && ./configure --enable-redis \
    && make -j$(nproc) && make install \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/redis*
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && composer run-script post-autoload-dump

# =============================================================================
# Stage 3: Production image
# =============================================================================
FROM php:8.3-fpm-alpine AS production

RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www

RUN apk add --no-cache \
    nginx supervisor curl \
    libzip-dev oniguruma-dev libpng-dev libjpeg-turbo-dev \
    freetype-dev icu-dev postgresql-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql pgsql zip bcmath mbstring intl opcache pcntl

COPY --from=php-deps /usr/local/lib/php/extensions/no-debug-non-zts-20230831/redis.so \
    /usr/local/lib/php/extensions/no-debug-non-zts-20230831/redis.so
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

RUN rm -f /usr/local/etc/php-fpm.d/docker.conf /usr/local/etc/php-fpm.d/www.conf
RUN mkdir -p /usr/local/etc/php-fpm.d \
    && printf '%s\n' \
        '[www]' \
        'listen = /var/run/php-fpm.sock' \
        'listen.owner = www' \
        'listen.group = www' \
        'user = www' \
        'group = www' \
        'pm = dynamic' \
        'pm.max_children = 20' \
        'pm.start_servers = 5' \
        'pm.min_spare_servers = 3' \
        'pm.max_spare_servers = 10' \
        'pm.max_requests = 500' \
        'clear_env = no' \
    > /usr/local/etc/php-fpm.d/www.conf

RUN printf '%s\n' \
    'opcache.enable=1' \
    'opcache.memory_consumption=256' \
    'opcache.interned_strings_buffer=16' \
    'opcache.max_accelerated_files=20000' \
    'opcache.validate_timestamps=0' \
    'opcache.save_comments=1' \
    'opcache.jit=1255' \
    'opcache.jit_buffer_size=128M' \
  > /usr/local/etc/php/conf.d/opcache.ini

RUN printf '%s\n' \
    'upload_max_filesize = 64M' \
    'post_max_size = 64M' \
    'memory_limit = 512M' \
    'max_execution_time = 300' \
  > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY --from=php-deps --chown=www:www /app /var/www/html
COPY --from=frontend --chown=www:www /build/public/build /var/www/html/public/build

RUN mkdir -p storage/framework/{cache,sessions,testing,views} storage/logs bootstrap/cache \
    && chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache

COPY --chown=root:root docker/nginx.conf /etc/nginx/http.d/default.conf
COPY --chown=root:root docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY --chown=root:root docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && sed -i 's/^user .*/user www www;/' /etc/nginx/nginx.conf \
    && mkdir -p /var/log/supervisor /var/run/nginx /var/run/php \
    && chown -R www:www /var/log/supervisor /var/run/nginx /var/run/php \
    && chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
