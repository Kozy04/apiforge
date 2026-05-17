FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    sqlite3 \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && npm ci \
    && npm run build \
    && php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan db:seed --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan app:generate-sitemap \
    && php artisan app:generate-llms-txt

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]
