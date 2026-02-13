#!/bin/bash
set -e

cd /var/www/html

if [ ! -d vendor ]; then
    composer install
fi

php-fpm
