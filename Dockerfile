FROM php:8.2-apache

# MySQL extension install
RUN docker-php-ext-install mysqli

# Apache rewrite enable (optional but useful)
RUN a2enmod rewrite

# Copy project files to Apache root
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
