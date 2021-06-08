DOCKER_PHP_CONTAINER=appdascryptofarm_php-fpm_1

help:
	@echo "Targets:"
	@echo
	@echo "    install                 Install deps"
	@echo "    up                      docker-compose up"
	@echo "    stop                    docker-compose stop"
	@echo "    worker                  Run background tasks"
	@echo "    db.migrate.dump-diff    Dump the SQL"
	@echo "    make.migration          Make database migration"
	@echo "    db.migrate              Run database migrations"

install:
	composer install
	yarn install

#.PHONY: assets
assets.dev:
	yarn run encore dev

up:
	docker-compose up -d --remove-orphans

stop:
	docker-compose stop

worker:
	docker exec -it ${DOCKER_PHP_CONTAINER} bin/console messenger:consume --env=dev -vvv -n --time-limit=300 --memory-limit=512M

# Dump the SQL for the migration
db.migrate.dump-diff:
	docker exec -it ${DOCKER_PHP_CONTAINER} bin/console doctrine:schema:update --env=dev -vvv -n --dump-sql

db.migrate:
	docker exec -it ${DOCKER_PHP_CONTAINER} bin/console doctrine:migrations:migrate --env=dev -vvv -n --all-or-nothing --query-time

make.migration:
	docker exec -it ${DOCKER_PHP_CONTAINER} bin/console make:migration --env=dev -vvv -n

app.invite.generate:
	docker exec -it ${DOCKER_PHP_CONTAINER} bin/console app:invite:generate --env=dev -vvv -n --count=1

build:
	aws ecr get-login-password --region us-east-1 | docker login --username AWS --password-stdin 759356101899.dkr.ecr.us-east-1.amazonaws.com
	docker build -t app .
	docker tag app:latest 759356101899.dkr.ecr.us-east-1.amazonaws.com/app:latest
	docker push 759356101899.dkr.ecr.us-east-1.amazonaws.com/app:latest

translations.add:
	git subtree add --prefix translations https://github.com/JEstesMining/translations.git master --squash

translations.pull:
	git subtree pull --prefix translations https://github.com/JEstesMining/translations.git master --squash
