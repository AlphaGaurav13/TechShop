FROM php:8.2-apache

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/client

RUN sed -ri 's!/var/www/html!/var/www/html/client!g' /etc/apache2/sites-available/*.conf
RUN sed -ri 's!/var/www/!/var/www/html/client!g' /etc/apache2/apache2.conf

COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html
