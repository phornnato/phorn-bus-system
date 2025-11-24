#!/bin/sh

# Start Nginx
nginx

# Copy .env if missing
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate Laravel APP_KEY
php artisan key:generate --force

# Ensure caches are cleared
php artisan config:clear
php artisan route:clear
php artisan view:clear

exec "$@"
