FROM composer:2.7 AS vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

FROM node:20-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
COPY . .
RUN npm install
RUN npm run build

FROM dunglas/frankenphp:1-php8.3-alpine AS final

RUN apk add --no-cache sqlite-dev \
    && docker-php-ext-install pdo_sqlite

WORKDIR /app
COPY frankenphp/caddy/Caddyfile /etc/caddy/Caddyfile
COPY . .

COPY --from=vendor /app/vendor /app/vendor

RUN ls -la

# Buat file database SQLite kosong sebelum mengatur permission
RUN mkdir -p database && touch database/database.sqlite

# Expose port yang digunakan oleh Caddy (FrankenPHP)
EXPOSE 80 443 443/udp

# Perintah default untuk menjalankan server
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
