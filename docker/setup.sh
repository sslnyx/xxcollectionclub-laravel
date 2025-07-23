#!/bin/bash

echo "Running setup script..."

# Print database environment variables for debugging
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "DB_DATABASE: $DB_DATABASE"
echo "DB_USERNAME: $DB_USERNAME"
echo "DB_PASSWORD: $DB_PASSWORD"

# Wait for the database to be ready
echo "Waiting for database to be ready..."
/usr/bin/php /var/www/html/artisan migrate:status > /dev/null 2>&1
while [ $? -ne 0 ]; do
    echo "Database not ready yet, retrying in 5 seconds..."
    sleep 5
    /usr/bin/php /var/www/html/artisan migrate:status > /dev/null 2>&1
done
echo "Database is ready!"

# Clear all Laravel caches
php artisan optimize:clear
php artisan config:clear

# Generate application key if not already set
php artisan key:generate --force

# Install Voyager and run its migrations
php artisan voyager:install --with-dummy

# List migrations to confirm they are published
ls -l database/migrations

# Run all pending database migrations
php artisan migrate --force

# Seed Voyager's default data
php artisan db:seed --class=VoyagerDatabaseSeeder

# Create storage link
php artisan storage:link

# Check migration status
php artisan migrate:status

echo "Setup script finished."

# Start supervisord
/usr/bin/supervisord -c /etc/supervisord.conf