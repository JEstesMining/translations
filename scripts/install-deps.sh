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
yarn install --cwd /app --cache-folder /tmp/yarn --non-interactive --prod
yarn run --cwd /app --cache-folder /tmp/yarn --non-interactive --prod production
chown -R www-data:www-data /app/node_modules
