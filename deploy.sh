#!/bin/bash
# APIForge Deployment Script (Linux / aaPanel VPS)

set -e

echo "=== APIForge Deployment ==="

echo "[1/6] Checking environment..."
command -v php >/dev/null 2>&1 || { echo "PHP required"; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "Composer required"; exit 1; }
command -v node >/dev/null 2>&1 || { echo "Node required"; exit 1; }
echo "  OK"

echo "[2/6] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "[3/6] Building frontend assets..."
npm ci --production
npm run build

echo "[4/6] Setting up configuration..."
[ ! -f .env ] && cp .env.example .env
php artisan key:generate
php artisan storage:link

echo "[5/6] Running migrations..."
php artisan migrate --force
[ "$1" = "--seed" ] && php artisan db:seed --force

echo "[6/6] Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan app:generate-sitemap
php artisan app:generate-llms-txt

echo ""
echo "=== Deployment complete ==="
echo "Next steps:"
echo "  1. Set SCRAPER_API_KEY in .env"
echo "  2. Add cron: * * * * * php $(pwd)/artisan schedule:run >> /dev/null 2>&1"
echo "  3. Add Python cron (weekly): 0 3 * * 0 cd $(pwd)/scraper && venv/bin/python scraper.py"
echo "  4. Submit $(pwd)/public/sitemap.xml to Google Search Console"
