FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Increase PHP memory limit for Composer
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy composer files first (for better caching)
COPY composer.json ./

# Install PHP dependencies with increased memory
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Configure Apache
RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

# Expose port
EXPOSE 10000

# Start Apache on Render's port
CMD sed -i "s/80/${PORT:-10000}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && apache2-foreground
