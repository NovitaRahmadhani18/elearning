FROM composer:2.7 AS vendor

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# -----------------------------------------------------------------------------

# Tahap 2: Build image produksi final
FROM dunglas/frankenphp:1-php8.3-alpine AS final

# Install ekstensi PHP yang dibutuhkan untuk SQLite
RUN docker-php-ext-install pdo_sqlite

WORKDIR /app

# Copy Caddyfile konfigurasi server
COPY frankenphp/caddy/Caddyfile /etc/caddy/Caddyfile

# Copy file aplikasi
COPY . .

# Copy direktori vendor dari tahap 'vendor'
COPY --from=vendor /app/vendor /app/vendor

# Optimisasi Laravel untuk produksi
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Buat file database SQLite kosong sebelum mengatur permission
RUN mkdir -p database && touch database/database.sqlite

# Setel kepemilikan file agar server (FrankenPHP) bisa menulis
RUN chown -R frankenphp:frankenphp storage bootstrap/cache database/database.sqlite

# Ganti user ke non-root untuk keamanan
USER frankenphp

# Expose port yang digunakan oleh Caddy (FrankenPHP)
EXPOSE 80 443 443/udp

# Perintah default untuk menjalankan server
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
