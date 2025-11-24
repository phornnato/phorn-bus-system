# ---------- build stage ----------
FROM php:8.2-fpm AS builder

# system deps
RUN apt-get update && apt-get install -y \
    git zip unzip curl libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip gd

# Bring composer from official composer image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# copy composer files first for better cache
COPY composer.json composer.lock /app/
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# copy rest of app
COPY . /app

# run optimizations
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache || true

# ---------- runtime stage ----------
FROM php:8.2-fpm

# Install runtime deps required by your app (if any)
RUN apt-get update && apt-get install -y libpng-dev libonig-dev libxml2-dev libzip-dev \
 && docker-php-ext-install pdo pdo_mysql gd

WORKDIR /app

# copy built app from builder
COPY --from=builder /app /app

# ensure storage + bootstrap cache permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# expose php-fpm socket port (if using nginx sidecar)
EXPOSE 9000

# default command runs php-fpm
CMD ["php-fpm"]
