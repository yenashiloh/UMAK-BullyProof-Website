FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libzip-dev \
    unzip \
    git \
    curl

# Install MongoDB extension
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Install other PHP extensions
RUN docker-php-ext-install pdo zip curl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Copy application code
COPY . .

# Create storage and cache directories
RUN mkdir -p storage/logs storage/framework/sessions storage/framework/views storage/framework/cache
RUN mkdir -p bootstrap/cache

# Set permissions
RUN chmod -R 755 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 8000

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]