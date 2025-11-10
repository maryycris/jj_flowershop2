#!/bin/bash
set -e

echo "=== Custom Build Script for Railway ==="

# Build frontend assets (dependencies already installed in install phase)
echo "Building frontend assets..."
cd frontend
npm run build
cd ..

# Setup Laravel storage
echo "Setting up Laravel storage..."
cd backend
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/framework/testing storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache || true
cd ..

echo "=== Build completed successfully ==="

