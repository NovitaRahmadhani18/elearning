#!/bin/bash

# Simple test deployment for development
set -e

echo "🚀 Testing E-Learning Platform with FrankenPHP + SQLite..."

# Create environment file
echo "Creating .env file..."
cat > .env << EOF
APP_NAME="E-Learning Platform"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
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
EOF

# Create database directory
mkdir -p database

# Build dan start containers (development)
echo "Building dan starting containers..."
docker compose -f docker-compose.sqlite.yml down --remove-orphans 2>/dev/null || true
docker compose -f docker-compose.sqlite.yml build --no-cache
docker compose -f docker-compose.sqlite.yml up -d

echo ""
echo "✅ Application starting at: http://localhost"
echo "📋 View logs: docker compose -f docker-compose.sqlite.yml logs -f"
echo "🐛 Debug: docker compose -f docker-compose.sqlite.yml exec app bash"
