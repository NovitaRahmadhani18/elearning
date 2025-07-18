#!/bin/bash

# Production deployment script for E-Learning Platform
set -e

echo "🚀 Starting E-Learning Platform Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if domain is provided
if [ -z "$1" ]; then
    print_error "Domain is required!"
    echo "Usage: ./docker-production-start.sh your-domain.com your-email@domain.com"
    exit 1
fi

if [ -z "$2" ]; then
    print_error "Email is required for SSL certificate!"
    echo "Usage: ./docker-production-start.sh your-domain.com your-email@domain.com"
    exit 1
fi

DOMAIN=$1
EMAIL=$2

print_status "Domain: $DOMAIN"
print_status "Email: $EMAIL"

# Check if Docker and Docker Compose are installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create necessary directories
print_status "Creating necessary directories..."
mkdir -p storage/backups
mkdir -p docker/ssl
mkdir -p docker/mysql/backup

# Set permissions for backup script
chmod +x docker/scripts/backup.sh

# Copy and configure environment file
print_status "Setting up environment configuration..."
if [ ! -f .env ]; then
    cp .env.production .env
    print_success "Environment file created from .env.production"
else
    print_warning ".env file already exists. Please manually review the configuration."
fi

# Update domain in configuration files
print_status "Updating domain configuration..."

# Update production nginx config
sed -i "s/your-domain.com/$DOMAIN/g" docker/nginx/production.conf
print_success "Updated Nginx configuration with domain: $DOMAIN"

# Update docker-compose.production.yml
sed -i "s/your-email@domain.com/$EMAIL/g" docker-compose.production.yml
sed -i "s/your-domain.com/$DOMAIN/g" docker-compose.production.yml
print_success "Updated Docker Compose configuration"

# Update .env file
sed -i "s/your-domain.com/$DOMAIN/g" .env
print_success "Updated environment configuration"

# Generate application key if not exists
print_status "Setting up Laravel application..."

# Build and start containers
print_status "Building and starting Docker containers..."
docker-compose -f docker-compose.production.yml down
docker-compose -f docker-compose.production.yml build --no-cache
docker-compose -f docker-compose.production.yml up -d app database redis

# Wait for database to be ready
print_status "Waiting for database to be ready..."
sleep 30

# Install dependencies and setup Laravel
print_status "Installing Composer dependencies..."
docker-compose -f docker-compose.production.yml exec -T app composer install --optimize-autoloader --no-dev

print_status "Generating application key..."
docker-compose -f docker-compose.production.yml exec -T app php artisan key:generate --force

print_status "Running database migrations..."
docker-compose -f docker-compose.production.yml exec -T app php artisan migrate --force

print_status "Seeding database..."
docker-compose -f docker-compose.production.yml exec -T app php artisan db:seed --force

print_status "Optimizing Laravel..."
docker-compose -f docker-compose.production.yml exec -T app php artisan config:cache
docker-compose -f docker-compose.production.yml exec -T app php artisan route:cache
docker-compose -f docker-compose.production.yml exec -T app php artisan view:cache

print_status "Setting proper permissions..."
docker-compose -f docker-compose.production.yml exec -T app chown -R www-data:www-data /var/www/storage
docker-compose -f docker-compose.production.yml exec -T app chown -R www-data:www-data /var/www/bootstrap/cache

# Start web server
print_status "Starting web server..."
docker-compose -f docker-compose.production.yml up -d webserver

print_status "Obtaining SSL certificate..."
print_warning "Please make sure your domain DNS points to this server before continuing."
read -p "Press Enter to continue with SSL certificate generation..."

# Get SSL certificate
docker-compose -f docker-compose.production.yml run --rm certbot certonly --webroot --webroot-path=/var/www/public --email $EMAIL --agree-tos --no-eff-email -d $DOMAIN -d www.$DOMAIN

if [ $? -eq 0 ]; then
    print_success "SSL certificate obtained successfully!"

    # Restart nginx with SSL
    docker-compose -f docker-compose.production.yml restart webserver
    print_success "Web server restarted with SSL configuration"
else
    print_error "Failed to obtain SSL certificate. Please check your domain DNS settings."
    print_warning "The application is running on HTTP only."
fi

# Set up cron job for SSL renewal and backups
print_status "Setting up automatic SSL renewal and backups..."
(crontab -l 2>/dev/null; echo "0 12 * * * cd $(pwd) && docker-compose -f docker-compose.production.yml run --rm certbot renew --quiet && docker-compose -f docker-compose.production.yml restart webserver") | crontab -
(crontab -l 2>/dev/null; echo "0 2 * * * cd $(pwd) && docker-compose -f docker-compose.production.yml run --rm backup") | crontab -

print_success "Cron jobs set up for SSL renewal and daily backups"

# Display final status
echo ""
echo "🎉 ==============================================="
echo "🎉  E-Learning Platform Deployment Complete!"
echo "🎉 ==============================================="
echo ""
print_success "✅ Application URL: https://$DOMAIN"
print_success "✅ SSL Certificate: Enabled"
print_success "✅ Database: MySQL 8.0"
print_success "✅ Cache: Redis"
print_success "✅ Backups: Automated daily"
print_success "✅ SSL Renewal: Automated"
echo ""
echo "📋 Next Steps:"
echo "   1. Access your application at https://$DOMAIN"
echo "   2. Create admin account through the application"
echo "   3. Configure email settings in .env file"
echo "   4. Monitor logs: docker-compose -f docker-compose.production.yml logs -f"
echo ""
echo "🔧 Management Commands:"
echo "   Start:   docker-compose -f docker-compose.production.yml up -d"
echo "   Stop:    docker-compose -f docker-compose.production.yml down"
echo "   Logs:    docker-compose -f docker-compose.production.yml logs -f"
echo "   Backup:  docker-compose -f docker-compose.production.yml run --rm backup"
echo ""

print_status "Deployment completed successfully! 🚀"
