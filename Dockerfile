# Use PHP 8.2 with Apache
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache virtual host configuration
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Copy application files (will be overridden by volume mount in development)
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Create entrypoint script
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Install dependencies if vendor/autoload.php does not exist\n\
if [ ! -f "vendor/autoload.php" ]; then\n\
    echo "Installing composer dependencies..."\n\
    composer install --no-interaction --optimize-autoloader\n\
fi\n\
\n\
# Install npm dependencies if node_modules is empty or package-lock.json is not in node_modules\n\
if [ ! -d "node_modules/.bin" ]; then\n\
    echo "Installing npm dependencies..."\n\
    npm install\n\
fi\n\
\n\
# Fix permissions\n\
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/resources\n\
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache\n\
\n\

# Fix permissions for PHP files (allows www-data to read new files created from host)\n\
find /var/www/html/app -type f -name "*.php" -exec chmod 644 {} \\; 2>/dev/null || true\n\
find /var/www/html/resources -type f \\( -name "*.php" -o -name "*.blade.php" -o -name "*.js" \\) -exec chmod 644 {} \\; 2>/dev/null || true\n\
find /var/www/html/database -type f -name "*.php" -exec chmod 644 {} \\; 2>/dev/null || true\n\
find /var/www/html/routes -type f -name "*.php" -exec chmod 644 {} \\; 2>/dev/null || true\n\
find /var/www/html/tests -type f -name "*.php" -exec chmod 644 {} \\; 2>/dev/null || true\n\
\n\

# Execute the main command\n\
exec "$@"' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
