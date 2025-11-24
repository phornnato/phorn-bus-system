#!/bin/sh
set -e

# Ensure PORT has a default value for nginx listen
PORT=${PORT:-80}
export PORT
# Substitute PORT into nginx config if template exists
if [ -f /etc/nginx/conf.d/default.conf.template ]; then
  envsubst '$PORT' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf
fi

# Ensure storage permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Optional: run migrations on start (uncomment if desired)
# php artisan migrate --force

# Start php-fpm in foreground then start nginx (nginx stays in foreground)
php-fpm -F &
nginx -g 'daemon off;'
