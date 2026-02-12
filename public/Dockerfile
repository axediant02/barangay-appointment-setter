# Official PHP image with Apache (PHP 7.4 to match your environment)
FROM php:7.4-apache

# Install system dependencies and PDO MySQL extension
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git && \
    docker-php-ext-install pdo pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Enable rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files from parent directory (one level up)
COPY .. /var/www/html/

# Set Apache document root to public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Ensure www-data owns the files
RUN chown -R www-data:www-data /var/www/html

# Expose port (Render will provide its own port via $PORT)
EXPOSE 80

# Default command (Render will run the container; keep as apache)
CMD ["apache2-foreground"]
