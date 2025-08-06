# TODO

```Dockerfile

FROM php:8.2-fpm

# Set working directory

WORKDIR /var/www

# Install system dependencies

RUN apt-get update && apt-get install -y \
 build-essential \
 libpng-dev \
 libonig-dev \
 libxml2-dev \
 zip \
 unzip \
 git \
 curl \
 libzip-dev \
 libpq-dev \
 libcurl4-openssl-dev \
 libssl-dev \
 nano \
 vim

# Install PHP extensions

RUN docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath gd

# Install Composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory

COPY . .

# Set permissions

RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Expose port 9000 and start php-fpm server

EXPOSE 9000
CMD ["php-fpm"]

```
