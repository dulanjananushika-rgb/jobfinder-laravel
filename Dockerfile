# syntax=docker/dockerfile:1
FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libsqlite3-dev libzip-dev libicu-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_sqlite mbstring bcmath exif pcntl zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr

RUN cp -n .env.example .env || true \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views storage/app/private/resumes bootstrap/cache \
    && touch database/database.sqlite \
    && php artisan key:generate --force \
    && chmod +x docker-entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 10000
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
