#!/bin/bash
set -e

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! nc -z automation-db 3306; do
    sleep 1
done
echo "MySQL is ready!"

# Check if this is first run
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "First time setup detected..."

    # Install dependencies
    composer install --no-progress --no-interaction

    # Generate application key
    php artisan key:generate

    # Run migrations
    php artisan migrate:fresh --seed
fi

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Start the application
php artisan serve --port=$PORT --host=0.0.0.0 --env=.env
