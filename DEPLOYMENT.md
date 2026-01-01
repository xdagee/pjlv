# PJLV Leave Management System - Deployment Guide

## Server Requirements

### Minimum Requirements
- **PHP**: 8.1 or higher
- **Database**: MySQL 8.0+ or MariaDB 10.3+
- **Web Server**: Nginx 1.18+ or Apache 2.4+
- **Composer**: 2.0+
- **Node.js**: 16+ (for asset compilation)
- **Redis**: 6.0+ (recommended for caching and sessions)

### PHP Extensions Required
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD or Imagick

---

## Initial Installation

### 1. Clone Repository
```bash
cd /var/www
git clone https://github.com/your-repo/pjlv.git
cd pjlv
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` file with your production settings:
- Database credentials
- Mail server configuration
- Application URL
- Timezone (Africa/Accra for Ghana)

### 4. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed initial data (roles, leave types, statuses, etc.)
php artisan db:seed --class=DatabaseSeeder --force
```

### 5. Storage & Permissions
```bash
php artisan storage:link
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Cache Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Web Server Configuration

### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    root /var/www/pjlv/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration (.htaccess already included)
Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

## Queue Worker Setup

### Install Supervisor
```bash
sudo apt-get install supervisor
```

### Create Supervisor Configuration
Create file: `/etc/supervisor/conf.d/pjlv-worker.conf`
```ini
[program:pjlv-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pjlv/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pjlv/storage/logs/worker.log
stopwaitsecs=3600
```

### Start Queue Worker
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pjlv-worker:*
```

---

## Scheduled Tasks (Cron Jobs)

Add to crontab (`sudo crontab -e -u www-data`):
```cron
* * * * * cd /var/www/pjlv && php artisan schedule:run >> /dev/null 2>&1
```

This runs the Laravel scheduler which handles:
- Cache clearing
- Log rotation
- Automated reports (if configured)

---

## Database Backup Strategy

### Manual Backup
```bash
mysqldump -u username -p pjlv_production > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Automated Daily Backup (Cron)
Create backup script: `/var/www/pjlv/scripts/backup.sh`
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/pjlv"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="pjlv_production"
DB_USER="your_user"

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete
```

Add to crontab:
```cron
0 2 * * * /var/www/pjlv/scripts/backup.sh >> /var/log/pjlv-backup.log 2>&1
```

### Restore Procedure
```bash
# Decompress backup
gunzip backup_20250101_020000.sql.gz

# Restore database
mysql -u username -p pjlv_production < backup_20250101_020000.sql

# Clear and rebuild cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

---

## SSL/HTTPS Setup (Recommended)

### Using Let's Encrypt (Certbot)
```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### Update .env
```env
APP_URL=https://your-domain.com
SESSION_SECURE_COOKIE=true
```

---

## Monitoring & Health Checks

### Health Check Endpoint
Available at: `https://your-domain.com/health`

Returns JSON:
```json
{
    "status": "healthy",
    "database": "ok",
    "cache": "ok"
}
```

### Setup Monitoring (Optional)
1. **Uptime Monitoring**: Use services like UptimeRobot, Pingdom
2. **Error Tracking**: Sentry, Bugsnag, Flare (configure in `.env`)
3. **Performance**: New Relic, Scout APM

---

## Security Best Practices

### 1. Environment Security
- Never commit `.env` file to version control
- Use strong, unique database passwords
- Rotate `APP_KEY` periodically in non-production environments only

### 2. File Permissions
```bash
# Application files
find /var/www/pjlv -type f -exec chmod 644 {} \;
find /var/www/pjlv -type d -exec chmod 755 {} \;

# Storage and cache (writable)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Firewall Configuration
```bash
sudo ufw allow 22/tcp   # SSH
sudo ufw allow 80/tcp   # HTTP
sudo ufw allow 443/tcp  # HTTPS
sudo ufw enable
```

### 4. Regular Updates
```bash
# Update dependencies
composer update --no-dev
php artisan migrate --force

# Clear caches
php artisan optimize:clear
php artisan optimize
```

---

## Troubleshooting

### Issue: 500 Error After Deployment
**Solution**:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
chmod -R 775 storage bootstrap/cache
```

### Issue: Queue Jobs Not Processing
**Solution**:
```bash
sudo supervisorctl status
sudo supervisorctl restart pjlv-worker:*
tail -f storage/logs/worker.log
```

### Issue: Database Connection Failed
**Solution**:
- Verify database credentials in `.env`
- Check MySQL service: `sudo systemctl status mysql`
- Test connection: `php artisan migrate:status`

### Issue: Permission Denied Errors
**Solution**:
```bash
sudo chown -R www-data:www-data /var/www/pjlv
chmod -R 755 /var/www/pjlv
chmod -R 775 storage bootstrap/cache
```

---

## Rollback Procedure

### 1. Database Rollback
```bash
# Restore from backup
mysql -u username -p pjlv_production < backup_YYYYMMDD_HHMMSS.sql

# Or rollback specific migration
php artisan migrate:rollback --step=1
```

### 2. Code Rollback
```bash
git log  # Find previous commit
git checkout <commit-hash>
composer install --no-dev
php artisan migrate
php artisan optimize
```

### 3. Clear All Caches
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

---

## Production Checklist

Before going live, verify:

- [ ] `.env` configured with production values
- [ ] `APP_DEBUG=false` and `APP_ENV=production`
- [ ] Database migrations run successfully
- [ ] Initial data seeded (roles, departments, leave types)
- [ ] SSL certificate installed and working
- [ ] Queue worker running via Supervisor
- [ ] Cron jobs configured for scheduler
- [ ] Backup script tested and scheduled
- [ ] File permissions correct (755 for dirs, 644 for files, 775 for storage)
- [ ] Error logging configured
- [ ] Monitoring/uptime checks configured
- [ ] Admin account created and tested
- [ ] Email notifications working
- [ ] Health check endpoint responding

---

## Support & Maintenance

### Log Files Location
- Application: `storage/logs/laravel.log`
- Nginx: `/var/log/nginx/error.log`
- PHP-FPM: `/var/log/php8.1-fpm.log`
- Queue Worker: `storage/logs/worker.log`

### Useful Artisan Commands
```bash
# View routes
php artisan route:list

# Check migrations status
php artisan migrate:status

# Create admin user (if needed)
php artisan tinker
>>> App\Models\User::factory()->create(['email' => 'admin@example.com']);

# Clear specific cache
php artisan cache:forget key_name
```

---

## Contact & Support

For issues or questions:
- Technical Support: support@your-domain.com
- Emergency Contact: +233 XX XXX XXXX
- Documentation: https://your-domain.com/docs
