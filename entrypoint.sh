#!/bin/bash

# Run Composer install if vendor does not exist
if [ ! -d "/var/www/html/vendor" ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
fi

# Run Apache in the foreground
apache2-foreground
