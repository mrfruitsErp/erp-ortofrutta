FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev zip \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 🔥 SOLO QUESTI (NO key generate)
RUN php artisan config:clear
RUN php artisan migrate --force
RUN php artisan db:seed --force

EXPOSE 10000

CMD php -S 0.0.0.0:10000 -t public