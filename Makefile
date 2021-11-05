db-seed:
	bin/console doctrine:schema:drop --force
	bin/console doctrine:schema:update --force
	bin/console doctrine:fixtures:load -n

#-----------------------------------------------------------
# docker (only DB)
init: docker-pull docker-build docker-up #composer-install yarn-install run-dev
up: docker-up
down: docker-down
restart: down up

docker-pull:
	docker-compose pull
docker-build:
	docker-compose build
docker-up:
	docker-compose up -d
docker-down:
	docker-compose down --remove-orphans

#-----------------------------------------------------------
# composer
composer-install:
	docker-compose run --rm php-cli composer install
composer-update:
	docker-compose run --rm php-cli composer update

#-----------------------------------------------------------
# yarn
yarn-install:
	docker-compose run --rm php-cli yarn install
run-dev:
	docker-compose run --rm php-cli yarn encore dev
run-watch:
	docker-compose run --rm php-cli yarn encore dev --watch

seed:
	docker-compose exec php-cli bin/console doctrine:schema:drop --full-database --force
	docker-compose exec php-cli bin/console doctrine:schema:update --force
	docker-compose exec php-cli bin/console doctrine:fixtures:load -n
