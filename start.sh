#!/bin/sh
set -e

# Ensure PORT has a default value for nginx listen
PORT=${PORT:-80}

# Generate nginx config at runtime to avoid unexpanded template issues
cat > /etc/nginx/conf.d/default.conf <<- 'NGINXCONF'
server {
  listen ${PORT};
  server_name _;

  root /var/www/public;
  index index.php index.html;

  location / {
    try_files $uri $uri/ /index.php?$query_string;
  }

  location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
  }

  client_max_body_size 100M;
}
NGINXCONF

# Export PORT for child processes just in case
export PORT

# Ensure storage permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Optional: run migrations on start (uncomment if desired)
# php artisan migrate --force

# Start php-fpm in foreground then start nginx (nginx stays in foreground)
php-fpm -F &
nginx -g 'daemon off;'
