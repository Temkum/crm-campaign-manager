# Stage 1: Build assets using Node and pnpm
FROM node:20-alpine AS node_modules
WORKDIR /app

# Install pnpm
RUN npm install -g pnpm

COPY package.json pnpm-lock.yaml ./
RUN pnpm install

COPY resources ./resources
COPY vite.config.js ./
RUN pnpm build

# Stage 2: PHP with Laravel
FROM php:8.3-fpm-alpine

# Install PHP dependencies
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
    make

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip intl opcache bcmath pcntl

# Install Redis via PECL
RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www/html

COPY --from=node_modules /app/public/build public/build
COPY . .

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Supervisor configuration
COPY docker/supervisord.conf /etc/supervisord.conf

# Nginx configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
