FROM php:8.3-apache

# unzip is needed by Composer to extract packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_HOME=/tmp/composer

# Standalone Dart Sass binary to compile scss/ into public/css/ (no Node.js needed).
# TARGETARCH is set by BuildKit: amd64 on most Linux hosts, arm64 on Apple Silicon.
ARG SASS_VERSION=1.105.1
ARG TARGETARCH
RUN case "${TARGETARCH:-amd64}" in arm64) arch=arm64 ;; *) arch=x64 ;; esac \
    && curl -fsSL "https://github.com/sass/dart-sass/releases/download/${SASS_VERSION}/dart-sass-${SASS_VERSION}-linux-${arch}.tar.gz" \
        | tar -xz -C /opt \
    && ln -s /opt/dart-sass/sass /usr/local/bin/sass

# Run Apache with the host user's UID so files created in the mounted project
# (vendor/, compiled templates in var/) stay owned by the host user.
ARG UID=1000
RUN usermod -u ${UID} www-data && groupmod -g ${UID} www-data

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
