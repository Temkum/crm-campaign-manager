#!/bin/bash
set -e

echo "Starting Laravel application setup..."

# Validate environment variables
: "${ENV_SUPERVISOR_PASSWORD:=default_password}"
: "${ENV_NUM_WORKERS:=1}"
: "${ENV_ENABLE_HORIZON:=true}"
: "${ENV_APP_ENV:=production}"
: "${ENV_APP_NAME:=Laravel}"
export ENV_SUPERVISOR_PASSWORD ENV_NUM_WORKERS ENV_ENABLE_HORIZON ENV_APP_ENV ENV_APP_NAME

# Verify supervisord.conf exists
if [ ! -f /etc/supervisord.conf ]; then
    echo "Error: /etc/supervisord.conf not found!"
    exit 1
fi

# Wait for PostgreSQL database to be ready
if [ -n "$DB_HOST" ]; then
    echo "Waiting for PostgreSQL connection..."
    until PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" -c '\q' >/dev/null 2>&1; do
        echo "PostgreSQL not ready, waiting..."
        sleep 2
    done
    echo "PostgreSQL is ready!"
fi

# Generate application key if it doesn't exist
if [ ! -f .env ] || ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY= .env | cut -d'=' -f2)" ]; then
    echo "Generating application key..."
    php artisan key:generate --no-interaction
fi

# Create storage link
if [ ! -L public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link --no-interaction
fi

# Cache configuration for production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration for production..."
    php artisan config:cache --no-interaction
    php artisan route:cache --no-interaction
    php artisan view:cache --no-interaction
fi

# Run database migrations (optional - uncomment if needed)
# if [ "$RUN_MIGRATIONS" = "true" ]; then
#     echo "Running database migrations..."
#     php artisan migrate --no-interaction --force
# fi

echo "Laravel application setup completed!"
echo "Starting supervisord..."

# Execute the main command
exec "$@"