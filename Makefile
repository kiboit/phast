RUN = docker run -it -v $(shell pwd):/data -w /data $(shell cat .docker-image-id)


all : vendor/autoload.php


.PHONY : test update

test : all
	$(RUN) ./vendor/phpunit/phpunit/phpunit

update : all
	$(RUN) composer update


vendor/autoload.php : docker composer.json composer.lock
	$(RUN) composer install

docker : .docker-image-id
.docker-image-id : Dockerfile docker/entrypoint
	docker build -q . > $@~
	mv $@~ $@
