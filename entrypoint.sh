#!/bin/sh
php-fpm7 -D || exit 1
nginx -g 'daemon on;' || exit 1

tail -F /app/var/log/*.log /app/var/log/prod.log
