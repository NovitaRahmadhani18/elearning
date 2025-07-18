FROM dunglas/frankenphp

# Copy your app
WORKDIR /app
COPY . /app

# Install additional PHP extensions for Laravel
RUN install-php-extensions \
    pdo_mysql \
    gd \
    intl \
    zip \
    opcache \
    redis \
    mysqli

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set proper permissions
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache
RUN chmod -R 775 /app/storage /app/bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Enable worker mode for better performance
ENV FRANKENPHP_CONFIG="worker /app/public/index.php"

ENTRYPOINT ["docker-entrypoint.sh"]

EXPOSE 80 443
