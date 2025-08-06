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

# Copy configuration files
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Switch to non-root user for security
USER laravel

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]