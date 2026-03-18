FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libsqlite3-dev libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libonig-dev libxml2-dev \
    libicu-dev \
    zip unzip git curl nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo pdo_sqlite zip gd mbstring xml bcmath opcache pcntl intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]