# Stage 1: Composer dependencies
FROM composer:2 as composer

WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist


# Stage 2: Frontend assets
FROM node:18 as node
WORKDIR /app
COPY package.json ./
RUN npm install
COPY . .
RUN npm run build


# Stage 3: Final application image
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    zip \
    pdo \
    pdo_mysql \
    exif \
    pcntl

# Copy application code and dependencies
COPY --from=composer /app/vendor /var/www/html/vendor
COPY --from=node /app/public /var/www/html/public
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx and Supervisor configurations
# These files will be created in the next step
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Entrypoint
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
