# Use the official PHP 8.2 image with Apache
FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    gnupg2 \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install PHP dependencies using Composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Copy the entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Verify entrypoint script exists and set executable permissions
RUN ls -la /usr/local/bin/ && chmod +x /usr/local/bin/entrypoint.sh

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Use the custom entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
