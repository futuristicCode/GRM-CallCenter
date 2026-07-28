#!/bin/sh
set -e

# ─── Wait for PostgreSQL (TCP check) ──────────────────────────────────────────
if [ "$DB_CONNECTION" = "pgsql" ] && [ -n "$DB_HOST" ]; then
  echo "⏳ Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT:-5432}..."
  until php -r "
    \$host = getenv('DB_HOST'); \$port = getenv('DB_PORT') ?: '5432';
    \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
    if (\$fp) { fclose(\$fp); exit(0); } exit(1);
  " 2>/dev/null; do
    sleep 1
  done
  echo "✅ PostgreSQL is ready."
fi

# ─── Wait for Redis (TCP check) ──────────────────────────────────────────────
if [ -n "$REDIS_HOST" ]; then
  echo "⏳ Waiting for Redis at ${REDIS_HOST}:${REDIS_PORT:-6379}..."
  until php -r "
    \$host = getenv('REDIS_HOST'); \$port = getenv('REDIS_PORT') ?: '6379';
    \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
    if (\$fp) { fclose(\$fp); exit(0); } exit(1);
  " 2>/dev/null; do
    sleep 1
  done
  echo "✅ Redis is ready."
fi

# ─── Laravel bootstrap ────────────────────────────────────────────────────────
echo "🔧 Caching configuration..."
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

echo "🗄️  Running migrations..."
php /var/www/html/artisan migrate --force 2>/dev/null || true
php /var/www/html/artisan db:seed --force 2>/dev/null || true
php /var/www/html/artisan migrate:fresh --seed --force 2>/dev/null || true

# ─── Exec custom command or start supervisord ─────────────────────────────────
if [ $# -gt 0 ]; then
  echo "🚀 Executing: $*"
  exec "$@"
fi

echo "🚀 Starting php-fpm + nginx..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
