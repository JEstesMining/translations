#!/usr/bin/env sh
set -e

export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/tmp/composer
#export COMPOSER_VENDOR=/app/vendor

composer install
#yarn install
