# 🚀 AWS EC2 + RDS Production Deployment Guide

Complete guide to deploy your Laravel Kontak application on AWS EC2 with RDS database.

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
- AMI: Ubuntu Server 22.04 LTS
- Instance Type: t3.micro (free tier) or t3.small
- Key Pair: Create new or use existing
- Security Group: Create new with these rules:
  * SSH (port 22): Your IP
  * HTTP (port 80): Anywhere
  * HTTPS (port 443): Anywhere
  * Custom TCP (port 3000): Anywhere (for Laravel dev server)
```

### Connect to your EC2 instance:
```bash
ssh -i your-key.pem ubuntu@your-ec2-public-ip
```

---

## **Phase 3: Server Setup**

### Update system and install dependencies:
```bash
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-zip php8.2-mbstring php8.2-bcmath php8.2-tokenizer php8.2-json php8.2-pdo

# Install Nginx
sudo apt install -y nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Git
sudo apt install -y git

# Install PM2 for process management
sudo npm install -g pm2
```

---

## **Phase 4: Deploy Application**

### Clone and setup your application:
```bash
# Navigate to web directory
cd /var/www

# Clone your repository
sudo git clone https://github.com/your-username/kontak.git
sudo chown -R ubuntu:ubuntu kontak
cd kontak

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install and build frontend assets
npm install
npm run build

# Set proper permissions
sudo chown -R www-data:www-data /var/www/kontak
sudo chmod -R 755 /var/www/kontak
sudo chmod -R 775 /var/www/kontak/storage
sudo chmod -R 775 /var/www/kontak/bootstrap/cache
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

### Generate application key and run migrations:
```bash
# Generate application key
php artisan key:generate

# Create database (connect to RDS first)
mysql -h your-rds-endpoint.amazonaws.com -u admin -p
# In MySQL prompt:
CREATE DATABASE kontak;
exit

# Run migrations
php artisan migrate --force

# Seed demo data (optional)
php artisan db:seed --class=DemoDataSeeder --force

# Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## **Phase 5: Configure Nginx**

### Create Nginx configuration:
```bash
sudo nano /etc/nginx/sites-available/kontak
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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/kontak /etc/nginx/sites-enabled/
sudo unlink /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
sudo systemctl restart php8.2-fpm
```

---

## **Phase 6: Setup Process Management (Keep Running Forever)**

### Create PM2 ecosystem file:
```bash
nano /var/www/kontak/ecosystem.config.js
```

**Add this configuration:**
```javascript
module.exports = {
  apps: [{
    name: 'kontak',
    script: 'artisan',
    args: 'serve --host=0.0.0.0 --port=8000',
    cwd: '/var/www/kontak',
    interpreter: 'php',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      APP_ENV: 'production'
    }
  }]
}
```

### Start application with PM2:
```bash
cd /var/www/kontak
pm2 start ecosystem.config.js
pm2 save
pm2 startup
# Follow the instructions given by pm2 startup command
```

---

## **Phase 7: Setup SSL with Let's Encrypt (Optional but Recommended)**

### Install Certbot:
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Get SSL certificate:
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

---

## **Phase 8: Setup Firewall**

### Configure UFW:
```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

---

## **Phase 9: Domain Setup**

### Point your domain to EC2:
- Go to your domain registrar (GoDaddy, Namecheap, etc.)
- Create an A record pointing to your EC2 public IP
- Example: `yourdomain.com` → `54.123.45.67`

---

## **Phase 10: Monitoring and Maintenance**

### Useful PM2 commands:
```bash
pm2 status          # Check application status
pm2 logs kontak     # View application logs
pm2 restart kontak  # Restart application
pm2 stop kontak     # Stop application
pm2 delete kontak   # Delete application from PM2
```

### Update application:
```bash
cd /var/www/kontak
git pull origin main
composer install --optimize-autoloader --no-dev
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
pm2 restart kontak
```

### Monitor logs:
```bash
# Application logs
tail -f /var/www/kontak/storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# PM2 logs
pm2 logs kontak
```

---

## **🔐 Security Considerations**

### Keep system updated:
```bash
sudo apt update && sudo apt upgrade -y
```

### Change default SSH port (optional):
```bash
sudo nano /etc/ssh/sshd_config
# Change Port 22 to Port 2222
sudo systemctl restart ssh
```

### Setup automatic backups:
```bash
# Create backup script
sudo nano /usr/local/bin/backup-kontak.sh
```

---

## **💰 Cost Optimization**

- Use `t3.micro` instances (free tier eligible)
- Use `db.t3.micro` for RDS (free tier eligible)
- Setup CloudWatch alarms for monitoring
- Consider using Application Load Balancer for high availability

---

## **🚀 Access Your Application**

Once everything is setup:
- **HTTP:** `http://your-domain.com`
- **HTTPS:** `https://your-domain.com` (after SSL setup)
- **Direct IP:** `http://your-ec2-ip` (for testing)

Your Laravel application will now run 24/7 on AWS, automatically restart if it crashes, and persist even when you close your terminal/console!

---

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

## **🔧 Troubleshooting**

### Common Issues:

1. **Permission errors:**
   ```bash
   sudo chown -R www-data:www-data /var/www/kontak
   sudo chmod -R 755 /var/www/kontak
   sudo chmod -R 775 /var/www/kontak/storage
   ```

2. **Database connection errors:**
   - Check RDS security group allows EC2 connection
   - Verify `.env` database credentials
   - Test connection: `mysql -h your-rds-endpoint -u admin -p`

3. **Application not accessible:**
   - Check EC2 security group allows HTTP/HTTPS
   - Verify Nginx configuration: `sudo nginx -t`
   - Check PM2 status: `pm2 status`

4. **SSL certificate issues:**
   - Ensure domain points to EC2 IP
   - Check DNS propagation
   - Verify Nginx configuration

---

**🎉 Congratulations! Your Laravel application is now running in production on AWS!** 
