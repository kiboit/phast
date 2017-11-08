all : vendor/autoload.php

test : all
	docker-compose run -w /data php ./vendor/phpunit/phpunit/phpunit

vendor/autoload.php : composer.json composer.lock
	composer install
