#!/bin/bash
php artisan key:generate --force --quiet
php artisan migrate --force --quiet
php artisan db:seed --force --quiet
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan app:generate-sitemap
php artisan app:generate-llms-txt
php -S 0.0.0.0:$PORT -t public/
