#!/bin/sh
set -e

# Default worker/process settings for Supervisor
export NUM_WORKERS="${NUM_WORKERS:-2}"
export ENABLE_HORIZON="${ENABLE_HORIZON:-false}"

# Configure Nginx port from environment (Render injects PORT). Default to 80.
PORT_TO_LISTEN=${PORT:-80}
REDIRECT_DISABLED=${DISABLE_HTTPS_REDIRECT:-true}

# Template nginx site conf to use the dynamic port
if [ -f /etc/nginx/sites-available/default ]; then
  sed -i "s/PORT_PLACEHOLDER/${PORT_TO_LISTEN}/g" /etc/nginx/sites-available/default
  # Optionally remove HTTPS redirect block when behind a TLS-terminating proxy
  if [ "$REDIRECT_DISABLED" = "true" ]; then
    awk '/# BEGIN_HTTPS_REDIRECT/{flag=1; next} /# END_HTTPS_REDIRECT/{flag=0; next} !flag {print}' \
      /etc/nginx/sites-available/default > /etc/nginx/sites-available/default.tmp && \
      mv /etc/nginx/sites-available/default.tmp /etc/nginx/sites-available/default
  fi
fi

# Ensure storage and cache directories exist with writable permissions
mkdir -p \
  /var/www/html/storage/app \
  /var/www/html/storage/framework/cache \
  /var/www/html/storage/framework/sessions \
  /var/www/html/storage/framework/views \
  /var/www/html/storage/logs \
  /var/www/html/bootstrap/cache

# Ensure correct ownership and permissions (idempotent)
chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache || true

# Create default log file if missing
if [ ! -f /var/www/html/storage/logs/laravel.log ]; then
  touch /var/www/html/storage/logs/laravel.log
  chown laravel:laravel /var/www/html/storage/logs/laravel.log
  chmod 664 /var/www/html/storage/logs/laravel.log
fi

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
php artisan config:clear || true
php artisan view:clear || true

exec "$@"