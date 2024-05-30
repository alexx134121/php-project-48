validate:
	composer validate
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
phpstan:
	composer exec --verbose phpstan -- analyse --level 0 --ansi src
autoload:
	composer dump-autoload
test:
	XDEBUG_MODE=coverage ./vendor/phpunit/phpunit/phpunit tests -c phpunit.xml