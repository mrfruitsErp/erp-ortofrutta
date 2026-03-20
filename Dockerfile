FROM php:8.2-cli

WORKDIR /app

# Dipendenze sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia progetto
COPY . .

# Installa Laravel
RUN composer install --no-dev --optimize-autoloader

# Porta
EXPOSE 10000

# Avvio
CMD php artisan config:clear && php artisan cache:clear && php artisan serve --host=0.0.0.0 --port=10000