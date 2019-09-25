PHP56 = docker run -it -v $(shell pwd):/data -w /data $(shell cat docker/php56.image)
PHP56MIN = docker run -it -v $(shell pwd):/data -w /data $(shell cat docker/php56-min.image)


.PHONY : all test test-local dist build clean


all : vendor/autoload.php

test : all docker/php56.image docker/php56-min.image
	$(PHP56) vendor/bin/phpunit
	$(PHP56MIN) vendor/bin/phpunit

test-local : all
	vendor/bin/phpunit

dist : all
	bin/package

build : build/phast.php build/cacert.pem

build/phast.php : vendor/autoload.php
	mkdir -p $(dir $@)
	bin/compile > $@

build/cacert.pem : src/HTTP/cacert.pem
	cp $< $@

clean :
	rm -rf build


vendor/autoload.php : composer.json composer.lock
	composer install

docker/%.image : docker/% docker/entrypoint
	docker build -q -f $< docker > $@~
	mv $@~ $@
