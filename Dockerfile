# Stage 1: Composer dependencies
FROM composer:2 as composer

WORKDIR /app
COPY database/ database/
COPY composer.json ./
RUN composer install --no-dev --no-interaction --no-scripts --prefer-dist --ignore-platform-reqs


# Stage 2: Frontend assets
FROM node:18 as node
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
RUN npm run prod


# Stage 3: Final application image
FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx     supervisor     curl     libzip-dev     libpng-dev     libjpeg-turbo-dev     freetype-dev     postgresql-dev     bash

# Install PHP extensions
# --- THIS SECTION HAS BEEN MODIFIED ---
# Removed the "-j$(nproc)" flag to prevent memory exhaustion on build.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    gd \
    zip \
    pdo \
    pdo_pgsql \
    exif \
    pcntl

# Copy application code and dependencies
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=node /app/public /var/www/html/public
COPY . /var/www/html

# Set permissions first, so artisan can write to cache
RUN mkdir -p /var/www/html/storage/framework/cache \
           /var/www/html/storage/framework/sessions \
           /var/www/html/storage/framework/views \
           /var/www/html/storage/logs \
           /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy composer binary
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Run composer scripts as superuser
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize --no-scripts

# Copy Nginx and Supervisor configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Entrypoint
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]