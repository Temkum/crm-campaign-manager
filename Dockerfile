# Stage 1: Build assets using Node and pnpm
FROM node:20-alpine AS node_builder

# Install pnpm
RUN corepack enable pnpm

WORKDIR /app

# Copy package files first for better layer caching
COPY package.json pnpm-lock.yaml ./

# Install ALL dependencies (including dev dependencies needed for build)
RUN pnpm install --frozen-lockfile

# Copy source files and build
COPY resources ./resources
COPY vite.config.js ./
COPY public ./public

# Set environment to production for proper Vite build
ENV NODE_ENV=production

# Build assets with production optimization and clean up
RUN pnpm build && \
    rm -rf node_modules && \
    if [ -f public/build/manifest.json ]; then \
    echo "Found manifest in standard location"; \
    elif [ -f public/build/.vite/manifest.json ]; then \
    echo "Found Vite manifest in .vite directory"; \
    mkdir -p public/build && \
    cp public/build/.vite/manifest.json public/build/manifest.json; \
    else \
    echo "Error: No manifest file found in build output!"; \
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
    python3 \
    py3-pip \
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

# Install Supervisor via pip
RUN pip3 install --no-cache-dir supervisor --break-system-packages

# Create laravel user and group
RUN addgroup -S laravel && adduser -S laravel -G laravel

WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set up Composer cache for faster builds
RUN mkdir -p /composer/cache && \
    chown laravel:laravel /composer/cache
ENV COMPOSER_HOME=/composer/cache

# Create Laravel directory structure with correct permissions
RUN mkdir -p bootstrap/cache \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    /var/log/supervisor /var/run && \
    chown -R laravel:laravel bootstrap storage /var/log/supervisor /var/run && \
    chmod -R 775 bootstrap storage && \
    chmod 755 /var/log/supervisor /var/run

# Copy ONLY composer files first for better caching
COPY composer.json composer.lock ./

# Switch to laravel user for Composer and Laravel operations
USER laravel

# Install dependencies without running scripts
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

# Copy the entire application, including .env.example
COPY --chown=laravel:laravel . .

# Ensure .env file exists by copying from .env.example if needed
RUN if [ ! -f .env ]; then \
    cp .env.example .env && \
    echo "Copied .env.example to .env"; \
    fi

# Run Laravel's package discovery manually
RUN php artisan package:discover --ansi

# Optimize Laravel
RUN composer dump-autoload --optimize && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Switch back to root for system operations
USER root

# Copy built assets from node stage
COPY --from=node_builder --chown=laravel:laravel /app/public/build ./public/build

# Verify build assets
RUN if [ ! -f public/build/manifest.json ]; then \
    echo "Error: Production manifest file not found!"; \
    exit 1; \
    fi

# Laravel: force HTTPS in production
RUN echo "<?php\nif (app()->environment('production')) { \n    \URL::forceScheme('https'); \n}" > app/Providers/ForceHttps.php

# Copy production php.ini configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/php.ini

# Copy PHP-FPM configuration
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy Nginx configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Copy supervisord configuration
COPY docker/supervisord.conf /etc/supervisord.conf
RUN chmod 644 /etc/supervisord.conf

# Copy entrypoint and make executable
COPY docker/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf", "-n"]