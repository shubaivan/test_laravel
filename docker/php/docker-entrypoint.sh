#!/bin/bash
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
    set -- php-fpm "$@"
fi

# Run only once to initialize the app
if [ ! -f /var/laratest/initalized ]; then
    echo "Initializing application"
    touch /var/laratest/initalized

    # Fix permissions on storage and cache
    mkdir -p storage bootstrap/cache
    setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX storage bootstrap/cache
    setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX storage bootstrap/cache

    composer install --prefer-dist --no-progress --no-suggest --no-interaction
fi

exec docker-php-entrypoint "$@"
