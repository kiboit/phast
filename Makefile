all : vendor/autoload.php

test : all
	docker-compose run -w /data php ./vendor/phpunit/phpunit/phpunit

update : all
	docker-compose run -w /data php composer update

vendor/autoload.php : composer.json composer.lock
	docker-compose run -w /data php composer install
