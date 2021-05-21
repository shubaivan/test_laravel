ARG PHP_VERSION=7.4
ARG API_PATH="../"

FROM php:${PHP_VERSION}-fpm

RUN set -eux; \
    apt-get update; \
    apt-get install -y acl libpq-dev libzip-dev zlib1g-dev libicu-dev unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev iputils-ping nodejs npm; \
    docker-php-ext-configure intl; \
    docker-php-ext-configure zip; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install intl pgsql pdo_pgsql zip gd sockets; \
    pecl install apcu; \
    pecl install xdebug; \
    docker-php-ext-enable apcu xdebug;

RUN mkdir -p /var/www
#COPY ${API_PATH} /var/www

WORKDIR /var/www

RUN echo "$(curl -sS https://composer.github.io/installer.sig) -" > composer-setup.php.sig \
        && curl -sS https://getcomposer.org/installer | tee composer-setup.php | sha384sum -c composer-setup.php.sig \
        && php composer-setup.php && rm composer-setup.php* \
        && chmod +x composer.phar && mv composer.phar /usr/bin/composer


COPY php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
ENTRYPOINT ["bash", "/usr/local/bin/docker-entrypoint"]
CMD ["php-fpm"]
