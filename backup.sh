#!/bin/bash

# Backup script for production environment
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"

echo "Starting backup process at $(date)"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Database backup
echo "Backing up MySQL database..."
mysqldump -h $MYSQL_HOST -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE > $BACKUP_DIR/database_$DATE.sql

if [ $? -eq 0 ]; then
    echo "Database backup completed: database_$DATE.sql"
    gzip $BACKUP_DIR/database_$DATE.sql
    echo "Database backup compressed"
else
    echo "Database backup failed!"
    exit 1
fi

# Clean old backups (keep last 7 days)
echo "Cleaning old backups..."
find $BACKUP_DIR -name "database_*.sql.gz" -mtime +7 -delete

echo "Backup process completed at $(date)"
