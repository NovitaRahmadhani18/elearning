#!/bin/bash
set -e

echo "🚀 Starting FrankenPHP E-Learning Platform with SQLite..."

# Check if vendor directory exists
if [ ! -d "/app/vendor" ]; then
    echo "Installing Composer dependencies..."
    cd /app
    composer install --optimize-autoloader --no-dev
fi

# Laravel setup
if [ ! -f /app/.env ]; then
    echo "Creating .env file..."
    cp /app/.env.example /app/.env
fi

# Generate app key if not exists
if ! grep -q "APP_KEY=" /app/.env || grep -q "APP_KEY=$" /app/.env; then
    echo "Generating application key..."
    cd /app
    php artisan key:generate --force
fi

# Create SQLite database directory and file
echo "Setting up SQLite database..."
mkdir -p /app/database
if [ ! -f "/app/database/database.sqlite" ]; then
    echo "Creating SQLite database file..."
    touch /app/database/database.sqlite
    chmod 664 /app/database/database.sqlite
    chown www-data:www-data /app/database/database.sqlite
fi

# Run migrations
echo "Running database migrations..."
cd /app
php artisan migrate
echo "Database migrations completed."

# Seed database if in development
if [ "${APP_ENV:-local}" != "production" ]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Optimize for production
if [ "${APP_ENV:-local}" = "production" ]; then
    echo "Optimizing application for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Start FrankenPHP
echo "Starting FrankenPHP..."
exec frankenphp run --config /app/Caddyfile
