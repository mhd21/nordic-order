#!/bin/bash

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
until nc -z db 3306; do
  sleep 1
done
echo "MySQL is up and running."

echo "remove .env"
if [ -f .env ] ; then
    rm .env
fi
touch .env
printf "APP_ENV=$APP_ENV\nAPP_DEBUG=$APP_DEBUG\nAPP_KEY=$APP_KEY\nDB_CONNECTION=$DB_CONNECTION\nDB_HOST=$DB_HOST\nDB_PORT=$DB_PORT\nDB_DATABASE=$DB_DATABASE\nDB_USERNAME=$DB_USERNAME\nDB_PASSWORD=$DB_PASSWORD\nQUEUE_CONNECTION=database" >> .env

# Run Laravel commands
echo "Generating application key..."
php artisan key:generate

echo "Running migrations..."
php artisan migrate --seed


# Start Laravel server
echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000
