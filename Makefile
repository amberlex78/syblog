#-----------------------------------------------------------
# docker
up: docker-up
down: docker-down
restart: down up
init: docker-pull docker-build docker-up

docker-up:
	docker-compose up -d
docker-down:
	docker-compose down --remove-orphans
docker-pull:
	docker-compose pull
docker-build:
	docker-compose build
