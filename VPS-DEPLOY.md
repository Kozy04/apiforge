# VPS Deployment Guide (aaPanel)

## Prerequisites
- Ubuntu 22.04 VPS with root access
- Domain pointed to VPS IP (A record)

## Step 1 — Install aaPanel
```bash
# SSH into your VPS, then:
wget -O install.sh http://www.aapanel.com/script/install-ubuntu_6.0_en.sh && sudo bash install.sh
```
Follow prompts. Save the login URL + credentials shown at the end.

## Step 2 — Install LAMP in aaPanel
1. Log into aaPanel panel
2. **App Store** → install:
   - Nginx (latest)
   - PHP 8.2 (check these extensions: `fileinfo, openssl, pdo_mysql, mbstring, curl, zip`)
   - MySQL 8.0 or MariaDB
   - phpMyAdmin

## Step 3 — Create Website + Database
1. **Website** → **Add Site**
   - Domain: `yourdomain.com`
   - PHP version: 8.2
   - Create
2. **Database** → **Add Database**
   - Name: `apiforge`
   - User: `apiforge` / Password: (generate strong)
   - Save

## Step 4 — Clone & Setup
```bash
# SSH into VPS
cd /www/wwwroot/yourdomain.com
rm -rf * .*
git clone https://github.com/Kozy04/apiforge.git .
cp .env.example .env
vim .env   # Edit these lines:
```
```
APP_URL=https://yourdomain.com
APP_DEBUG=false
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apiforge
DB_USERNAME=apiforge
DB_PASSWORD=your-db-password
SCRAPER_API_KEY=(run: openssl rand -base64 32)
ADMIN_PASSWORD=(run: openssl rand -base64 16)
```

## Step 5 — Install Dependencies + Build
```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan app:generate-sitemap
php artisan app:generate-llms-txt
php artisan storage:link
```

## Step 6 — aaPanel Site Config
1. **Website** → your domain → **Site Directory**
   - Running directory: `/www/wwwroot/yourdomain.com`
   - Document root: `/www/wwwroot/yourdomain.com/public`
2. **Website** → your domain → **URL Rewrite** → paste (Laravel):
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```
3. **PHP** → disable all functions except basic ones (or just leave defaults)

## Step 7 — Set Up Cron Jobs
In aaPanel **Cron** tab, add two entries:

| Name | Schedule | Command |
|------|----------|---------|
| Laravel Scheduler | `* * * * *` | `php /www/wwwroot/yourdomain.com/artisan schedule:run >> /dev/null 2>&1` |
| Python Scraper | `0 3 * * 0` | `cd /www/wwwroot/yourdomain.com/scraper && python3 scraper.py >> logs/scraper.log 2>&1` |
| Blog Scraper | `0 6 * * *` | `cd /www/wwwroot/yourdomain.com/scraper && python3 blog_scraper.py >> logs/blog_scraper.log 2>&1` |

For the Python scrapers, set env vars in crontab:
```bash
APIFORGE_API_KEY=your-key
APIFORGE_WEBHOOK_URL=https://yourdomain.com/api/update-prices
APIFORGE_BLOG_WEBHOOK=https://yourdomain.com/api/blog-posts
```

## Step 8 — SSL
aaPanel → **Website** → your domain → **SSL** → **Let's Encrypt** → Apply

## Verify
Visit `https://yourdomain.com` — site loads.
Visit `https://yourdomain.com/admin` — login with ADMIN_PASSWORD.
Visit `https://yourdomain.com/sitemap.xml` — 407+ URLs.
