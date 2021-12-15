#-----------------------------------------------------------
# for local
db-seed:
	bin/console doctrine:schema:drop --full-database --force
	bin/console doctrine:schema:update --force
	bin/console doctrine:fixtures:load -n

#-----------------------------------------------------------
# for local
stop-local-services:
	sudo systemctl stop apache2
	sudo systemctl stop mysql

start-local-services:
	sudo systemctl start apache2
	sudo systemctl start mysql

#-----------------------------------------------------------
# docker
init: docker-pull docker-build docker-up composer-install yarn-install run-dev
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
	docker-compose exec php composer install
composer-update:
	docker-compose exec php composer update

#-----------------------------------------------------------
# yarn
yarn-install:
	docker-compose exec php yarn install
run-dev:
	docker-compose exec php yarn encore dev
run-watch:
	docker-compose exec php yarn encore dev --watch

seed:
	docker-compose exec php bin/console doctrine:schema:drop --full-database --force
	docker-compose exec php bin/console doctrine:schema:update --force
	docker-compose exec php bin/console doctrine:fixtures:load -n
