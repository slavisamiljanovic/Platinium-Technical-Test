FROM php:8.3-fpm AS php_fpm

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system dependencies and required libraries for PHP extensions.
RUN apt-get update && \
    apt-get -y install \
        acl \
        file \
        gettext \
        git \
        && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions.
RUN set -eux; \
    install-php-extensions \
        intl \
        zip \
        apcu \
        opcache \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    ;

# Install Composer.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Symfony CLI.
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

ARG APP_USER
ARG APP_USER_ID
RUN useradd -m -s /bin/bash -u ${APP_USER_ID} ${APP_USER} && \
    chown ${APP_USER}:${APP_USER} -R /var/www && \
    sed -i -e "s/www-data/${APP_USER}/g" /usr/local/etc/php-fpm.d/www.conf

COPY docker/app/entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

RUN ln -s /var/www/html/bin/console /usr/local/bin/ && \
    ln -s /var/www/html/bin/phpunit /usr/local/bin/

ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]


FROM php_fpm AS app_dev

ENV APP_ENV=dev

RUN set -eux; \
    install-php-extensions \
        pcov \
        xdebug \
    ;

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini && \
    rm $PHP_INI_DIR/php.ini-production

ARG APP_USER
USER ${APP_USER}


FROM php_fpm AS app_prod

ENV APP_ENV=prod

RUN mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini && \
    rm $PHP_INI_DIR/php.ini-development

ARG APP_USER
USER ${APP_USER}

# Prevent the re-installation of vendors at every changes in the source code.
COPY --chown=${APP_USER} composer.* symfony.* ./
RUN set -eux; \
    composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
    composer clear-cache

# Copy sources.
COPY --chown=${APP_USER} . .
RUN rm -Rf docker/

RUN set -eux; \
    mkdir -p var/cache var/log; \
    composer dump-autoload --classmap-authoritative --no-dev; \
    composer dump-env prod; \
    composer run-script --no-dev post-install-cmd; \
    sync



FROM nginx:1-alpine AS proxy

ARG APP_USER
ARG APP_USER_ID
RUN adduser --disabled-password -u ${APP_USER_ID} ${APP_USER} && \
    mkdir -p /var/www/html/public && \
    chown ${APP_USER}:${APP_USER} -R /var/www/html && \
    sed -i -e "s/^user .*/user ${APP_USER};/g" /etc/nginx/nginx.conf

VOLUME /var/www/html/public
WORKDIR /var/www/html/public

COPY --from=app_prod /var/www/html/public .

COPY docker/nginx/conf.d/backend.conf /etc/nginx/conf.d/default.conf

