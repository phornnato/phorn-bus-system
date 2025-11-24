FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    zip \
    unzip \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath

# Setup working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Copy Nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permission
RUN chown -R www-data:www-data /var/www

# Expose port 80 for Render
EXPOSE 80

# Start Nginx + PHP-FPM together
CMD service nginx start && php-fpm
