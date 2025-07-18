FROM dunglas/frankenphp

# Install system dependencies
RUN apt-get update && apt-get install -y \
    sqlite3 \
    && rm -rf /var/lib/apt/lists/*

# Install additional PHP extensions for Laravel
RUN install-php-extensions \
    pdo_sqlite \
    sqlite3 \
    gd \
    intl \
    zip \
    opcache \
    redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy your app
WORKDIR /app
COPY . /app

# Install dependencies
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Generate optimized autoloader
RUN composer dump-autoload --optimize

# Set proper permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Enable worker mode for better performance (disabled for debugging)
# ENV FRANKENPHP_CONFIG="worker /app/public/index.php"

ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 80 443
