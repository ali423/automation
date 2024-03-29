#!/bin/bash
if [ ! -f "vendor/autoload.php" ]; then
    composer install --no-progress --no-interaction
    php artisan migrate:fresh --seed
    if [ -f  ".env"]; then
    php artisan key:generate
    fi
fi

if [ ! -f  ".env"]; then
    echo "creating env file for env $APP_ENV"
    cp .env.example .env
fi
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan key:generate

php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
exec docker-php-entrypoint
