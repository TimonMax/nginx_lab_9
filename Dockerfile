FROM php:8.5-fpm

RUN apt-get update && apt-get install -y zip unzip git curl libxml2-dev libonig-dev \
    && docker-php-ext-install sockets pdo pdo_mysql mbstring xml xmlwriter dom \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist

COPY ./www ./www

CMD ["php-fpm"]

