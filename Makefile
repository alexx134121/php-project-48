validate:
	composer validate
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin tests
autoload:
	composer dump-autoload
test:
	XDEBUG_MODE=coverage ./vendor/phpunit/phpunit/phpunit tests -c phpunit.xml