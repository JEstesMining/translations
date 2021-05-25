#!/usr/bin/env sh
set -e

export APP_ENV=prod
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/tmp/composer
#export COMPOSER_VENDOR=/app/vendor
DEPLOYMENT_ARCHIVE=/opt/codedeploy-agent/deployment-root/${DEPLOYMENT_GROUP_ID}/${DEPLOYMENT_ID}/deployment-archive

composer install --working-dir=${DEPLOYMENT_ARCHIVE} --prefer-dist --no-dev --optimize-autoloader
#yarn install
