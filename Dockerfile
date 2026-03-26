FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libldap2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
 && docker-php-ext-install -j$(nproc) \
      mysqli pdo_mysql gd intl zip ldap \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers env dir mime

COPY web/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html
