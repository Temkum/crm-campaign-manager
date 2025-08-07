# Stage 1: Node builder for Vite assets
FROM node:20-alpine AS node_builder

# Install pnpm and build dependencies
RUN corepack enable && \
    apk add --no-cache git python3 make g++

WORKDIR /app

# Cache pnpm dependencies
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile --prod=false

# Copy and build assets
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public ./public

ARG VITE_APP_ENV=production
ARG VITE_APP_URL
ARG VITE_APP_NAME

RUN pnpm build && \
    # Handle both Vite 3 and 4 manifest locations
    if [ ! -f public/build/manifest.json ] && [ ! -f public/build/.vite/manifest.json ]; then \
    echo "Error: Vite manifest not found in either location!"; \
    find public/build -type f; \
    exit 1; \
    fi && \
    # Standardize manifest location for Laravel
    if [ -f public/build/.vite/manifest.json ]; then \
    mkdir -p public/build && \
    cp public/build/.vite/manifest.json public/build/manifest.json; \
    fi && \
    # Clean up dev dependencies
    pnpm prune --prod && \
    rm -rf node_modules/.cache

# Stage 2: PHP base with extensions
FROM php:8.3-fpm-alpine AS php_base

# Install build dependencies first
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    autoconf \
    make \
    g++ \
    linux-headers \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libpng-dev \
    libxml2-dev \
    shadow

# Install runtime dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-client \
    libzip \
    icu \
    oniguruma \
    libpng \
    libxml2 \
    libstdc++

# Install PHP extensions
RUN docker-php-ext-install \
    opcache \
    pdo \
    pdo_pgsql \
    pgsql \
    bcmath \
    intl \
    zip \
    gd \
    pcntl

# Install Redis extension
RUN pecl install -o -f redis \
    && docker-php-ext-enable redis \
    && rm -rf /tmp/pear

# Clean up build dependencies
RUN apk del --no-cache .build-deps

# Production php.ini
COPY docker/prod/php.ini /usr/local/etc/php/conf.d/production.ini

# Stage 3: Final production image
FROM php_base

# Create application user with Alpine-compatible commands
RUN addgroup -g 1000 -S laravel && \
    adduser -u 1000 -S laravel -G laravel -h /var/www/html -s /bin/sh && \
    mkdir -p /var/www/html && \
    chown laravel:laravel /var/www/html

WORKDIR /var/www/html

# Install production Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files with proper permissions
COPY --chown=laravel:laravel . .

# Copy built assets from node stage
COPY --from=node_builder --chown=laravel:laravel /app/public/build ./public/build

# Runtime directories
RUN mkdir -p \
    storage/framework/{cache,sessions,views} \
    storage/logs \
    bootstrap/cache && \
    chown -R laravel:laravel \
    storage \
    bootstrap/cache && \
    chmod -R 775 \
    storage \
    bootstrap/cache

# Production Composer install (no dev dependencies)
USER laravel
RUN composer install --no-dev --no-interaction --optimize-autoloader --ignore-platform-reqs && \
    composer dump-autoload --optimize

# Production optimizations
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

USER root

# Configure supervisor
COPY docker/prod/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configure nginx
COPY docker/prod/nginx.conf /etc/nginx/nginx.conf
COPY docker/prod/nginx-site.conf /etc/nginx/sites-available/default

# Health check endpoint
# COPY docker/prod/healthcheck.php /var/www/html/public/health

# Entrypoint
COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Render-specific optimizations
ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    OPACHE_ENABLE=1

HEALTHCHECK --interval=30s --timeout=10s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]