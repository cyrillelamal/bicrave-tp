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

1. Copy the `.env` to `.env.local` or `.env.prod` and configure the environment variables. Notice that without valid
   variables `STRIPE_PUBLIC` and `STRIPE_PRIVATE` you will not be able to play around with Stripe.
2. Use Docker Compose to up containers: `docker-compose up -d`.
3. Run database migrations: `docker-compose run php-fpm sh -c "php bin/console doctrine:migrations:migrate"`
4. The application is hosted at `bicrave-tp.com`. To access it you have to update your `hosts` or deploy it somewhere
   else.
5. Profit!

Optionally, load fixtures: `docker-compose run php-fpm sh -c "php bin/console doctrine:fixtures:load --no-interaction`
or play around with the admin dashboard.

### Add a content manager

Run the Symfony command `app:create-content-manager` and follow the instructions.

### Test

Tests are run on CircleCI. To run them locally, repeat `build-dev` workflow on your machine (see `.circleci/config.yml`)
.
