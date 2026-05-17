# APIForge Deployment Script (Windows / aaPanel VPS)
# Run as Administrator on the production server

param(
    [switch]$Seed = $false
)

$ErrorActionPreference = "Stop"

Write-Host "=== APIForge Deployment ===" -ForegroundColor Cyan

# 1. Environment
Write-Host "[1/6] Checking environment..." -ForegroundColor Yellow
if (-not (Get-Command php -ErrorAction SilentlyContinue)) { throw "PHP not found" }
if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw "Composer not found" }
if (-not (Get-Command node -ErrorAction SilentlyContinue)) { throw "Node not found" }
if (-not (Get-Command npm -ErrorAction SilentlyContinue)) { throw "npm not found" }
Write-Host "  PHP $(php -r 'echo PHP_VERSION;') | Composer $(composer --version 2>$null | Select-String '\d+\.\d+\.\d+') | Node $(node -v)" -ForegroundColor Green

# 2. Backend
Write-Host "[2/6] Installing Composer dependencies..." -ForegroundColor Yellow
composer install --no-dev --optimize-autoloader
if ($LASTEXITCODE -ne 0) { throw "Composer install failed" }

# 3. Frontend
Write-Host "[3/6] Building frontend assets..." -ForegroundColor Yellow
npm ci --production
if ($LASTEXITCODE -ne 0) { throw "npm ci failed" }
npm run build
if ($LASTEXITCODE -ne 0) { throw "npm build failed" }

# 4. Config
Write-Host "[4/6] Setting up configuration..." -ForegroundColor Yellow
if (-not (Test-Path ".env")) { Copy-Item ".env.example" ".env" }
php artisan key:generate
php artisan storage:link

# 5. Database
Write-Host "[5/6] Running migrations..." -ForegroundColor Yellow
php artisan migrate --force
if ($Seed) {
    Write-Host "  Seeding database..." -ForegroundColor Yellow
    php artisan db:seed --force
}

# 6. Optimize
Write-Host "[6/6] Optimizing for production..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan app:generate-sitemap
php artisan app:generate-llms-txt

Write-Host "`n=== Deployment complete ===" -ForegroundColor Cyan
Write-Host "Next steps:" -ForegroundColor White
Write-Host "  1. Set SCRAPER_API_KEY in .env" -ForegroundColor Gray
Write-Host "  2. Add cron: * * * * * php /path/to/artisan schedule:run" -ForegroundColor Gray
Write-Host "  3. Add Python cron (weekly): 0 3 * * 0 cd /path/to/scraper && venv/bin/python scraper.py" -ForegroundColor Gray
Write-Host "  4. Submit sitemap.xml to Google Search Console" -ForegroundColor Gray
