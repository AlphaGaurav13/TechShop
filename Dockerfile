FROM php:8.2-apache

WORKDIR /var/www/html

COPY index.php .
COPY Admin ./Admin
COPY client ./client
COPY server ./server
COPY partials ./partials
COPY images ./images

RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN a2enmod rewrite
