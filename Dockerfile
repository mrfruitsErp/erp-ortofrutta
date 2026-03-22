FROM php:8.2-cli

# Install dipendenze
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev zip \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Cartella lavoro
WORKDIR /var/www

# Copia progetto
COPY . .

# Install Laravel
RUN composer install --no-dev --optimize-autoloader

# Permessi
RUN mkdir -p storage/framework/views \
    storage/framework/cache \
    storage/framework/sessions \
    bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Porta
EXPOSE 10000

# 🔥 AVVIO CORRETTO (QUI STA LA CHIAVE)
CMD php artisan config:clear && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    php -S 0.0.0.0:10000 -t public