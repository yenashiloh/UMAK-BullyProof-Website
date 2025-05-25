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
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy everything
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 755 storage bootstrap/cache

# Railway provides the PORT environment variable
EXPOSE $PORT

# Start Laravel on Railway's provided port
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}