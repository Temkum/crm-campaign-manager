# Stage 1: Build assets using Node and pnpm
FROM node:20-alpine AS node_builder

# Install pnpm
RUN corepack enable pnpm

WORKDIR /app

# Copy package files first for better layer caching
COPY package.json pnpm-lock.yaml ./

# Install ALL dependencies (including dev dependencies needed for build)
RUN pnpm install --frozen-lockfile  # Removed --prod flag

# Copy source files and build
COPY resources ./resources
COPY vite.config.js ./
COPY public ./public

# Set environment to production for proper Vite build
ENV NODE_ENV=production

# Build assets with production optimization
RUN pnpm build && \
    echo "Build completed, checking output..." && \
    ls -la public/build/ && \
    if [ ! -f public/build/manifest.json ]; then \
    echo "Error: Manifest file not found in build output!"; \
    exit 1; \
    fi

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

# Configure OPcache for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Clean up build dependencies
RUN apk del --no-cache autoconf gcc g++ make $PHPIZE_DEPS

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Create Laravel directory structure with proper permissions
RUN mkdir -p bootstrap/cache \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs && \
    chown -R www-data:www-data bootstrap storage && \
    chmod -R 775 bootstrap storage

# Install PHP dependencies as www-data
USER www-data
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application source
COPY --chown=www-data:www-data . .

# Optimize Laravel
RUN composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Switch back to root for system operations
USER root

# Copy built assets from node stage
COPY --from=node_builder --chown=www-data:www-data /app/public/build ./public/build

# Verify build assets
RUN echo "Build assets verification:" && \
    ls -la public/build/ && \
    if [ ! -f public/build/manifest.json ]; then \
    echo "Error: Production manifest file not found!"; \
    exit 1; \
    fi

# Laravel: force HTTPS in production
RUN echo "<?php\nif (app()->environment('production')) { \n    \URL::forceScheme('https'); \n}" > app/Providers/ForceHttps.php

# Copy production php.ini configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/php.ini

# Copy entrypoint and make executable
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]