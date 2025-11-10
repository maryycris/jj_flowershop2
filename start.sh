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
mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache storage/app/public/catalog_products 2>&1
chmod -R 775 storage bootstrap/cache 2>&1 || echo "Warning: chmod failed (may not be critical)" >&2

# Ensure catalog_products directory exists and is writable
echo "Ensuring catalog_products directory exists..." >&2
mkdir -p storage/app/public/catalog_products 2>&1
chmod -R 775 storage/app/public/catalog_products 2>&1 || echo "Warning: chmod catalog_products failed (may not be critical)" >&2

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

# Create storage symlink at root public directory (not backend/public)
echo "Checking storage symlink..." >&2
# We're in backend/ directory, so root public is ../public
ROOT_PUBLIC="../public"
ROOT_STORAGE="../backend/storage/app/public"

if [ ! -L "$ROOT_PUBLIC/storage" ]; then
    echo "Creating storage symlink at root public directory..." >&2
    # Remove existing symlink if it's broken
    rm -f "$ROOT_PUBLIC/storage" 2>&1 || true
    # Create symlink from root public/storage to backend/storage/app/public
    ln -sfn "$ROOT_STORAGE" "$ROOT_PUBLIC/storage" 2>&1 || {
        echo "Manual symlink failed, trying php artisan storage:link..." >&2
        # Try Laravel's storage:link command (creates at backend/public/storage)
        php artisan storage:link 2>&1 || echo "Storage link command also failed" >&2
        # If Laravel created it at backend/public/storage, copy/create at root too
        if [ -L "public/storage" ] && [ ! -L "$ROOT_PUBLIC/storage" ]; then
            echo "Laravel created symlink at backend/public/storage, creating at root..." >&2
            ln -sfn "$ROOT_STORAGE" "$ROOT_PUBLIC/storage" 2>&1 || echo "Root symlink creation failed" >&2
        fi
    }
    # Verify symlink was created
    if [ -L "$ROOT_PUBLIC/storage" ]; then
        echo "Storage symlink created successfully at $ROOT_PUBLIC/storage" >&2
        ls -la "$ROOT_PUBLIC/storage" >&2 || echo "Warning: Could not list symlink" >&2
        # Test if symlink works
        if [ -e "$ROOT_PUBLIC/storage" ]; then
            echo "Storage symlink is working" >&2
            ls -la "$ROOT_PUBLIC/storage/catalog_products" 2>&1 | head -5 >&2 || echo "catalog_products directory not found in symlink" >&2
        else
            echo "WARNING: Storage symlink exists but is broken!" >&2
        fi
    else
        echo "WARNING: Storage symlink was not created at $ROOT_PUBLIC/storage!" >&2
    fi
else
    echo "Storage symlink already exists at $ROOT_PUBLIC/storage" >&2
    # Verify it's not broken
    if [ ! -e "$ROOT_PUBLIC/storage" ]; then
        echo "WARNING: Storage symlink exists but is broken, recreating..." >&2
        rm -f "$ROOT_PUBLIC/storage" 2>&1 || true
        ln -sfn "$ROOT_STORAGE" "$ROOT_PUBLIC/storage" 2>&1 || echo "Storage link recreation failed" >&2
    else
        echo "Storage symlink is working correctly" >&2
    fi
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
    
    # Check if staff users exist, if not, run seeder
    echo "Checking if staff users exist..." >&2
    ADMIN_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo \App\Models\User::where('role', 'admin')->count();" 2>&1 | tail -1)
    CLERK_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo \App\Models\User::where('role', 'clerk')->count();" 2>&1 | tail -1)
    DRIVER_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo \App\Models\User::where('role', 'driver')->count();" 2>&1 | tail -1)
    
    if [ "$ADMIN_COUNT" = "0" ] || [ "$CLERK_COUNT" = "0" ] || [ "$DRIVER_COUNT" = "0" ] || [ -z "$ADMIN_COUNT" ]; then
        echo "Missing staff users. Running database seeder..." >&2
        php artisan db:seed --class=CreateAdminUserSeeder --force 2>&1 || {
            echo "WARNING: Seeder failed. You may need to create users manually." >&2
        }
        echo "Seeder completed. Users created:" >&2
        echo "  - Admin: username=admin, password=password" >&2
        echo "  - Clerk: username=clerk, password=password" >&2
        echo "  - Driver: username=driver, password=password" >&2
        echo "  - Customer: email=customer@example.com, password=password" >&2
    else
        echo "Staff users found - Admin: $ADMIN_COUNT, Clerk: $CLERK_COUNT, Driver: $DRIVER_COUNT" >&2
    fi
    
    # Check if inventory is populated, if not, run inventory seeder
    echo "Checking if inventory is populated..." >&2
    PRODUCT_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo \App\Models\Product::count();" 2>&1 | tail -1)
    
    if [ "$PRODUCT_COUNT" -lt "50" ]; then
        echo "Inventory has only $PRODUCT_COUNT items. Populating inventory..." >&2
        php artisan db:seed --class=InventorySeeder --force 2>&1 || {
            echo "WARNING: Inventory seeder failed." >&2
        }
        NEW_COUNT=$(php -r "require 'vendor/autoload.php'; \$app = require_once 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo \App\Models\Product::count();" 2>&1 | tail -1)
        echo "Inventory populated. Total products: $NEW_COUNT" >&2
    else
        echo "Inventory already populated with $PRODUCT_COUNT items." >&2
    fi
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
# Use router.php to serve static files directly, then route to Laravel
php -S 0.0.0.0:$PORT -t public public/router.php 2>&1

