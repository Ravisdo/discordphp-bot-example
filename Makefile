.PHONY: docker artisan

default: install

WORKING_DIR=/var/www
PHP_SERVICE=discordphp-bot-example_php

up:
	docker-compose up -d

run: up
	docker-compose exec ${PHP_SERVICE} php start.php

install:
	docker-compose exec ${PHP_SERVICE} php composer.phar install

update: up
	docker-compose exec ${PHP_SERVICE} php -d memory_limit=-1 composer.phar update

autoload: up
	docker-compose exec ${PHP_SERVICE} php composer.phar dump-autoload

production_autoload: up
	docker-compose exec ${PHP_SERVICE} php composer.phar install --no-dev --classmap-authoritative

sniff: up
	docker-compose exec ${PHP_SERVICE} vendor/bin/phpcs -w -p -s --standard=vendor/flyeralarm/php-code-validator/ruleset.xml ./ --ignore=vendor/

migrate: up
	${WORKING_DIR}/vendor/bin/phinx --configuration=${WORKING_DIR}/config/phinx.config.php migrate
