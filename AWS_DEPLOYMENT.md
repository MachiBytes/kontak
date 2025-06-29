# 🚀 AWS EC2 + RDS Production Deployment Guide

Complete guide to deploy your Laravel Kontak application on AWS EC2 with RDS database using AWS Linux and nohup.

## **Phase 1: Setup AWS RDS Database**

### Create RDS MySQL Instance:
```bash
# Login to AWS Console → RDS → Create Database
- Engine: MySQL 8.0
- Template: Production (or Dev/Test for lower cost)
- DB Instance Class: db.t3.micro (free tier) or db.t3.small
- Storage: 20GB (adjust as needed)
- DB Instance Identifier: kontak-database
- Master Username: admin
- Master Password: [Generate strong password]
- VPC: Default VPC
- Public Access: No
- Security Group: Create new (allow MySQL port 3306)
```

### Note down RDS connection details:
- Endpoint (e.g., `kontak-database.xxxxx.us-east-1.rds.amazonaws.com`)
- Port: `3306`
- Username: `admin`
- Password: `[your-password]`

---

## **Phase 2: Setup AWS EC2 Instance**

### Launch EC2 Instance:
```bash
# AWS Console → EC2 → Launch Instance
- AMI: Amazon Linux 2023
- Instance Type: t3.micro (free tier) or t3.small
- Key Pair: Create new or use existing
- Security Group: Create new with these rules:
  * SSH (port 22): Your IP
  * HTTP (port 80): Anywhere
  * HTTPS (port 443): Anywhere
  * Custom TCP (port 8000): Anywhere (for Laravel server)
```

### Connect to your EC2 instance:
```bash
ssh -i your-key.pem ec2-user@your-ec2-public-ip
```

---

## **Phase 3: Server Setup**

### Update system and install dependencies:
```bash
sudo yum update -y

# Install EPEL repository for additional packages
sudo yum install -y epel-release

# Install PHP 8.2 and extensions
sudo yum install -y amazon-linux-extras
sudo amazon-linux-extras enable php8.2
sudo yum clean metadata
sudo yum install -y php php-cli php-fpm php-mysqlnd php-xml php-gd php-curl php-zip php-mbstring php-bcmath php-json php-pdo php-opcache

# Install Nginx
sudo yum install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install -y nodejs

# Install Git
sudo yum install -y git
```

---

## **Phase 4: Deploy Application**

### Clone and setup your application:
```bash
# Navigate to web directory
cd /var/www

# Clone your repository
sudo git clone https://github.com/MachiBytes/kontak.git
sudo chown -R ec2-user:ec2-user kontak
cd kontak

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install and build frontend assets
npm install
npm run build

# Set proper permissions
sudo chown -R nginx:nginx /var/www/kontak
sudo chmod -R 755 /var/www/kontak
sudo chmod -R 775 /var/www/kontak/storage
sudo chmod -R 775 /var/www/kontak/bootstrap/cache
sudo chown -R ec2-user:ec2-user /var/www/kontak
```

### Configure environment:
```bash
# Copy environment file
cp .env.example .env

# Edit environment file
nano .env
```

**Update `.env` with production settings:**
```env
APP_NAME=Kontak
APP_ENV=production
APP_KEY=base64:your-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=your-rds-endpoint.amazonaws.com
DB_PORT=3306
DB_DATABASE=kontak
DB_USERNAME=admin
DB_PASSWORD=your-rds-password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Setup Laravel application:
```bash
# Generate application key
php artisan key:generate

# Run migrations (this will create the database automatically if it doesn't exist)
php artisan migrate:fresh

# Seed demo data
php artisan db:seed

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## **Phase 5: Configure Nginx**

### Create Nginx configuration:
```bash
sudo nano /etc/nginx/conf.d/kontak.conf
```

**Add this configuration:**
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/kontak/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Configure PHP-FPM:
```bash
# Configure PHP-FPM to listen on port 9000
sudo nano /etc/php-fpm.d/www.conf
```

**Update these lines in the PHP-FPM configuration:**
```ini
listen = 127.0.0.1:9000
user = nginx
group = nginx
```

### Enable and start services:
```bash
# Remove default Nginx configuration
sudo rm -f /etc/nginx/conf.d/default.conf

# Test Nginx configuration
sudo nginx -t

# Start and enable services
sudo systemctl start nginx
sudo systemctl enable nginx
sudo systemctl start php-fpm
sudo systemctl enable php-fpm

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php-fpm
```

---

## **Phase 6: Start Application with nohup**

### Start Laravel application:
```bash
cd /var/www/kontak

# Start Laravel in background with nohup
nohup php artisan serve --host=0.0.0.0 --port=8000 > logs/laravel-app.log 2>&1 &

# Save the process ID for future reference
echo $! > /var/www/kontak/laravel.pid

# Verify it's running
ps aux | grep "artisan serve"
```

### Application management commands:
```bash
# Check if running
ps aux | grep "artisan serve"

# Stop the application
kill $(cat /var/www/kontak/laravel.pid)

# Restart the application
cd /var/www/kontak
nohup php artisan serve --host=0.0.0.0 --port=8000 > logs/laravel-app.log 2>&1 &
echo $! > /var/www/kontak/laravel.pid

# View application server logs
tail -f logs/laravel-app.log
```

### Update application:
```bash
cd /var/www/kontak

# Stop application
kill $(cat /var/www/kontak/laravel.pid)

# Update code
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build

# Run migrations and cache
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart application
nohup php artisan serve --host=0.0.0.0 --port=8000 > logs/laravel-app.log 2>&1 &
echo $! > /var/www/kontak/laravel.pid
```

## **📋 Demo Data Access**

After deployment, you can access the application with these demo accounts:

### Login Credentials
All demo accounts use the password: **`password`**

#### 🏢 Main Account (Contact Book Owner)
- **Email:** `developers@kontak.app`
- **Name:** Kontak Developers
- **Features:** Full access to personal contact book with 65+ sample contacts

#### 👨‍💼 Admin Users (Full CRUD Access)
- `mark.flores@kontak.app`
- `isaeus.guiang@kontak.app`
- `jedric.vicente@kontak.app`
- `ceferino.arrey@kontak.app`
- `rainier.reyes@kontak.app`

#### 👥 Audience Users (Read-Only Access)
- `marketing@kontak.app`
- `sales@kontak.app`
- `support@kontak.app`

---