# -*- mode: conf -*-
# vi: set ft=conf:

release: ./bin/release

web: vendor/bin/heroku-php-nginx -F php-fpm.conf -C nginx.conf public/

#command_bus: bin/console messenger:consume --env=prod -vvv -n --limit=10000 --time-limit=3600 --memory-limit=512M commands_async
#event_bus:   bin/console messenger:consume --env=prod -vvv -n --limit=10000 --time-limit=3600 --memory-limit=512M events_async
