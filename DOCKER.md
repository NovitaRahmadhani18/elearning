# FrankenPHP E-Learning Platform

## 🧟 Modern PHP App Server dengan FrankenPHP

Project ini menggunakan **FrankenPHP** - modern PHP application server yang dibangun di atas Caddy web server dengan fitur-fitur canggih seperti Worker Mode, HTTP/2, HTTP/3, dan HTTPS otomatis.

### 📋 Services Included:

- **app**: FrankenPHP dengan Worker Mode (super fast!)
- **database**: MySQL 8.0 database
- **redis**: Redis untuk caching dan sessions

### 🚀 Development Quick Start:

1. **Start development environment:**

    ```bash
    docker-compose up -d
    ```

2. **Access the application:**
    - **Web App**: http://localhost
    - **Database**: localhost:3306
    - **Redis**: localhost:6379

### 🌐 Production Deployment:

**Untuk VPS Production:**

```bash
# Clone project ke VPS
git clone <your-repo> elearning
cd elearning

# Deploy dengan domain Anda
chmod +x deploy-production.sh
./deploy-production.sh yourdomain.com
```

**Fitur Production:**

- ✅ **HTTPS Otomatis** - Caddy automatic SSL
- ✅ **Worker Mode** - Performance maksimal
- ✅ **HTTP/2 & HTTP/3** - Modern protocols
- ✅ **Auto Backup** - Database backup harian
- ✅ **Security Headers** - Production security

### 🔧 Daily Development Commands:

**Start services:**

```bash
docker-compose up -d
```

**Stop services:**

```bash
docker-compose down
```

**View logs:**

```bash
docker-compose logs -f app
docker-compose logs -f webserver
```

**Access containers:**

```bash
# Laravel app container
docker-compose exec app bash

# Database container
docker-compose exec database mysql -u elearning -p
```

### 📦 Common Development Tasks:

**Artisan commands:**

```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan make:controller ExampleController
docker-compose exec app php artisan queue:work
```

**Composer:**

```bash
docker-compose exec app composer install
docker-compose exec app composer require package/name
```

**NPM/Asset compilation:**

```bash
docker-compose exec node npm install
docker-compose exec node npm run build
docker-compose exec node npm run dev
```

**Database operations:**

```bash
# Run migrations
docker-compose exec app php artisan migrate

# Run seeders
docker-compose exec app php artisan db:seed

# Fresh migration with seeding
docker-compose exec app php artisan migrate:fresh --seed
```

### 🗂️ Volume Mapping:

- Application code: `./` → `/var/www`
- Database data: `database_data` volume (persistent)
- Redis data: `redis_data` volume (persistent)

### 🔐 Environment Variables:

All environment variables are configured in `.env.docker` file. Key settings:

- `DB_HOST=database` (Docker service name)
- `REDIS_HOST=redis` (Docker service name)
- `APP_URL=http://localhost:8000`

### 🐛 Troubleshooting:

**Permission issues:**

```bash
docker-compose exec app chown -R www:www /var/www/storage
docker-compose exec app chown -R www:www /var/www/bootstrap/cache
```

**Clear Laravel cache:**

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan view:clear
```

**Rebuild containers:**

```bash
docker-compose down
docker-compose up -d --build
```

### 📊 Database Connection:

- **Host**: localhost (from host machine) or `database` (from containers)
- **Port**: 3306
- **Database**: elearning
- **Username**: elearning
- **Password**: password

### 🔴 Redis Connection:

- **Host**: localhost (from host machine) or `redis` (from containers)
- **Port**: 6379
- **No password required**

---

## 🌐 Production Deployment (VPS with Domain)

### 🚀 Production Quick Start:

**Prerequisites:**

- VPS with Docker and Docker Compose installed
- Domain name pointing to your VPS IP
- Email address for SSL certificate

**Deployment Command:**

```bash
chmod +x docker-production-start.sh
./docker-production-start.sh your-domain.com your-email@domain.com
```

### 📋 Production Services:

- **app**: PHP 8.2-FPM Laravel application (production optimized)
- **webserver**: Nginx with SSL/HTTPS support
- **database**: MySQL 8.0 with production security
- **redis**: Redis with memory optimization
- **certbot**: Let's Encrypt SSL certificate automation
- **backup**: Automated database and storage backups

### 🔐 Production Features:

**SSL/HTTPS:**

- ✅ Automatic Let's Encrypt SSL certificates
- ✅ HTTP to HTTPS redirection
- ✅ Strong SSL configuration (TLS 1.2/1.3)
- ✅ Automatic certificate renewal

**Security:**

- ✅ Security headers (HSTS, CSP, XSS Protection)
- ✅ Rate limiting for login endpoints
- ✅ Secure session configuration
- ✅ File upload protection

**Performance:**

- ✅ Gzip compression
- ✅ Static file caching
- ✅ Redis memory optimization
- ✅ PHP production optimization

**Backup & Monitoring:**

- ✅ Daily automated database backups
- ✅ Storage file backups
- ✅ 7-day backup retention
- ✅ Comprehensive logging

### 🛠️ Production Management:

**Start/Stop Services:**

```bash
# Start all services
docker-compose -f docker-compose.production.yml up -d

# Stop all services
docker-compose -f docker-compose.production.yml down

# Restart specific service
docker-compose -f docker-compose.production.yml restart webserver
```

**Monitoring & Logs:**

```bash
# View all logs
docker-compose -f docker-compose.production.yml logs -f

# View specific service logs
docker-compose -f docker-compose.production.yml logs -f app
docker-compose -f docker-compose.production.yml logs -f webserver

# View Nginx access logs
docker-compose -f docker-compose.production.yml exec webserver tail -f /var/log/nginx/elearning_access.log
```

**Database Operations:**

```bash
# Run migrations
docker-compose -f docker-compose.production.yml exec app php artisan migrate --force

# Run backups manually
docker-compose -f docker-compose.production.yml run --rm backup

# Access database
docker-compose -f docker-compose.production.yml exec database mysql -u elearning -p
```

**Laravel Operations:**

```bash
# Clear caches
docker-compose -f docker-compose.production.yml exec app php artisan config:clear
docker-compose -f docker-compose.production.yml exec app php artisan cache:clear

# Optimize for production
docker-compose -f docker-compose.production.yml exec app php artisan config:cache
docker-compose -f docker-compose.production.yml exec app php artisan route:cache
docker-compose -f docker-compose.production.yml exec app php artisan view:cache
```

### 🔧 SSL Certificate Management:

**Manual certificate renewal:**

```bash
docker-compose -f docker-compose.production.yml run --rm certbot renew
docker-compose -f docker-compose.production.yml restart webserver
```

**Check certificate status:**

```bash
docker-compose -f docker-compose.production.yml run --rm certbot certificates
```

### 📊 Production Environment Variables:

Key production settings in `.env.production`:

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
SESSION_SECURE_COOKIE=true
FORCE_HTTPS=true
SANCTUM_STATEFUL_DOMAINS=your-domain.com,www.your-domain.com
```

### 🔍 Production Troubleshooting:

**SSL Issues:**

```bash
# Check SSL certificate
openssl s_client -connect your-domain.com:443 -servername your-domain.com

# Renew certificate manually
docker-compose -f docker-compose.production.yml run --rm certbot renew --force-renewal
```

**Performance Issues:**

```bash
# Check container resources
docker stats

# Check MySQL performance
docker-compose -f docker-compose.production.yml exec database mysql -u root -p -e "SHOW PROCESSLIST;"

# Check Redis memory usage
docker-compose -f docker-compose.production.yml exec redis redis-cli info memory
```

**Backup Issues:**

```bash
# List backups
ls -la storage/backups/

# Test backup restoration
docker-compose -f docker-compose.production.yml exec database mysql -u elearning -p elearning_prod < storage/backups/database_YYYYMMDD_HHMMSS.sql
```

### 📈 Production Monitoring:

**System Health Check:**

```bash
# Check all services status
docker-compose -f docker-compose.production.yml ps

# Check resource usage
docker-compose -f docker-compose.production.yml exec app df -h
docker-compose -f docker-compose.production.yml exec app free -m
```

**Application Health:**

```bash
# Test application response
curl -I https://your-domain.com

# Check Laravel status
docker-compose -f docker-compose.production.yml exec app php artisan about
```

### 🔄 Automated Tasks:

The production deployment sets up automatic cron jobs:

- **SSL Renewal**: Daily at 12:00 PM
- **Database Backup**: Daily at 2:00 AM
- **Log Rotation**: Managed by Docker

**View cron jobs:**

```bash
crontab -l
```
