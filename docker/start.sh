#!/bin/sh
set -e

php artisan storage:link || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan package:discover --ansi || true
php artisan migrate --force || true

php-fpm -D
exec nginx -g "daemon off;"