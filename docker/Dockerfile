FROM php:8.3-apache

# unzip is needed by Composer to extract packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_HOME=/tmp/composer

# Run Apache with the host user's UID so files created in the mounted project
# (vendor/, compiled templates in var/) stay owned by the host user.
ARG UID=1000
RUN usermod -u ${UID} www-data && groupmod -g ${UID} www-data

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
