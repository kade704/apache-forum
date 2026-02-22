FROM php:8.2-apache

RUN apt-get update
RUN apt-get install -y ffmpeg

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

ARG UID=1000
ARG GID=1000
RUN groupmod -g ${GID} www-data && \
    usermod -u ${UID} -g www-data www-data

# php.ini-production을 기본 php.ini로 사용
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN { \
    echo "upload_max_filesize=100M"; \
    echo "post_max_size=100M"; \
    echo "memory_limit=256M"; \
    } >> "$PHP_INI_DIR/php.ini"
