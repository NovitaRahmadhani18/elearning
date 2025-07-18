#!/bin/bash

# Simple SQLite deployment script untuk FrankenPHP E-Learning
set -e

echo "🚀 Deploying E-Learning Platform with FrankenPHP + SQLite..."

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# Domain parameter
DOMAIN=${1:-localhost}

# Create environment file
print_status "Membuat file environment untuk SQLite..."
cat > .env << 'ENVEOF'
APP_NAME="E-Learning Platform"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

REDIS_HOST=redis
REDIS_PORT=6379
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

SERVER_NAME=localhost

# Level Up & Trix
LEVEL_UP_USER_FOREIGN_KEY=user_id
LEVEL_UP_USER_TABLE=users
TRIX_STORAGE_DISK=public
ENVEOF

# Update Caddyfile
print_status "Konfigurasi Caddyfile..."
cat > Caddyfile << 'CADDYEOF'
{
    frankenphp
    auto_https off
}

:80 {
    root * /app/public
    
    encode gzip
    
    php_server {
        try_files {path} /index.php
    }
    
    header {
        X-Frame-Options "SAMEORIGIN"
        X-XSS-Protection "1; mode=block"
        X-Content-Type-Options "nosniff"
    }
    
    @static {
        file
        path *.css *.js *.ico *.png *.jpg *.jpeg *.gif *.svg *.woff *.woff2 *.ttf *.eot
    }
    header @static Cache-Control "public, max-age=3600"
    
    log {
        output stdout
        format console
    }
}
CADDYEOF

# Create database directory
mkdir -p database

# Build dan start containers
print_status "Building dan starting containers..."
docker compose -f docker-compose.sqlite.yml down --remove-orphans 2>/dev/null || true
docker compose -f docker-compose.sqlite.yml build --no-cache
docker compose -f docker-compose.sqlite.yml up -d

# Wait for application
print_status "Menunggu aplikasi siap..."
sleep 20

# Verify application
print_status "Verifying application status..."
docker compose -f docker-compose.sqlite.yml ps

print_success "🎉 Deployment selesai!"
echo ""
echo "✅ Application: http://localhost"
echo "✅ Database: SQLite"
echo "✅ Container: FrankenPHP"
echo ""
echo "📋 Commands:"
echo "   Logs: docker compose -f docker-compose.sqlite.yml logs -f"
echo "   Shell: docker compose -f docker-compose.sqlite.yml exec app bash"
echo "   Stop: docker compose -f docker-compose.sqlite.yml down"
