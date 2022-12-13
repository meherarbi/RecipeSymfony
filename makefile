tests:
	vendor/bin/behat
	php bin/phpunit --testdox

fixtures-test:
	php bin/console doctrine:fixtures:load -n --env=test

fixtures:
	php bin/console doctrine:fixtures:load -n --env=dev

database-test:
	php bin/console doctrine:database:drop --if-exists --force --env=test
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:schema:update --force --env=test

database:
	php bin/console doctrine:database:drop  --force --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update --force --env=dev

prepare-test:
	make database-test
	make fixtures-test

prepare-dev:
	make database-dev
	make fixtures-dev

prepare-build:
	make database-test
	make fixtures-test
	npm run dev