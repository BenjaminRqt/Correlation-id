#!make
install:
	@docker compose -f docker-compose.yml build
	@docker exec -it correlationId sh -c "composer install"

build:
	@docker compose -f docker-compose.yml build

up:
	@docker compose -f docker-compose.yml up --force-recreate -d

down:
	@docker compose -f docker-compose.yml down

bash:
	@docker exec -it correlationId bash

phpcsfixer:
	@docker exec -it correlationId sh -c "vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --allow-risky=yes -vv"

phpcsfixer-dry:
	@docker exec -it correlationId sh -c "vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --allow-risky=yes -vv"

phpstan:
	@docker exec -it correlationId sh -c "vendor/bin/phpstan analyse src tests"

phpcs:
	@docker exec -it correlationId sh -c "./vendor/bin/phpcs"

phpcbf:
	@docker exec -it correlationId sh -c "./vendor/bin/phpcbf"

fix: phpcsfixer phpcbf

test:
	@docker exec -it correlationId sh -c "php bin/phpunit"

qa: phpcsfixer-dry phpstan phpcs test

list:
	@grep -E '^[a-zA-Z_-]+:.*$$' Makefile | cut -d':' -f1
