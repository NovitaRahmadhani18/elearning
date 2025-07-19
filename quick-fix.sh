#!/bin/bash

# Quick fix script untuk error 500
echo "🔧 Quick Fix untuk Error 500..."

echo "1️⃣ Generate APP_KEY jika belum ada:"
docker compose -f docker-compose.sqlite.yml exec app php artisan key:generate --force

echo "2️⃣ Create database jika belum ada:"
docker compose -f docker-compose.sqlite.yml exec app touch /app/database/database.sqlite
docker compose -f docker-compose.sqlite.yml exec app chmod 664 /app/database/database.sqlite

echo "3️⃣ Fix permissions:"
docker compose -f docker-compose.sqlite.yml exec app chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/database
docker compose -f docker-compose.sqlite.yml exec app chmod -R 775 /app/storage /app/bootstrap/cache

echo "4️⃣ Run migrations:"
docker compose -f docker-compose.sqlite.yml exec app php artisan migrate --force

echo "5️⃣ Create storage link:"
docker compose -f docker-compose.sqlite.yml exec app php artisan storage:link

echo "6️⃣ Clear caches:"
docker compose -f docker-compose.sqlite.yml exec app php artisan config:clear
docker compose -f docker-compose.sqlite.yml exec app php artisan cache:clear
docker compose -f docker-compose.sqlite.yml exec app php artisan view:clear

echo "7️⃣ Restart container:"
docker compose -f docker-compose.sqlite.yml restart app

echo ""
echo "✅ Quick fix completed! Test aplikasi sekarang."
echo "🌐 URL: http://elearningsmpsdb.site"
