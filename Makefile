PHP56 = docker run -it -v $(shell pwd):/data -w /data $(shell cat docker/php56.image)
PHP56MIN = docker run -it -v $(shell pwd):/data -w /data $(shell cat docker/php56-min.image)


.PHONY : all test test-local update docker dist


all : vendor/autoload.php

test : all docker/php56.image docker/php56-min.image
	$(PHP56) vendor/bin/phpunit
	$(PHP56MIN) vendor/bin/phpunit

test-local : all
	vendor/bin/phpunit

update : all
	vendor/composer.phar update

dist : all
	bin/package


vendor/autoload.php : vendor/composer.phar composer.json composer.lock
	vendor/composer.phar install

vendor/composer.phar :
	mkdir -p vendor
	wget -O $@~ https://github.com/composer/composer/releases/download/1.6.3/composer.phar
	chmod +x $@~
	mv $@~ $@

docker/%.image : docker/% docker/entrypoint
	docker build -q -f $< docker > $@~
	mv $@~ $@
