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
    php-dev \
    shadow

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
RUN groupadd -g 1000 laravel && \
    useradd -u 1000 -g laravel -m laravel

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

# Set proper permissions
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views} bootstrap/cache && \
    chown -R laravel:laravel /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Create configuration files inline to avoid dependency on external files
RUN cat > /etc/supervisord.conf << 'EOF'
[supervisord]
nodaemon=true
# user=root (run supervisord with root privileges)
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid
childlogdir=/var/log/supervisor
loglevel=info

[unix_http_server]
file=/var/run/supervisor.sock
chmod=0700

[supervisorctl]
serverurl=unix:///var/run/supervisor.sock

[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface

[program:nginx]
command=nginx -g "daemon off;"
user=root
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/nginx.log
stderr_logfile=/var/log/supervisor/nginx.log
priority=10

[program:php-fpm]
command=php-fpm -F
user=root
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/php-fpm.log
stderr_logfile=/var/log/supervisor/php-fpm.log
priority=20
EOF

# Create nginx configuration
RUN cat > /etc/nginx/nginx.conf << 'EOF'
user laravel;
worker_processes auto;
pid /run/nginx.pid;
error_log /var/log/nginx/error.log warn;

events {
worker_connections 1024;
use epoll;
multi_accept on;
}

http {
include /etc/nginx/mime.types;
default_type application/octet-stream;

log_format main '$remote_addr - $remote_user [$time_local] "$request" '
'$status $body_bytes_sent "$http_referer" '
'"$http_user_agent" "$http_x_forwarded_for"';
access_log /var/log/nginx/access.log main;

sendfile on;
tcp_nopush on;
tcp_nodelay on;
keepalive_timeout 65;
client_max_body_size 25M;

gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header X-Content-Type-Options "nosniff" always;

server {
listen 80;
server_name _;
root /var/www/html/public;
index index.php;

location = /health {
access_log off;
return 200 "healthy\n";
add_header Content-Type text/plain;
}

location / {
try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
try_files $uri =404;
fastcgi_split_path_info ^(.+\.php)(/.+)$;
fastcgi_pass 127.0.0.1:9000;
fastcgi_index index.php;
fastcgi_param SCRIPT_FILENAME $document_root$fast.php;
include fastcgi_params;
fastcgi_hide_header X-Powered-By;
fastcgi_read_timeout 300;
}

location ~ /\. {
deny all;
}
}
}
EOF

# Create PHP configuration
RUN cat > /usr/local/etc/php/conf.d/99-custom.ini << 'EOF'
[PHP]
expose_php = Off
display_errors = Off
log_errors = On
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 20M
post_max_size = 25M
date.timezone = UTC

opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60
opcache.fast_shutdown = 1
EOF

# Create PHP-FPM configuration
RUN cat > /usr/local/etc/php-fpm.d/www.conf << 'EOF'
[www]
user = laravel
group = laravel
listen = 127.0.0.1:9000
listen.owner = laravel
listen.group = laravel
listen.mode = 0660

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500

request_terminate_timeout = 300
security.limit_extensions = .php
clear_env = no
EOF

# Create entrypoint script
RUN cat > /usr/local/bin/entrypoint.sh << 'EOF'
#!/bin/bash
set -e

echo "Starting Laravel application setup..."

# Generate application key if it doesn't exist
if [ ! -f .env ] || ! grep -q "APP_KEY=" .env ||
[ ! -z "$(grep APP_KEY= .env | cut -d'=' -f2)" ]; then
echo "Generating application key..."
php artisan key:generate --no-interaction --force
fi

# Create storage link
if [ ! -L public/storage ]; then
echo "Creating storage link..."
php artisan storage:link --no-interaction || true
fi

# Cache configuration for production
if [ "$APP_ENV" = "production" ]; then
echo "Caching configuration for production..."
php artisan config:cache --no-interaction || true
php artisan route:cache --no-interaction || true
php artisan view:cache --no-interaction || true
fi

echo "Laravel application setup completed!"
exec "$@"
EOF

RUN chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Switch to non-root user for security
USER laravel

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]