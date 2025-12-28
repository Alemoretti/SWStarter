FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    pkg-config \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 24 LTS (latest LTS) and update npm to latest
RUN curl -fsSL https://deb.nodesource.com/setup_24.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Set npm cache directory and fix permissions for www-data user
RUN mkdir -p /var/www/.npm && \
    chown -R www-data:www-data /var/www/.npm && \
    npm config set cache /var/www/.npm --global

# Create psysh config directory with proper permissions
RUN mkdir -p /var/www/.config/psysh && \
    chown -R www-data:www-data /var/www/.config

# Install Redis PHP extension
RUN pecl install redis && docker-php-ext-enable redis

# Copy existing application directory permissions
RUN chown -R www-data:www-data /var/www/html

# Change current user to www-data
USER www-data

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]