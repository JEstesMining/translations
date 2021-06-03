# @see https://docs.docker.com/engine/reference/builder/
# @see https://docs.docker.com/develop/develop-images/multistage-build/
################################################################################
# PHP Deps
################################################################################
FROM composer:latest as vendor

WORKDIR /app
COPY composer.json composer.json
COPY composer.lock composer.lock
COPY symfony.lock symfony.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader
################################################################################
# Frontend
################################################################################
FROM node:alpine as frontend

WORKDIR /app

#RUN mkdir -vp assets

COPY assets assets
COPY package.json package.json
COPY yarn.lock yarn.lock
COPY webpack.config.js webpack.config.js

RUN yarn install && yarn run build
################################################################################
# Final
################################################################################
FROM nginx:alpine

ENV APP_ENV="prod"
ENV APP_DEBUG="0"

RUN apk add --update --no-cache \
    #parallel \
    #coreutils \
    #make \
    #curl \
    php7 \
    php7-sodium \
    php7-fpm \
    php7-apcu \
    php7-ctype \
    php7-curl \
    php7-dom \
    #php7-gd \
    php7-iconv \
    #php7-imagick \
    php7-json \
    php7-intl \
    php7-mcrypt \
    php7-fileinfo\
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    #php7-pdo \
    php7-pdo_pgsql \
    php7-xml \
    php7-zlib \
    #php7-phar \
    php7-tokenizer \
    php7-session \
    php7-simplexml \
    #php7-zip \
    #php7-xmlwriter \
    php7-redis

# Better Organize
COPY php.ini /etc/php7/php.ini
COPY php-fpm.conf /etc/php7/php-fpm.conf
COPY www.conf /etc/php7/php-fpm.d/www.conf
RUN echo "upstream php-upstream { server 0.0.0.0:9000; }" > /etc/nginx/conf.d/upstream.conf
COPY nginx.conf   /etc/nginx/nginx.conf
COPY default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

COPY . /app
RUN rm -rf vendor/ public/build/
COPY --from=vendor /app/vendor /app/vendor
COPY --from=frontend /app/public/build /app/public/build
RUN mkdir -vp var/cache/prod var/log

# This is prolly a bad idea but fuck it
#RUN php bin/console secrets:decrypt-to-local --force --env=prod

# Cleanup files we no longer require
RUN rm -rf assets/ \
      # Symfony shits itself without composer.json file, could touch the file?
      composer.lock \
      package.json \
      symfony.lock \
      yarn.lock \
      webpack.config.js \
      default.conf \
      nginx.conf \
      php-fpm.conf \
      php.ini \
      www.conf \
      entrypoint.sh

# fixes permissions
#ARG UID=nginx
#ARG GID=nginx
#RUN addgroup -g $GID -S nginx \
#    && adduser -S -D -H -u $UID -h /var/cache/nginx -s /sbin/nologin -G nginx -g nginx nginx \
RUN chown -R nobody:0 /var/cache/nginx \
    && chmod -R g+w /var/cache/nginx \
    && chown -R nobody:0 /etc/nginx \
    && chmod -R g+w /etc/nginx \
    && chown -R nobody:0 /app

COPY entrypoint.sh /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
#CMD ["nginx", "-g", "daemon off;"]
