init: docker-down docker-pull docker-build docker-up

create_network:
	@if [ -z "$$(docker network ls --filter name=web-php-symfony-server -q)" ]; then \
		docker network create web-php-symfony-server; \
	else \
		echo "Docker network web-php-symfony-server already exists, skipping creation."; \
	fi

docker-down:
	docker compose --env-file ./project/.env.local down --remove-orphans

docker-pull:
	docker compose --env-file ./project/.env.local pull

docker-build:
	docker compose --env-file ./project/.env.local build --pull

docker-up:
	docker compose --env-file ./project/.env.local up -d

php-cli:
	docker compose --env-file ./project/.env.local run --rm php-cli bash

dev-update:
	docker compose --env-file ./project/.env.local exec php-cli bash
	composer install
	bin/console d:m:m --no-inreraction
