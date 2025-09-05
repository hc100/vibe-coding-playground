#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

# Ensure expected directories exist
mkdir -p storage bootstrap/cache

# Composer install if vendor is missing (bind-mounted dev workflow)
if [ ! -d vendor ]; then
  echo "[php] Running composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if not set
if [ -f .env ]; then
  if ! grep -q "^APP_KEY=" .env || [ -z "$(grep "^APP_KEY=" .env | cut -d= -f2-)" ]; then
    echo "[php] Generating APP_KEY..."
    php artisan key:generate --force || true
  fi
fi

# Ensure storage permissions
chmod -R ug+rw storage bootstrap/cache || true

exec "$@"

