#!/usr/bin/env sh
set -e

export APP_ENV=prod
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/tmp/composer
export COMPOSER_VENDOR=/app/vendor
DEPLOYMENT_ARCHIVE=/app

composer install --working-dir=${DEPLOYMENT_ARCHIVE} --prefer-dist --no-dev --optimize-autoloader
chown -R www-data:www-data /app/var
chown -R www-data:www-data /app/vendor


#DEPLOYMENT_ARCHIVE=/opt/codedeploy-agent/deployment-root/${DEPLOYMENT_GROUP_ID}/${DEPLOYMENT_ID}/deployment-archive
yarn --cwd /app --cache-folder /tmp/yarn --non-interactive --prod install
#yarn --cwd /app --cache-folder /tmp/yarn --non-interactive --prod run build
chown -R www-data:www-data /app/node_modules
