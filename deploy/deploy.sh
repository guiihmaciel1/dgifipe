#!/bin/bash
# DG iFipe Deploy Script
# Usage: bash deploy/deploy.sh

set -e

echo "=== DG iFipe Deploy ==="
echo ""

cd /root/dgifipe

echo "1. Pulling latest code..."
git pull origin master

echo "2. Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "3. Building frontend..."
npm ci --production=false
npx vite build

echo "4. Running migrations..."
php artisan migrate --force

echo "5. Clearing caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "6. Restarting PHP-FPM..."
sudo systemctl reload php8.3-fpm

echo ""
echo "=== Deploy complete! ==="
