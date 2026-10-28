#!/bin/sh
set -e

# Ensure SQLite file exists
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/app/private/resumes
mkdir -p /var/www/html/bootstrap/cache

# Run database migrations and idempotent seeder
php artisan migrate --force
php artisan db:seed --force

# Cache configuration, routes, and views for lightning performance in production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "JobFinder is ready and listening on port ${PORT:-10000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
