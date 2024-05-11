validate:
	composer validate
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin
autoload:
	composer dump-autoload
test:
	composer exec --verbose phpunit tests