#!/usr/bin/env sh
set -e

# Update Packages
apt update

# Install deps
apt install -y php-cli \
    php-apcu \
    php-ctype \
    php-curl \
    php-fpm \
    php-pgsql \
    php-phar \
    php-redis

# Install composer into /app/bin
EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --install-dir=/app/bin --filename=composer
rm composer-setup.php
chmod +x /app/bin/composer
runuser -l ubuntu -c '/app/bin/composer install --prefer-source --no-dev --optimize-autoloader --ignore-platform-reqs'
