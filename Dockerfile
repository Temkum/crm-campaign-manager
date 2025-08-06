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

# Build assets
RUN pnpm build

# Stage 2: PHP with Laravel
FROM php:8.3-fpm-alpine

# Install system dependencies
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
    mysql-client \
    autoconf \
    gcc \
    g++ \
    make \
    libpng-dev \
    libxml2-dev \
    openssl-dev \
    $PHPIZE_DEPS

# Install core PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
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

# Create application user
RUN addgroup -g 1000 laravel && \
    adduser -u 1000 -G laravel -D laravel

# Set working directory
WORKDIR /var/www/html

# Copy built assets from node stage
COPY --from=node_builder /app/public/build ./public/build

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application source
COPY . .

# Set proper permissions BEFORE creating config files
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache && \
    chown -R laravel:laravel /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Create log directories with proper permissions
RUN mkdir -p /var/log/supervisor /var/log/nginx && \
    chown -R laravel:laravel /var/log/supervisor && \
    chmod 755 /var/log/supervisor

# Create configuration files using echo commands to avoid heredoc issues
RUN echo '[supervisord]' > /etc/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisord.conf && \
    echo 'logfile=/var/log/supervisor/supervisord.log' >> /etc/supervisord.conf && \
    echo 'pidfile=/var/run/supervisord.pid' >> /etc/supervisord.conf && \
    echo 'childlogdir=/var/log/supervisor' >> /etc/supervisord.conf && \
    echo 'loglevel=info' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[unix_http_server]' >> /etc/supervisord.conf && \
    echo 'file=/var/run/supervisor.sock' >> /etc/supervisord.conf && \
    echo 'chmod=0700' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[supervisorctl]' >> /etc/supervisord.conf && \
    echo 'serverurl=unix:///var/run/supervisor.sock' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[rpcinterface:supervisor]' >> /etc/supervisord.conf && \
    echo 'supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[program:nginx]' >> /etc/supervisord.conf && \
    echo 'command=nginx -g "daemon off;"' >> /etc/supervisord.conf && \
    echo 'user=root' >> /etc/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisord.conf && \
    echo 'stdout_logfile=/var/log/supervisor/nginx.log' >> /etc/supervisord.conf && \
    echo 'stderr_logfile=/var/log/supervisor/nginx.log' >> /etc/supervisord.conf && \
    echo 'priority=10' >> /etc/supervisord.conf && \
    echo '' >> /etc/supervisord.conf && \
    echo '[program:php-fpm]' >> /etc/supervisord.conf && \
    echo 'command=php-fpm -F' >> /etc/supervisord.conf && \
    echo 'user=root' >> /etc/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisord.conf && \
    echo 'stdout_logfile=/var/log/supervisor/php-fpm.log' >> /etc/supervisord.conf && \
    echo 'stderr_logfile=/var/log/supervisor/php-fpm.log' >> /etc/supervisord.conf && \
    echo 'priority=20' >> /etc/supervisord.conf && \
    chmod 644 /etc/supervisord.conf

# Create basic nginx configuration
RUN echo 'user laravel;' > /etc/nginx/nginx.conf && \
    echo 'worker_processes auto;' >> /etc/nginx/nginx.conf && \
    echo 'pid /run/nginx.pid;' >> /etc/nginx/nginx.conf && \
    echo 'error_log /var/log/nginx/error.log warn;' >> /etc/nginx/nginx.conf && \
    echo '' >> /etc/nginx/nginx.conf && \
    echo 'events {' >> /etc/nginx/nginx.conf && \
    echo '    worker_connections 1024;' >> /etc/nginx/nginx.conf && \
    echo '}' >> /etc/nginx/nginx.conf && \
    echo '' >> /etc/nginx/nginx.conf && \
    echo 'http {' >> /etc/nginx/nginx.conf && \
    echo '    include /etc/nginx/mime.types;' >> /etc/nginx/nginx.conf && \
    echo '    default_type application/octet-stream;' >> /etc/nginx/nginx.conf && \
    echo '    sendfile on;' >> /etc/nginx/nginx.conf && \
    echo '    keepalive_timeout 65;' >> /etc/nginx/nginx.conf && \
    echo '    client_max_body_size 25M;' >> /etc/nginx/nginx.conf && \
    echo '' >> /etc/nginx/nginx.conf && \
    echo '    server {' >> /etc/nginx/nginx.conf && \
    echo '        listen 80;' >> /etc/nginx/nginx.conf && \
    echo '        server_name _;' >> /etc/nginx/nginx.conf && \
    echo '        root /var/www/html/public;' >> /etc/nginx/nginx.conf && \
    echo '        index index.php;' >> /etc/nginx/nginx.conf && \
    echo '' >> /etc/nginx/nginx.conf && \
    echo '        location = /health {' >> /etc/nginx/nginx.conf && \
    echo '            return 200 "healthy\\n";' >> /etc/nginx/nginx.conf && \
    echo '            add_header Content-Type text/plain;' >> /etc/nginx/nginx.conf && \
    echo '        }' >> /etc/nginx/nginx.conf && \
    echo '' >> /etc/nginx/nginx.conf && \
    echo '        location / {' >> /etc/nginx/nginx.conf && \
    echo '            try_files $uri $uri/ /index.php?$query_string;' >> /etc/nginx/nginx.conf && \
    echo '        }' >> /etc/nginx/nginx.conf && \
    echo '' >> /etc/nginx/nginx.conf && \
    echo '        location ~ \\.php$ {' >> /etc/nginx/nginx.conf && \
    echo '            try_files $uri =404;' >> /etc/nginx/nginx.conf && \
    echo '            fastcgi_pass 127.0.0.1:9000;' >> /etc/nginx/nginx.conf && \
    echo '            fastcgi_index index.php;' >> /etc/nginx/nginx.conf && \
    echo '            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' >> /etc/nginx/nginx.conf && \
    echo '            include fastcgi_params;' >> /etc/nginx/nginx.conf && \
    echo '        }' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '}' >> /etc/nginx/nginx.conf

# Create PHP configuration
RUN echo '[PHP]' > /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'expose_php = Off' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'display_errors = Off' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'log_errors = On' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'memory_limit = 256M' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'max_execution_time = 30' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'upload_max_filesize = 20M' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'post_max_size = 25M' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'date.timezone = UTC' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'opcache.enable = 1' >> /usr/local/etc/php/conf.d/99-custom.ini && \
    echo 'opcache.memory_consumption = 128' >> /usr/local/etc/php/conf.d/99-custom.ini

# Create PHP-FPM configuration
RUN echo '[www]' > /usr/local/etc/php-fpm.d/www.conf && \
    echo 'user = laravel' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'group = laravel' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 20' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 10' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'clear_env = no' >> /usr/local/etc/php-fpm.d/www.conf

# Create simple entrypoint script
RUN echo '#!/bin/bash' > /usr/local/bin/entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Starting Laravel application..."' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Ensure proper permissions' >> /usr/local/bin/entrypoint.sh && \
    echo 'chown -R laravel:laravel /var/www/html/storage /var/www/html/bootstrap/cache' >> /usr/local/bin/entrypoint.sh && \
    echo 'chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Handle .env file' >> /usr/local/bin/entrypoint.sh && \
    echo 'if [ ! -f .env ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    if [ -f .env.example ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '        cp .env.example .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo ".env file created from .env.example"' >> /usr/local/bin/entrypoint.sh && \
    echo '    else' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_KEY=" > .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_ENV=production" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "APP_DEBUG=false" >> .env' >> /usr/local/bin/entrypoint.sh && \
    echo '        echo "Minimal .env file created"' >> /usr/local/bin/entrypoint.sh && \
    echo '    fi' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Generate application key' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan key:generate --no-interaction --force 2>/dev/null || true' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo '# Create storage link' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan storage:link --no-interaction 2>/dev/null || true' >> /usr/local/bin/entrypoint.sh && \
    echo '' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "Laravel application setup completed!"' >> /usr/local/bin/entrypoint.sh && \
    echo 'exec "$@"' >> /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Switch to non-root user for security
USER laravel

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]