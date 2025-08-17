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