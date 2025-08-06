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

## Hosting Options Comparison for Laravel/PHP

| Feature / Use Case         | **Render**                       | **Railway**                      | **Hetzner**                           |
| -------------------------- | -------------------------------- | -------------------------------- | ------------------------------------- |
| **Ease of setup**          | ⭐️⭐️⭐️⭐️⭐️ (Very simple)    | ⭐️⭐️⭐️⭐️⭐️ (Very simple)    | ⭐️⭐️ (Needs manual setup)           |
| **Best for**               | Full-stack apps, hobby → prod    | Quick deployments, side projects | Custom VPS, performance, self-hosting |
| **Supports Laravel**       | ✅ (Docker or native buildpacks) | ✅ (Docker or Nixpacks)          | ✅ (Manual LAMP/LEMP or Docker)       |
| **Free tier**              | ✅ (Web services with limits)    | ✅ (Limited hours/month)         | ❌ (No free tier, low-cost VPS)       |
| **Pricing (starter)**      | Free → \$7/month                 | Free → \$5/month                 | €4.15/month VPS (CX11)                |
| **Deployment method**      | Git + Docker or Buildpacks       | Git + Docker/Nixpacks            | Manual (SSH, Git, Docker, CI/CD)      |
| **Database support**       | Built-in PostgreSQL, Redis, etc. | Built-in PostgreSQL, MySQL       | Self-managed or install manually      |
| **Custom domains + HTTPS** | ✅ Free HTTPS                    | ✅ Free HTTPS                    | ✅ Manual setup with Let's Encrypt    |
| **Performance**            | ⭐️⭐️⭐️⭐️ (good for mid apps) | ⭐️⭐️⭐️ (good for light apps)  | ⭐️⭐️⭐️⭐️⭐️ (full control, fast)  |
| **Scalability**            | ✅ Horizontal (paid tiers)       | ✅ Limited in free plan          | ✅ Manual scaling                     |
| **Persistent storage**     | ✅ (via disks on paid plans)     | ✅ (project volumes)             | ✅ Full disk control                  |
| **Queue/Worker support**   | ✅ via Background Workers        | ✅ via Services                  | ✅ (You set it up)                    |
