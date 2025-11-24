
FROM php:8.2-fpm

# Install system dependencies and nginx for HTTP
RUN apt-get update && apt-get install -y \
    git curl unzip libpq-dev libonig-dev libzip-dev zip nginx gettext-base procps \
    && docker-php-ext-install pdo pdo_mysql mbstring zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy app files
COPY . .

# Ensure composer runs non-interactively and install dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-interaction --optimize-autoloader --prefer-dist || true

# Ensure correct permissions for Laravel
RUN chown -R www-data:www-data /var/www || true
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Copy nginx config template and start script (added below)
COPY docker/nginx/default.conf.template /etc/nginx/conf.d/default.conf.template
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Expose the HTTP port and allow platform to set PORT
EXPOSE 80
ENV PORT=80

# Clear caches (safe at build time)
RUN php artisan config:clear || true && \
    php artisan route:clear || true && \
    php artisan view:clear || true

# Start script will substitute $PORT into nginx template and run php-fpm + nginx
CMD ["sh", "/start.sh"]

