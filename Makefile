PHP56 = docker run -it -v $(shell pwd):/data -w /data $(shell cat docker/php56.image)
PHP56MIN = docker run -it -v $(shell pwd):/data -w /data $(shell cat docker/php56-min.image)


.PHONY : all watch test test-local dist clean


all : build/phast.php

watch :
	git ls-files src | entr make

test : all docker/php56.image docker/php56-min.image
	$(PHP56) vendor/bin/phpunit
	$(PHP56MIN) vendor/bin/phpunit

test-local : all
	vendor/bin/phpunit

dist : all
	bin/package

clean :
	rm -rf build


build/phast.php : vendor/autoload.php $(shell git ls-files src)
	bin/compile $(dir $@)

vendor/autoload.php : composer.json composer.lock
	composer install

docker/%.image : docker/% docker/entrypoint
	docker build -q -f $< docker > $@~
	mv $@~ $@
