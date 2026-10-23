#!/bin/sh
set -e
cd /var/www/html
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force   # idempotent: creates admin + products only if missing
chown -R www-data:www-data storage bootstrap/cache
exec apache2-foreground
