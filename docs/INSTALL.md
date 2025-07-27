# راهنمای نصب Vape Tube

## مراحل نصب سریع

### 1. آماده‌سازی سرور

#### نصب PHP و MySQL (Ubuntu/Debian)
```bash
sudo apt update
sudo apt install -y php php-mysql php-gd mysql-server
```

#### نصب PHP و MySQL (CentOS/RHEL)
```bash
sudo yum install -y php php-mysql php-gd mysql-server
```

### 2. تنظیم پایگاه داده

#### ایجاد پایگاه داده
```bash
sudo mysql
```

```sql
CREATE DATABASE vape_tube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'vape_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON vape_tube.* TO 'vape_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. آپلود فایل‌ها

1. فایل‌های پروژه را در روت وب سرور قرار دهید
2. مجوزهای مناسب را تنظیم کنید:

```bash
sudo chown -R www-data:www-data /var/www/html/vape-tube/
sudo chmod -R 755 /var/www/html/vape-tube/
sudo chmod -R 777 /var/www/html/vape-tube/assets/images/
```

### 4. تنظیم config.php

فایل `config.php` را ویرایش کنید:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vape_tube');
define('DB_USER', 'vape_user');
define('DB_PASS', 'your_secure_password');
```

### 5. اجرای اسکریپت راه‌اندازی

```bash
cd /var/www/html/vape-tube/
php setup.php
```

### 6. تنظیم وب سرور

#### Apache
ایجاد فایل `.htaccess`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.html [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

#### Nginx
تنظیمات Nginx:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/vape-tube;
    index index.html;

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";

    # PHP handling
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files caching
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # API routes
    location /api/ {
        try_files $uri $uri/ =404;
    }

    # Main route
    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

## تست نصب

### 1. تست محلی
```bash
cd /path/to/vape-tube/
php -S localhost:8000
```

سپس به آدرس `http://localhost:8000` مراجعه کنید.

### 2. تست عملکرد

- ✅ بارگذاری صفحه اصلی
- ✅ نمایش محصولات
- ✅ عملکرد سبد خرید
- ✅ سیستم جستجو
- ✅ فرم‌های ورود و ثبت نام

## عیب‌یابی

### خطاهای رایج

#### خطای اتصال به پایگاه داده
```
Error: Connection failed: Access denied for user
```

**راه حل:**
1. بررسی اطلاعات اتصال در `config.php`
2. اطمینان از وجود کاربر و پایگاه داده
3. بررسی مجوزهای MySQL

#### خطای 500 Internal Server Error
**راه حل:**
1. بررسی لاگ‌های وب سرور
2. اطمینان از نصب PHP و ماژول‌های مورد نیاز
3. بررسی مجوزهای فایل‌ها

#### تصاویر نمایش داده نمی‌شوند
**راه حل:**
1. بررسی مجوزهای پوشه `assets/images/`
2. اجرای `php create_placeholders.php`

### بررسی سیستم

#### تست PHP
```bash
php -v
php -m | grep mysql
php -m | grep gd
```

#### تست MySQL
```bash
mysql --version
mysql -u vape_user -p vape_tube -e "SHOW TABLES;"
```

## بهینه‌سازی عملکرد

### 1. تنظیمات PHP
در فایل `php.ini`:

```ini
memory_limit = 256M
max_execution_time = 60
upload_max_filesize = 10M
post_max_size = 10M
```

### 2. تنظیمات MySQL
در فایل `my.cnf`:

```ini
[mysqld]
innodb_buffer_pool_size = 256M
query_cache_size = 64M
query_cache_type = 1
```

### 3. فعال‌سازی OPcache
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
```

## امنیت

### 1. تنظیمات امنیتی PHP
```ini
expose_php = Off
display_errors = Off
log_errors = On
allow_url_fopen = Off
allow_url_include = Off
```

### 2. محافظت از فایل‌های حساس
ایجاد فایل `.htaccess` در پوشه `includes/`:

```apache
Order Deny,Allow
Deny from all
```

### 3. SSL/HTTPS
برای محیط تولید، حتماً SSL را فعال کنید:

```bash
sudo certbot --apache -d your-domain.com
```

## بک‌آپ

### بک‌آپ خودکار پایگاه داده
ایجاد اسکریپت cron:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u vape_user -p'your_password' vape_tube > /backup/vape_tube_$DATE.sql
find /backup -name "vape_tube_*.sql" -mtime +7 -delete
```

### بک‌آپ فایل‌ها
```bash
tar -czf vape_tube_files_$(date +%Y%m%d).tar.gz /var/www/html/vape-tube/
```

## مانیتورینگ

### لاگ‌های مهم
- `/var/log/apache2/error.log` (Apache)
- `/var/log/nginx/error.log` (Nginx)
- `/var/log/mysql/error.log` (MySQL)
- `/var/log/php8.1-fpm.log` (PHP-FPM)

### مانیتورینگ عملکرد
```bash
# بررسی استفاده از منابع
htop
iotop
mysqladmin processlist
```

## پشتیبانی

در صورت بروز مشکل:

1. بررسی لاگ‌های سیستم
2. مراجعه به بخش عیب‌یابی
3. تماس با تیم پشتیبانی

---

**نکته**: این راهنما برای نصب در محیط تولید طراحی شده است. برای محیط توسعه، می‌توانید از PHP Development Server استفاده کنید.

