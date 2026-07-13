FROM php:8.2-apache

RUN docker-php-ext-install mysqli
RUN a2enmod rewrite

RUN mkdir -p /data/images
RUN chown -R www-data:www-data /data/images