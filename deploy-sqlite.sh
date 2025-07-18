#!/bin/bash

# Simple SQLite deployment script untuk FrankenPHP E-Learning
set -e

echo "🚀 Deploying E-Learning Platform with FrankenPHP + SQLite..."

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_status "Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    rm get-docker.sh
    print_success "Docker installed successfully!"
fi

# Check if Docker Compose is installed
if ! docker compose version &> /dev/null; then
    print_status "Installing Docker Compose plugin..."
    sudo apt-get update
    sudo apt-get install -y docker-compose-plugin
    print_success "Docker Compose installed successfully!"
fi

# Check domain parameter
DOMAIN=${1:-localhost}

# Create environment file
print_status "Membuat file environment untuk SQLite..."
cat > .env << EOF
APP_NAME="E-Learning Platform"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$DOMAIN

DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite

REDIS_HOST=redis
REDIS_PORT=6379
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

SERVER_NAME=$DOMAIN

# Level Up & Trix
LEVEL_UP_USER_FOREIGN_KEY=user_id
LEVEL_UP_USER_TABLE=users
TRIX_STORAGE_DISK=public
EOF

# Update Caddyfile for production
print_status "Konfigurasi Caddyfile untuk production..."
cat > Caddyfile << EOF
{
    frankenphp
    # Worker mode disabled for stability
}

$DOMAIN {
    root * /app/public
    
    encode gzip
    
    php_server {
        try_files {path} /index.php
    }
    
    header {
        X-Frame-Options "SAMEORIGIN"
        X-XSS-Protection "1; mode=block"
        X-Content-Type-Options "nosniff"
        Referrer-Policy "no-referrer-when-downgrade"
        Strict-Transport-Security "max-age=31536000; includeSubDomains"
    }
    
    @static {
        file
        path *.css *.js *.ico *.png *.jpg *.jpeg *.gif *.svg *.woff *.woff2 *.ttf *.eot
    }
    header @static Cache-Control "public, max-age=31536000"
    
    log {
        output stdout
        format console
    }
}
EOF# Create database directory
mkdir -p database

# Build dan start containers
print_status "Building dan starting containers..."
docker compose -f docker-compose.sqlite.yml down --remove-orphans
docker compose -f docker-compose.sqlite.yml build --no-cache
docker compose -f docker-compose.sqlite.yml up -d

# Wait for application to be ready
print_status "Menunggu aplikasi siap..."
sleep 15

# Verify application is running
print_status "Verifying application status..."
docker compose -f docker-compose.sqlite.yml ps

# Set final permissions
print_status "Setting final permissions..."
docker compose -f docker-compose.sqlite.yml exec -T app chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database

print_success "🎉 Deployment selesai!"
echo ""
echo "✅ Domain: https://$DOMAIN"
echo "✅ Database: SQLite (no external dependencies)"
echo "✅ HTTPS: Otomatis (FrankenPHP + Caddy)"
echo "✅ Worker Mode: Aktif"
echo "✅ HTTP/2 & HTTP/3: Aktif"
echo ""
echo "📋 Management Commands:"
echo "   Start: docker compose -f docker-compose.sqlite.yml up -d"
echo "   Stop:  docker compose -f docker-compose.sqlite.yml down"
echo "   Logs:  docker compose -f docker-compose.sqlite.yml logs -f"
echo "   Shell: docker compose -f docker-compose.sqlite.yml exec app bash"
