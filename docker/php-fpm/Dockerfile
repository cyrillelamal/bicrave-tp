FROM php:8.0-fpm

COPY wait-for-it.sh /usr/local/bin/wait-for-it.sh
RUN chmod +x /usr/local/bin/wait-for-it.sh \
    && sed -i 's/\r$//' /usr/local/bin/wait-for-it.sh \
    && apt-get update && apt-get install -y libpq-dev zlib1g-dev libicu-dev libpng-dev g++ \
    && docker-php-ext-install pdo pdo_pgsql intl gd exif \
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis

EXPOSE 9000

CMD php-fpm --nodaemonize
