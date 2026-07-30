#!/bin/sh
set -e

# ─── Attente PostgreSQL ──────────────────────────────────────────────────────
if [ "${DB_CONNECTION}" = "pgsql" ] && [ -n "${DB_HOST}" ]; then
  echo "Waiting for PostgreSQL at ${DB_HOST}:${DB_PORT:-5432}..."
  until php -r "
    \$host = getenv('DB_HOST');
    \$port = getenv('DB_PORT') ?: '5432';
    \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
    if (\$fp) { fclose(\$fp); exit(0); } exit(1);
  " 2>/dev/null; do
    sleep 1
  done
  echo "PostgreSQL is ready."
fi

# ─── Attente Redis ───────────────────────────────────────────────────────────
if [ -n "${REDIS_HOST}" ]; then
  echo "Waiting for Redis at ${REDIS_HOST}:${REDIS_PORT:-6379}..."
  until php -r "
    \$host = getenv('REDIS_HOST');
    \$port = getenv('REDIS_PORT') ?: '6379';
    \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
    if (\$fp) { fclose(\$fp); exit(0); } exit(1);
  " 2>/dev/null; do
    sleep 1
  done
  echo "Redis is ready."
fi

# ─── Ensure storage directories exist ─────────────────────────────────────────
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache

# ─── Cache Laravel (sauf en local) ───────────────────────────────────────────
if [ "${APP_ENV}" != "local" ]; then
  echo "Caching configuration..."
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
fi

# ─── Storage link ────────────────────────────────────────────────────────────
if [ ! -L public/storage ]; then
  php artisan storage:link --force 2>/dev/null || true
fi

# ─── Migrations (--isolated = verrou DB pour éviter les races sur K8s) ──────
echo "Running pending migrations..."
php artisan migrate --force --isolated || php artisan migrate --force

# ─── Commande personnalisée ou Supervisord ───────────────────────────────────
if [ $# -gt 0 ]; then
  exec "$@"
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
