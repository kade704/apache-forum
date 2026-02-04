FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

ARG UID=1000
ARG GID=1000
RUN groupmod -g ${GID} www-data && \
    usermod -u ${UID} -g www-data www-data

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
