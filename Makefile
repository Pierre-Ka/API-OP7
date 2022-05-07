database:
	php bin/console doctrine:database:drop --if-exists --force --env=dev
	php bin/console doctrine:database:create --env=dev
	php bin/console doctrine:schema:update --force --env=dev

fixtures:
	php bin/console doctrine:fixtures:load -n --env=dev

prepare:
	make database env=dev
	make fixtures env=dev