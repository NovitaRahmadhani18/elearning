#!/bin/bash
set -e

# Laravel setup
if [ ! -f /app/.env ]; then
    echo "Creating .env file..."
    cp /app/.env.example /app/.env
fi

# Generate app key if not exists
if ! grep -q "APP_KEY=" /app/.env || grep -q "APP_KEY=$" /app/.env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Optimize for production
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start FrankenPHP
echo "Starting FrankenPHP..."
exec frankenphp run --config /etc/caddy/Caddyfile
