FROM php:8.3-fpm

ARG UID
ARG UGROUP

WORKDIR /var/www

COPY .  /var/www/

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

RUN apt-get update && apt-get install -y \
    git \
    curl \
    nano \
    vim \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer

RUN chown -R www-data:www-data /var/www

RUN composer install
COPY ./dev-ops/start-container /usr/local/bin/start-container

RUN chmod +x /usr/local/bin/start-container
