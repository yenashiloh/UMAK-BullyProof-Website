FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libzip-dev \
    unzip \
    git \
    curl

# Configure and install GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd pdo zip curl

# Install MongoDB extension (specific version to match requirements)
RUN pecl install mongodb-1.18.1 && docker-php-ext-enable mongodb

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Install with platform requirements ignored for version conflicts
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-mongodb

RUN chmod -R 755 storage bootstrap/cache

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}