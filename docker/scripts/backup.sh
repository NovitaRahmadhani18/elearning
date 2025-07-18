#!/bin/bash

# Backup script for MySQL database and Laravel storage
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"
DB_CONTAINER="elearning_db_prod"

echo "Starting backup process at $(date)"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Database backup
echo "Backing up MySQL database..."
docker exec $DB_CONTAINER mysqldump -u elearning -p$DB_PASSWORD elearning_prod > $BACKUP_DIR/database_$DATE.sql

if [ $? -eq 0 ]; then
    echo "Database backup completed: database_$DATE.sql"
    gzip $BACKUP_DIR/database_$DATE.sql
    echo "Database backup compressed"
else
    echo "Database backup failed!"
    exit 1
fi

# Storage backup
echo "Backing up Laravel storage..."
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/storage/app/public

if [ $? -eq 0 ]; then
    echo "Storage backup completed: storage_$DATE.tar.gz"
else
    echo "Storage backup failed!"
fi

# Clean old backups (keep last 7 days)
echo "Cleaning old backups..."
find $BACKUP_DIR -name "database_*.sql.gz" -mtime +7 -delete
find $BACKUP_DIR -name "storage_*.tar.gz" -mtime +7 -delete

echo "Backup process completed at $(date)"
