FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    curl unzip sqlite3 libsqlite3-dev nodejs npm \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN cp .env.example .env \
    && composer install --no-dev --optimize-autoloader \
    && php artisan key:generate --force \
    && npm ci && npm run build \
    && php artisan migrate --force \
    && php artisan db:seed --force

RUN php artisan app:generate-sitemap && php artisan app:generate-llms-txt

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
