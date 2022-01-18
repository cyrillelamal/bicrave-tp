# bicrave-tp

> E-commerce engine

## General considerations

This application is a draft for a real world e-commerce application.

The author doesn't pretend that his application has a very good architecture, structure, etc. Far from there: this is a
training project!

The application is based on PHP8 and Symfony. The proposed database is PostgreSQL, but there is no vendor-specific
queries nor raw queries.

Bulma and jQuery are used as the front-end technologies.

Redis is used as the cache store.

## Some implemented features

### Cart

The cart is available for all authorized users (including unauthenticated ones). If you sign up or log in with a
non-empty cart, the session cart items will be transferred to your account cart.

### Category tree

You can build category trees with (almost) infinite depth.

### Dashboard for employees

Depending on their roles, users can manipulate goods, categories, etc. The CMS is based on EasyAdmin.

### Image resizing

The product images are resized to not abuse the network.

### Payment using Stripe

Users can pay for an order using Stripe.

## How to install

Use `make`.

* Run `dev` target to start a local version of the application.

```shell
make dev
```

By default, the application is available at [http://bicrave-tp.com](http://bicrave-tp.com), so refine yours hosts
settings or configure your `bicrave-tp.conf` nginx configuration (placed
in [docker/nginx/bicrave-tp.conf](docker/nginx/bicrave-tp.conf)) before running the command.

Optionally, load fixtures using the following command or play around with the admin dashboard.

```shell
# Load fixtures
docker-compose run php-fpm sh -c "php bin/console doctrine:fixtures:load --no-interaction"

# Or create an admin user
docker-compose run php-fpm sh -c "php bin/console app:create-content-manager example@your.mail"
```

* You also can run `test` target to run tests and then tear down the application.

```shell
make test
```

### Add a content manager

Run the Symfony command `app:create-content-manager` and follow the instructions.

### Test

Tests are run on CircleCI. To run them locally, run the `test` target:

```shell
make test
```
