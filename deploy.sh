#!/bin/bash
#  chmod +x deploy.sh 
set -e

PROJECT_DIR="/var/www/virtual/finovast/finovaste"
PM2_PROCESS_NAME="finovaste-ssr"

echo "🚀 Starting deployment..."

cd "$PROJECT_DIR" || { echo "❌ Could not cd into $PROJECT_DIR. Aborting."; exit 1; }

php artisan down || true

echo "🔄 Pulling latest changes from git..."
git pull origin main

echo "📦 Installing Composer (PHP) dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "📦 Installing npm dependencies..."
npm ci

echo "🛠️ Building assets for production (SSR)..."
NODE_OPTIONS=--max-old-space-size=4096 npm run build:ssr

echo "🏃 Running database migrations..."
php artisan migrate --force

echo "🧹 Clearing old Laravel caches..."
php artisan optimize:clear

echo "🔥 Caching configuration for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🗺️ Generating sitemap..."
php artisan sitemap:generate

echo "⚙️ Reloading SSR service with new code..."
pm2 reload "$PM2_PROCESS_NAME"

php artisan up

echo "✅ Deployment finished successfully!"
