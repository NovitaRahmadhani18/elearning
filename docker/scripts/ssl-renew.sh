#!/bin/bash

# SSL Certificate Renewal Script
set -e

echo "🔒 Renewing SSL Certificate..."

PROJECT_DIR="/path/to/your/project"
cd $PROJECT_DIR

# Renew certificate
docker-compose -f docker-compose.production.yml run --rm certbot renew --quiet

# Check if renewal was successful
if [ $? -eq 0 ]; then
    echo "✅ SSL certificate renewed successfully"

    # Reload nginx
    docker-compose -f docker-compose.production.yml restart webserver
    echo "✅ Web server reloaded"

    # Log the renewal
    echo "$(date): SSL certificate renewed successfully" >> storage/logs/ssl-renewal.log
else
    echo "❌ SSL certificate renewal failed"
    echo "$(date): SSL certificate renewal failed" >> storage/logs/ssl-renewal.log
    exit 1
fi
