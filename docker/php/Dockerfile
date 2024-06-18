# Use the official PHP-FPM image
FROM php:8.2-fpm

# Install necessary system packages and PHP extensions
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

RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configure Xdebug
RUN { \
    echo 'zend_extension=xdebug'; \
    echo 'xdebug.mode=debug'; \
    echo 'xdebug.start_with_request=yes'; \
    echo 'xdebug.client_host=host.docker.internal'; \
    echo 'xdebug.client_port=9003'; \
    echo 'xdebug.log=/tmp/xdebug.log'; \
    echo 'xdebug.idekey=PHPSTORM'; \
} > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Set the working directory
WORKDIR /var/www/html

# Copy the application code
COPY . /var/www/html

# Copy custom PHP configuration
COPY ./docker/php/conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Copy existing application directory permissions
RUN chown -R www-data:www-data /var/www/html

# Expose the port PHP-FPM will run on
EXPOSE 9000

# Command to run PHP-FPM
CMD ["php-fpm"]
