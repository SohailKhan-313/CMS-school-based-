#!/bin/bash
set -e

echo "==> Preparing storage directories..."
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

# If SQLite is used, ensure database file exists
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    echo "==> Ensuring SQLite database file exists..."
    touch database/database.sqlite
    chmod 775 database/database.sqlite
fi

echo "==> Linking public storage..."
php artisan storage:link --force || true

echo "==> Running database migrations..."
php artisan migrate --force

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "==> Seeding database with roles, accounts, and dummy data..."
    php artisan db:seed --force
fi

echo "==> Optimizing configuration, route, and view caches..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting web application on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
