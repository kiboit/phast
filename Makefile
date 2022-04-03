.DELETE_ON_ERROR :

PHP74 = docker run -v $(shell pwd):/data -w /data $(shell cat docker/php74.image)
JSMIN_SOURCES := $(wildcard vendor/kiboit/jsmin-php/src/JSMin/*.php)
JSMIN_TARGETS := $(patsubst vendor/kiboit/jsmin-php/src/%,src/%,$(JSMIN_SOURCES))

ifdef FILTER
PHPUNIT_ARGS := --filter="$(FILTER)"
endif

.PHONY : all
all : build/phast.php

.PHONY : watch
watch :
	git ls-files src test | entr -c -r $(MAKE) -s test74

.PHONY : test
test : test74

.PHONY : test74
test74 : all docker/php74.image
	$(PHP74) vendor/bin/phpunit $(PHPUNIT_ARGS)

.PHONY : test-local
test-local : all
	vendor/bin/phpunit

.PHONY : dist
dist : all
	bin/package

.PHONY : clean
clean :
	rm -rf build docker/php74.image

.PHONY : format
format : node_modules
	fd -g '*.js' -t f --ignore-file .format_exclude -X node_modules/.bin/prettier -w


build/phast.php : vendor/autoload.php node_modules $(JSMIN_TARGETS) $(shell git ls-files src)
	bin/compile $(dir $@)

src/JSMin/% : vendor/kiboit/jsmin-php/src/JSMin/% | vendor/autoload.php
	@mkdir -p $(dir $@)
	cat $< | perl -p -e 's~(\bnamespace\s+)(?=JSMin\b)~$$1Kibo\\Phast\\~g' > $@

vendor/autoload.php : composer.json composer.lock
	composer install
	touch vendor/autoload.php

docker/%.image : docker/% docker/entrypoint
	docker build --pull --iidfile $@~ -f $< docker
	mv $@~ $@

node_modules : yarn.lock
	yarn
	touch $@
