FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y git unzip libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

CMD sh -c "php artisan migrate --force && php artisan db:seed --class=AdminUserSeeder --force && php artisan db:seed --class=PadraoUserSeeder --force && php artisan config:cache && php artisan serve --host=0.0.0.0 --port=\${PORT:-10000}"
