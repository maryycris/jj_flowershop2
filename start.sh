#!/bin/bash

# Change to backend directory where Laravel app is located
cd backend || {
    echo "ERROR: backend directory not found!" >&2
    exit 1
}

# Output immediately to ensure logs are captured
echo "=== Starting Laravel Application ===" >&2
echo "PORT: $PORT" >&2
echo "APP_KEY is set: $([ -n "$APP_KEY" ] && echo 'YES' || echo 'NO')" >&2
echo "APP_ENV: ${APP_ENV:-not set}" >&2
echo "DB_CONNECTION: ${DB_CONNECTION:-not set}" >&2
echo "DB_HOST: ${DB_HOST:-not set}" >&2
echo "DB_PORT: ${DB_PORT:-not set}" >&2

# Check if PORT is set
if [ -z "$PORT" ]; then
    echo "ERROR: PORT environment variable is not set!" >&2
    exit 1
fi

# Ensure storage directories exist and are writable
echo "Checking storage directories..." >&2
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache 2>&1
chmod -R 775 storage bootstrap/cache 2>&1 || echo "Warning: chmod failed (may not be critical)" >&2

# Clear config cache first (this doesn't require database)
echo "Clearing config cache..." >&2
php artisan config:clear 2>&1 || echo "Config clear failed (non-critical)" >&2

# Only clear other caches if database is configured (to avoid SQLite errors)
if [ -n "$DB_CONNECTION" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Clearing application caches..." >&2
    php artisan cache:clear 2>&1 || echo "Cache clear failed (non-critical)" >&2
fi

# Clear route and view caches (these don't require database)
php artisan route:clear 2>&1 || echo "Route clear failed (non-critical)" >&2
php artisan view:clear 2>&1 || echo "View clear failed (non-critical)" >&2

# List all routes for debugging (only in production to help diagnose)
if [ "$APP_ENV" = "production" ]; then
    echo "Listing available dashboard routes..." >&2
    php artisan route:list --name=dashboard 2>&1 | head -20 >&2 || echo "Could not list routes" >&2
fi

# Create storage symlink if it doesn't exist
if [ ! -L "../public/storage" ]; then
    echo "Creating storage symlink..." >&2
    php artisan storage:link 2>&1 || echo "Storage link failed (non-critical)" >&2
fi

# Run migrations if database is configured
if [ -n "$DB_CONNECTION" ] && [ "$DB_CONNECTION" != "sqlite" ]; then
    echo "Running database migrations..." >&2
    
    # Ensure sessions table exists (required for database session driver)
    echo "Checking sessions table..." >&2
    php artisan migrate --path=database/migrations/2024_06_14_000000_create_sessions_table.php --force 2>&1 || echo "Sessions table migration skipped (may already exist)" >&2
    
    # Try to check migration status (this will fail if database is empty or connection fails)
    MIGRATION_STATUS=$(php artisan migrate:status 2>&1)
    MIGRATION_EXIT_CODE=$?
    
    # Check if error is due to missing migrations table (database is empty)
    if [ $MIGRATION_EXIT_CODE -ne 0 ]; then
        if echo "$MIGRATION_STATUS" | grep -qE "Base table or view not found.*migrations|migrations.*doesn't exist|SQLSTATE\[42S02\]"; then
            echo "Migrations table not found. Database appears empty. Running fresh migrations with seeding..." >&2
            php artisan migrate:fresh --force --seed 2>&1 || {
                echo "ERROR: Failed to run fresh migrations! Check database connection and credentials." >&2
                echo "Continuing anyway - server will start but may show database errors." >&2
            }
        else
            echo "WARNING: Could not check migration status. Error: $MIGRATION_STATUS" >&2
            echo "Attempting to run migrations anyway..." >&2
            php artisan migrate --force 2>&1 || {
                echo "WARNING: Normal migration failed. Attempting fresh migration..." >&2
                php artisan migrate:fresh --force --seed 2>&1 || {
                    echo "ERROR: All migration attempts failed! Check database connection." >&2
                    echo "Continuing anyway - server will start but may show database errors." >&2
                }
            }
        fi
    else
        # Migration status succeeded, check if there are pending migrations
        if echo "$MIGRATION_STATUS" | grep -qE "Pending|No migrations found"; then
            echo "Running pending migrations..." >&2
            php artisan migrate --force 2>&1 || {
                echo "WARNING: Migration failed, but continuing..." >&2
            }
        else
            echo "All migrations are up to date." >&2
        fi
    fi
    echo "Migration process completed." >&2
fi

# If APP_KEY is not set, try to generate one (only if .env exists)
if [ -z "$APP_KEY" ]; then
    if [ -f ".env" ]; then
        echo "WARNING: APP_KEY not set, generating new key..." >&2
        php artisan key:generate --force 2>&1 || {
            echo "ERROR: Failed to generate APP_KEY!" >&2
            exit 1
        }
        echo "APP_KEY generated successfully" >&2
    else
        echo "ERROR: APP_KEY is not set and .env file does not exist!" >&2
        echo "Please set APP_KEY as an environment variable in Railway." >&2
        echo "You can generate one locally with: php artisan key:generate --show" >&2
        exit 1
    fi
fi

# Set APP_URL if not set (Railway provides this via RAILWAY_PUBLIC_DOMAIN)
if [ -z "$APP_URL" ] && [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    export APP_URL="https://$RAILWAY_PUBLIC_DOMAIN"
    echo "Set APP_URL to: $APP_URL" >&2
fi

# Set session configuration for HTTPS (Railway uses HTTPS)
if [ -z "$SESSION_SECURE_COOKIE" ]; then
    export SESSION_SECURE_COOKIE="true"
    echo "Set SESSION_SECURE_COOKIE to true for HTTPS" >&2
fi

# Verify Vite manifest exists
if [ -f "../public/build/manifest.json" ]; then
    echo "Vite manifest found at public/build/manifest.json" >&2
else
    echo "WARNING: Vite manifest not found at public/build/manifest.json" >&2
    echo "This may cause 500 errors on pages using Vite assets" >&2
    if [ -d "../public/build" ]; then
        echo "Contents of public/build:" >&2
        ls -la ../public/build/ >&2 || true
    fi
fi

# Start the server from root public directory
echo "Starting PHP server on 0.0.0.0:$PORT..." >&2
cd .. || exit 1
php -S 0.0.0.0:$PORT -t public public/index.php 2>&1

