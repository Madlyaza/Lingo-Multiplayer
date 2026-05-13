#!/bin/bash
set -e

# Install/update composer dependencies if vendor is missing (e.g. fresh volume mount)
if [ ! -d "vendor" ]; then
    echo "vendor/ not found, running composer install..."
    composer install --no-interaction --optimize-autoloader
fi

# Clear config cache so env overrides from docker-compose are picked up
php artisan config:clear

# Run migrations automatically on startup
php artisan migrate --force

# Start the main process
exec "$@"
