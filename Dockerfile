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

# Set environment to production for proper Vite build
ENV NODE_ENV=production

# Build assets with production optimization
RUN pnpm build && \
    echo "Build completed, checking output..." && \
    ls -la public/build/ && \
    find public/build -type f -name "*.json"

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

# Re-optimize autoload in case new files were copied
RUN composer dump-autoload --optimize

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

# Set permissions for build directory
RUN chown -R www-data:www-data public/build && \
    chmod -R 755 public/build

# Laravel: force HTTPS in production
RUN echo "<?php\\nif (app()->environment('production')) { \\n    \\URL::forceScheme('https'); \\n}" > app/Providers/ForceHttps.php

# Reminder: add to AppServiceProvider manually if needed.

# (Remaining config like Nginx, Supervisor, Entrypoint remains unchanged)

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Stay as root to avoid permission issues
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
