FROM php:8.2-fpm-alpine

# Download the magic installer
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install extensions (Added 'intl' and 'gmp')
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql mbstring gd zip bcmath intl gmp @composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload
