FROM php:7.4-apache

# Install Some Additional Packages
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    unzip \
    libaio1 \
    jpegoptim optipng pngquant gifsicle \
    curl \
    wget \
    firebird-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists*

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


RUN docker-php-ext-install pdo_firebird pdo_mysql