#!/bin/sh
set -e

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
  echo "WARNING: Generating new APP_KEY"
  php artisan key:generate --force
fi

# Run migrations if enabled
if [ "$RUN_MIGRATIONS" = "true" ]; then
  php artisan migrate --force
fi

# Clear caches in case config changed
php artisan config:clear
php artisan view:clear

exec "$@"