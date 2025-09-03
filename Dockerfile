FROM composer:2.7 AS vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-interaction --optimize-autoloader --no-scripts

FROM node:20-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
COPY . .
COPY --from=vendor /app/vendor /app/vendor
RUN npm install
RUN npm run build

FROM dunglas/frankenphp:1-php8.4-alpine AS final

RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    sqlite-dev \
    && docker-php-ext-install -j$(nproc) zip pdo_sqlite \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

WORKDIR /app
COPY frankenphp/caddy/Caddyfile /etc/caddy/Caddyfile
COPY . .

COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public /app/public

RUN ls -la

# # Buat file database SQLite kosong sebelum mengatur permission
# RUN mkdir -p database && touch database/database.sqlite

# RUN cp .env.example .env \
#     && php artisan key:generate \
#     && php artisan storage:link

RUN php artisan storage:link


# Expose port yang digunakan oleh Caddy (FrankenPHP)
EXPOSE 80 443 443/udp

# Perintah default untuk menjalankan server
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
