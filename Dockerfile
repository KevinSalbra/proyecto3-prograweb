FROM node:20 AS node_builder

WORKDIR /app

COPY package*.json ./
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm install

COPY . .

RUN npm run build


FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install \
        pdo_sqlite \
        mbstring \
        zip \
        bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=node_builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p \
    database \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    public/images/productos \
    public/images/categorias \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache database public/images

EXPOSE 10000

CMD php artisan optimize:clear && \
    php artisan migrate:fresh --seed --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}