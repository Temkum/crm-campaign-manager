#!/bin/bash
set -e

echo "Starting Laravel application setup..."

# Wait for database to be ready (optional)
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection..."
    until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
        echo "Database not ready, waiting..."
        sleep 2
    done
    echo "Database is ready!"
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

# Execute the main command
exec "$@"