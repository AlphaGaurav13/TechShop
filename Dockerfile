# PHP + Apache official image
FROM php:8.2-apache

# Working directory
WORKDIR /var/www/html

# PHP extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite
