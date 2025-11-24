#!/bin/sh

# Copy .env if missing
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate app key only if missing
if ! grep -q "APP_KEY=base64" .env; then
    php artisan key:generate
fi

php artisan config:clear
php artisan route:clear
php artisan view:clear

exec "$@"
