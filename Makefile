all: install test

clean:
	rm -fr build/ vendor/ composer.lock

install:
	composer install

PHP70 := $(shell command -v php70x 2>/dev/null)
PHP71 := $(shell command -v php71x 2>/dev/null)
test:
ifdef PHP70
ifdef PHP71
	make -j2 -O test-70 test-71
else
	composer test
endif
else
	composer test
endif

test-70:
ifdef PHP70
	php70x vendor/phpunit/phpunit/phpunit --colors=always --no-coverage
endif

test-71:
ifdef PHP71
	php71x vendor/phpunit/phpunit/phpunit --colors=always --coverage-html build/logs/coverage
endif
