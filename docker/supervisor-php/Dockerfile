FROM php:8.1-cli

ENV COMPOSER_ALLOW_SUPERUSER 1

COPY ./supervisor.d/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN apt-get update && apt-get install -y libmcrypt-dev mariadb-client supervisor procps curl librabbitmq-dev libssh-dev\
            && pecl install amqp \
            && docker-php-ext-enable amqp \
            && docker-php-ext-install sockets \
            && docker-php-ext-install -j$(nproc) iconv mysqli pdo_mysql\
            && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html/app

RUN usermod -u 1000 www-data

EXPOSE 9001

CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/conf.d/supervisord.conf"]