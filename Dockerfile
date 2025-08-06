# Stage 1: Build assets using Node and pnpm
FROM node:20-alpine AS node_builder

# Install pnpm
RUN corepack enable pnpm

WORKDIR /app

# Copy package files first for better layer caching
COPY package.json pnpm-lock.yaml ./

# Install dependencies
RUN pnpm install --frozen-lockfile

# Copy source files and build
COPY resources ./resources
COPY vite.config.js ./
COPY public ./public

# Build assets with production optimization
RUN pnpm build

# Stage 2: PHP with Laravel
FROM php:8.3-fpm-alpine

# Install system dependencies including PostgreSQL
RUN apk add --no-cache \
    bash \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    nginx \
    supervisor \
    postgresql-client \
    postgresql-dev \
    autoconf \
    gcc \
    g++ \
    make \
    libpng-dev \
    libxml2-dev \
    openssl-dev \
    $PHPIZE_DEPS

# Install core PHP extensions including PostgreSQL
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    bcmath \
    pcntl \
    opcache \
    gd

# Install Redis via PECL
RUN pecl update-channels && \
    pecl install redis && \
    docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application source
COPY . .

# Copy built assets from node stage with proper manifest
COPY --from=node_builder /app/public/build ./public/build

# Verify build assets exist and show manifest content
RUN ls -la public/build/ && \
    echo "Checking for manifest files..." && \
    find public/build -name "*.json" -type f && \
    if [ -f public/build/manifest.json ]; then \
    echo "Manifest found:"; \
    cat public/build/manifest.json; \
    elif [ -f public/build/.vite/manifest.json ]; then \
    echo "Vite manifest found in .vite directory, moving..."; \
    mv public/build/.vite/manifest.json public/build/manifest.json; \
    else \
    echo "No manifest found, checking build structure..."; \
    find public/build -type f; \
    fi

# Create Laravel directories and set permissions (as root)
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache && \
    mkdir -p /var/log/nginx /var/run && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

# Create PHP configuration optimized for production
RUN echo '[PHP]' > /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'expose_php = Off' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'display_errors = Off' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'log_errors = On' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'max_execution_time = 60' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'post_max_size = 25M' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'date.timezone = UTC' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'opcache.enable = 1' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'opcache.memory_consumption = 256' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'opcache.max_accelerated_files = 20000' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'opcache.validate_timestamps = 0' >> /usr/local/etc/php/conf.d/99-custom.ini

# Create nginx configuration with proper asset handling
RUN printf 'user www-data;\n' > /etc/nginx/nginx.conf && \
    printf 'worker_processes auto;\n' >> /etc/nginx/nginx.conf && \
    printf 'pid /var/run/nginx.pid;\n' >> /etc/nginx/nginx.conf && \
    printf 'error_log /var/log/nginx/error.log warn;\n\n' >> /etc/nginx/nginx.conf && \
    printf 'events {\n' >> /etc/nginx/nginx.conf && \
    printf '    worker_connections 1024;\n' >> /etc/nginx/nginx.conf && \
    printf '    use epoll;\n' >> /etc/nginx/nginx.conf && \
    printf '}\n\n' >> /etc/nginx/nginx.conf && \
    printf 'http {\n' >> /etc/nginx/nginx.conf && \
    printf '    include /etc/nginx/mime.types;\n' >> /etc/nginx/nginx.conf && \
    printf '    default_type application/octet-stream;\n' >> /etc/nginx/nginx.conf && \
    printf '    sendfile on;\n' >> /etc/nginx/nginx.conf && \
    printf '    tcp_nopush on;\n' >> /etc/nginx/nginx.conf && \
    printf '    tcp_nodelay on;\n' >> /etc/nginx/nginx.conf && \
    printf '    keepalive_timeout 65;\n' >> /etc/nginx/nginx.conf && \
    printf '    client_max_body_size 25M;\n' >> /etc/nginx/nginx.conf && \
    printf '    gzip on;\n' >> /etc/nginx/nginx.conf && \
    printf '    gzip_vary on;\n' >> /etc/nginx/nginx.conf && \
    printf '    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;\n\n' >> /etc/nginx/nginx.conf && \
    printf '    server {\n' >> /etc/nginx/nginx.conf && \
    printf '        listen 80;\n' >> /etc/nginx/nginx.conf && \
    printf '        server_name _;\n' >> /etc/nginx/nginx.conf && \
    printf '        root /var/www/html/public;\n' >> /etc/nginx/nginx.conf && \
    printf '        index index.php index.html;\n\n' >> /etc/nginx/nginx.conf && \
    printf '        # Security headers\n' >> /etc/nginx/nginx.conf && \
    printf '        add_header X-Frame-Options "SAMEORIGIN" always;\n' >> /etc/nginx/nginx.conf && \
    printf '        add_header X-Content-Type-Options "nosniff" always;\n' >> /etc/nginx/nginx.conf && \
    printf '        add_header X-XSS-Protection "1; mode=block" always;\n\n' >> /etc/nginx/nginx.conf && \
    printf '        location = /health {\n' >> /etc/nginx/nginx.conf && \
    printf '            return 200 "healthy\\n";\n' >> /etc/nginx/nginx.conf && \
    printf '            add_header Content-Type text/plain;\n' >> /etc/nginx/nginx.conf && \
    printf '            access_log off;\n' >> /etc/nginx/nginx.conf && \
    printf '        }\n\n' >> /etc/nginx/nginx.conf && \
    printf '        # Handle Vite build assets with aggressive caching\n' >> /etc/nginx/nginx.conf && \
    printf '        location /build/ {\n' >> /etc/nginx/nginx.conf && \
    printf '            expires 1y;\n' >> /etc/nginx/nginx.conf && \
    printf '            add_header Cache-Control "public, immutable, no-transform";\n' >> /etc/nginx/nginx.conf && \
    printf '            add_header Vary "Accept-Encoding";\n' >> /etc/nginx/nginx.conf && \
    printf '            try_files $uri =404;\n' >> /etc/nginx/nginx.conf && \
    printf '        }\n\n' >> /etc/nginx/nginx.conf && \
    printf '        # Static assets with versioning\n' >> /etc/nginx/nginx.conf && \
    printf '        location ~* \\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot|webp|avif)$ {\n' >> /etc/nginx/nginx.conf && \
    printf '            expires 1y;\n' >> /etc/nginx/nginx.conf && \
    printf '            add_header Cache-Control "public, immutable, no-transform";\n' >> /etc/nginx/nginx.conf && \
    printf '            add_header Vary "Accept-Encoding";\n' >> /etc/nginx/nginx.conf && \
    printf '            try_files $uri =404;\n' >> /etc/nginx/nginx.conf && \
    printf '        }\n\n' >> /etc/nginx/nginx.conf && \
    printf '        # Laravel routes\n' >> /etc/nginx/nginx.conf && \
    printf '        location / {\n' >> /etc/nginx/nginx.conf && \
    printf '            try_files $uri $uri/ /index.php?$query_string;\n' >> /etc/nginx/nginx.conf && \
    printf '        }\n\n' >> /etc/nginx/nginx.conf && \
    printf '        # PHP FastCGI\n' >> /etc/nginx/nginx.conf && \
    printf '        location ~ \\.php$ {\n' >> /etc/nginx/nginx.conf && \
    printf '            try_files $uri =404;\n' >> /etc/nginx/nginx.conf && \
    printf '            fastcgi_split_path_info ^(.+\\.php)(/.+)$;\n' >> /etc/nginx/nginx.conf && \
    printf '            fastcgi_pass 127.0.0.1:9000;\n' >> /etc/nginx/nginx.conf && \
    printf '            fastcgi_index index.php;\n' >> /etc/nginx/nginx.conf && \
    printf '            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n' >> /etc/nginx/nginx.conf && \
    printf '            fastcgi_param PATH_INFO $fastcgi_path_info;\n' >> /etc/nginx/nginx.conf && \
    printf '            fastcgi_read_timeout 300;\n' >> /etc/nginx/nginx.conf && \
    printf '            include fastcgi_params;\n' >> /etc/nginx/nginx.conf && \
    printf '        }\n\n' >> /etc/nginx/nginx.conf && \
    printf '        # Deny access to sensitive files\n' >> /etc/nginx/nginx.conf && \
    printf '        location ~ /\\. {\n' >> /etc/nginx/nginx.conf && \
    printf '            deny all;\n' >> /etc/nginx/nginx.conf && \
    printf '        }\n' >> /etc/nginx/nginx.conf && \
    printf '    }\n' >> /etc/nginx/nginx.conf && \
    printf '}\n' >> /etc/nginx/nginx.conf

# Create supervisor configuration
RUN echo '[supervisord]' > /etc/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisord.conf && \
    echo 'user=root' >> /etc/supervisord.conf && \
    echo 'logfile=/tmp/supervisord.log' >> /etc/supervisord.conf && \
    echo 'pidfile=/tmp/supervisord.pid' >> /etc/supervisord.conf && \
    echo 'loglevel=info' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[program:nginx]' >> /etc/supervisord.conf && \
    echo 'command=nginx -g "daemon off;"' >> /etc/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisord.conf && \
    echo 'stdout_logfile=/dev/stdout' >> /etc/supervisord.conf && \
    echo 'stdout_logfile_maxbytes=0' >> /etc/supervisord.conf && \
    echo 'stderr_logfile=/dev/stderr' >> /etc/supervisord.conf && \
    echo 'stderr_logfile_maxbytes=0' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[program:php-fpm]' >> /etc/supervisord.conf && \
    echo 'command=php-fpm -F' >> /etc/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisord.conf && \
    echo 'stdout_logfile=/dev/stdout' >> /etc/supervisord.conf && \
    echo 'stdout_logfile_maxbytes=0' >> /etc/supervisord.conf && \
    echo 'stderr_logfile=/dev/stderr' >> /etc/supervisord.conf && \
    echo 'stderr_logfile_maxbytes=0' >> /etc/supervisord.conf

# Create entrypoint script with PostgreSQL support and asset verification
RUN echo '#!/bin/bash' > /usr/local/bin/entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Starting Laravel application setup..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Ensure directories exist and have proper permissions' >> /usr/local/bin/entrypoint.sh && \
    echo 'mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache' >> /usr/local/bin/entrypoint.sh && \
    echo 'chmod -R 775 storage bootstrap/cache' >> /usr/local/bin/entrypoint.sh && \
    echo 'chown -R www-data:www-data storage bootstrap/cache' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Debug assets and manifest' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=== ASSET DEBUGGING ==="' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Public directory contents:"' >> /usr/local/bin/entrypoint.sh && \
    echo 'ls -la public/' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Build directory contents:"' >> /usr/local/bin/entrypoint.sh && \
    echo 'ls -la public/build/ 2>/dev/null || echo "Build directory not found"' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Manifest file:"' >> /usr/local/bin/entrypoint.sh && \
    echo 'cat public/build/manifest.json 2>/dev/null | head -20 || echo "Manifest not found"' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "=========================="' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Handle environment file with PostgreSQL defaults' >> /usr/local/bin/entrypoint.sh && \
    echo 'if [ ! -f .env ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    if [ -f .env.example ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '        cp .env.example .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo ".env created from .env.example"' >> /usr/local/bin/entrypoint.sh && \
    echo '    else' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_NAME=Laravel" > .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_ENV=production" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_KEY=" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_DEBUG=false" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_URL=http://localhost" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "DB_CONNECTION=pgsql" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "DB_HOST=${DATABASE_HOST:-localhost}" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "DB_PORT=${DATABASE_PORT:-5432}" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "DB_DATABASE=${DATABASE_NAME:-laravel}" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "DB_USERNAME=${DATABASE_USER:-postgres}" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "DB_PASSWORD=${DATABASE_PASSWORD:-}" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "VIEW_COMPILED_PATH=/var/www/html/storage/framework/views" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "Basic .env created with PostgreSQL"' >> /usr/local/bin/entrypoint.sh && \
    echo '    fi' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Update database configuration if environment variables exist' >> /usr/local/bin/entrypoint.sh && \
    echo 'if [ ! -z "${DATABASE_URL}" ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "DATABASE_URL=${DATABASE_URL}" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo 'elif [ ! -z "${DATABASE_HOST}" ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_HOST=.*/DB_HOST=${DATABASE_HOST}/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_PORT=.*/DB_PORT=${DATABASE_PORT:-5432}/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DATABASE_NAME}/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DATABASE_USER}/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo '    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DATABASE_PASSWORD}/" .env' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Ensure VIEW_COMPILED_PATH is set' >> /usr/local/bin/entrypoint.sh && \
    echo 'if ! grep -q "VIEW_COMPILED_PATH" .env; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "VIEW_COMPILED_PATH=/var/www/html/storage/framework/views" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Generate application key' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan key:generate --no-interaction --force || echo "Key generation failed, continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Test database connection' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Testing database connection..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan migrate:status || echo "Database connection test failed, but continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Run migrations if database is available' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan migrate --no-interaction --force || echo "Migrations failed, continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Create storage link' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan storage:link --no-interaction || echo "Storage link failed, continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Clear and cache configuration' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan config:cache || echo "Config cache failed, continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan route:cache || echo "Route cache failed, continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan view:cache || echo "View cache failed, continuing..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Laravel setup completed!"' >> /usr/local/bin/entrypoint.sh && \
    echo 'exec "$@"' >> /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Stay as root to avoid permission issues
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]