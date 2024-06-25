FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    gcc \
    make \
    autoconf \
    libc-dev \
    pkg-config \
    git \
    zip \
    zlib1g-dev \
    libzip-dev \
    vim

RUN docker-php-ext-install pdo pdo_mysql sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN { \
    echo 'zend_extension=xdebug'; \
    echo 'xdebug.mode=debug'; \
    echo 'xdebug.start_with_request=yes'; \
    echo 'xdebug.client_host=host.docker.internal'; \
    echo 'xdebug.client_port=9003'; \
    echo 'xdebug.log=/tmp/xdebug.log'; \
    echo 'xdebug.idekey=PHPSTORM'; \
} > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

COPY . /var/www/html

COPY ./docker/php/conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
