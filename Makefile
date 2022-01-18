# We use the target name as the environment.
ENV = $@

# Run the application
dev: db deps assets
	docker-compose run php-fpm sh -c "php bin/console doctrine:database:create --if-not-exists --env=$(ENV)"
	@docker-compose run php-fpm sh -c "php bin/console doctrine:migrations:migrate --no-interaction --env=$(ENV)"
	@echo "The application is ready. Refine your 'hosts' file to be able to access it."

# Run tests and tear down the application
test: db deps assets
	@docker-compose down
	@docker-compose run php-fpm sh -c "php bin/console doctrine:database:drop --force --if-exists --env=$(ENV)"
	@docker-compose run php-fpm sh -c "php bin/console doctrine:database:create --env=$(ENV)"
	@docker-compose run php-fpm sh -c "php bin/console doctrine:migrations:migrate --no-interaction --env=$(ENV)"
	@docker-compose run php-fpm sh -c "php bin/console doctrine:fixtures:load --env=$(ENV) --no-interaction"
	@docker-compose run php-fpm bash -c "php ./bin/phpunit"
	@docker-compose down

# Run Docker
containers:
	@docker-compose up -d

# Install PHP dependencies
deps: containers
	@docker-compose run composer sh -c "composer install"
	# && composer dump-autoload --optimize

# Install NodeJS dependencies
assets: containers
	@docker-compose run node sh -c "yarn install && yarn run build"

# Wait for the database
db: containers
	@docker-compose run php-fpm sh -c "wait-for-it.sh database:5432"
