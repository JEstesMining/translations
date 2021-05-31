#!/usr/bin/env sh
set -e

#mkdir -vp /app

export APP_ENV=prod
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_HOME=/tmp/composer
export COMPOSER_VENDOR=/app/vendor
#DEPLOYMENT_ARCHIVE=/app
DEPLOYMENT_ARCHIVE=/opt/codedeploy-agent/deployment-root/${DEPLOYMENT_GROUP_ID}/${DEPLOYMENT_ID}/deployment-archive

composer install --working-dir=${DEPLOYMENT_ARCHIVE} --prefer-dist --no-dev --optimize-autoloader --no-scripts
chown -R www-data:www-data ${DEPLOYMENT_ARCHIVE}/var
chown -R www-data:www-data ${DEPLOYMENT_ARCHIVE}/vendor


#DEPLOYMENT_ARCHIVE=/opt/codedeploy-agent/deployment-root/${DEPLOYMENT_GROUP_ID}/${DEPLOYMENT_ID}/deployment-archive
yarn --cwd ${DEPLOYMENT_ARCHIVE} --cache-folder /tmp/yarn --non-interactive --prod install
yarn --cwd ${DEPLOYMENT_ARCHIVE} --cache-folder /tmp/yarn --non-interactive --prod run build
chown -R www-data:www-data ${DEPLOYMENT_ARCHIVE}/node_modules
