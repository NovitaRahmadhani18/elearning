#!/bin/bash

# Set executable permission
chmod +x docker-start.sh

# Build and start containers
echo "🐳 Building Docker containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "⏳ Waiting for containers to start..."
sleep 10

# Install dependencies
echo "📦 Installing Composer dependencies..."
docker-compose exec app composer install

# Set permissions
echo "🔐 Setting file permissions..."
docker-compose exec app chown -R www:www /var/www/storage
docker-compose exec app chown -R www:www /var/www/bootstrap/cache

# Generate app key if not exists
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Create storage link
echo "🔗 Creating storage link..."
docker-compose exec app php artisan storage:link

# Run migrations and seeders
echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate --seed

echo "✅ Docker setup complete!"
echo "🌐 Application: http://localhost:8000"
echo "📊 Database: localhost:3306"
echo "🔴 Redis: localhost:6379"
