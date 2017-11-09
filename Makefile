RUN = docker run -it -v $(shell pwd):/data -w /data $(shell cat .docker-image-id)


all : vendor/autoload.php


.PHONY : test update

test : all
	$(RUN) ./vendor/phpunit/phpunit/phpunit

test-local : all
	vendor/phpunit/phpunit/phpunit

update : all
	vendor/composer.phar update


vendor/autoload.php : vendor/composer.phar composer.json composer.lock
	vendor/composer.phar install

vendor/composer.phar :
	mkdir -p vendor
	wget -O $@~ https://github.com/composer/composer/releases/download/1.5.2/composer.phar
	chmod +x $@~
	mv $@~ $@

docker : .docker-image-id
.docker-image-id : Dockerfile docker/entrypoint
	docker build -q . > $@~
	mv $@~ $@
