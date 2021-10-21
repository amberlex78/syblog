#-----------------------------------------------------------
# docker (only DB)
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

