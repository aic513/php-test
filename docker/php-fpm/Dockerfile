FROM php:8.1-fpm

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./php.ini /usr/local/etc/php/conf.d/php.ini

RUN apt-get update --fix-missing && apt-get install -y --no-install-recommends\
        git curl wget grep libmemcached-dev zlib1g-dev vim nano libcurl4-gnutls-dev libgnutls28-dev libpq-dev libpng-dev libzip-dev libonig-dev autoconf g++ make zip unzip  procps curl exif mariadb-client librabbitmq-dev libssh-dev\
        && pecl install amqp \
        && docker-php-ext-enable amqp \
        && docker-php-ext-install sockets \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql\
        && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/app

VOLUME /var/www/html

RUN usermod -u 1000 www-data

EXPOSE 9000

CMD ["php-fpm"]