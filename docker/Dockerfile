FROM php:8.5-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl

COPY ./docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Composer
RUN curl -s https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer about

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
