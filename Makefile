#-----------------------------------------------------------
# for local
stop-local-services:
	sudo systemctl stop apache2
	sudo systemctl stop mysql

start-local-services:
	sudo systemctl start apache2
	sudo systemctl start mysql

db-seed:
	bin/console doctrine:schema:drop --full-database --force
	bin/console doctrine:schema:update --force
	bin/console doctrine:fixtures:load -n

#-----------------------------------------------------------
# docker
init: docker-pull docker-build docker-up
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
# Setup
setup: composer-install yarn-install run-dev

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

#-----------------------------------------------------------
# doctrine
db-dul: db-drop db-update db-load

db-drop:
	docker-compose exec php bin/console doctrine:schema:drop --full-database --force
db-update:
	docker-compose exec php bin/console doctrine:schema:update --force
db-load:
	docker-compose exec php bin/console doctrine:fixtures:load -n
