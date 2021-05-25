#!/usr/bin/env sh
set -e

export APP_ENV=prod
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/tmp/composer
#export COMPOSER_VENDOR=/app/vendor

composer install --working-dir=/app --prefer-dist --no-dev --optimize-autoloader
#yarn install
