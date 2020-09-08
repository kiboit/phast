PHP56 = docker run -v $(shell pwd):/data -w /data $(shell cat docker/php56.image)
PHP73 = docker run -v $(shell pwd):/data -w /data $(shell cat docker/php73.image)

ifdef FILTER
PHPUNIT_ARGS := --filter="$(FILTER)"
endif

.PHONY : all
all : build/phast.php

.PHONY : watch
watch :
	git ls-files src test | entr -c -r $(MAKE) -s test73

.PHONY : test
test : test73 test56

.PHONY : test56
test56 : all docker/php56.image
	$(PHP56) vendor/bin/phpunit $(PHPUNIT_ARGS)

.PHONY : test73
test73 : all docker/php73.image
	$(PHP73) vendor/bin/phpunit $(PHPUNIT_ARGS)

.PHONY : test-local
test-local : all
	vendor/bin/phpunit

.PHONY : dist
dist : all
	bin/package

.PHONY : clean
clean :
	rm -rf build


build/phast.php : vendor/autoload.php $(shell git ls-files src)
	bin/compile $(dir $@)

vendor/autoload.php : composer.json composer.lock
	composer install
	touch vendor/autoload.php

docker/%.image : docker/% docker/entrypoint
	docker build -q -f $< docker > $@~
	mv $@~ $@
