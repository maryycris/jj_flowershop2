# Use PHP 8.2 CLI (for php artisan serve)
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy dependency files first for better caching
RUN mkdir -p /var/www/html/backend /var/www/html/frontend
COPY backend/composer.json backend/composer.lock /var/www/html/backend/
COPY frontend/package.json frontend/package-lock.json /var/www/html/frontend/

# Install PHP dependencies
WORKDIR /var/www/html/backend
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install frontend dependencies (including dev dependencies for build)
WORKDIR /var/www/html/frontend
RUN npm ci

# Copy the rest of the application files
WORKDIR /var/www/html
COPY . /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod +x /var/www/html/start.sh

# Build frontend assets
WORKDIR /var/www/html/frontend
RUN npm run build

# Run composer scripts (post-install hooks)
WORKDIR /var/www/html/backend
RUN composer dump-autoload --optimize

# Go back to root directory
WORKDIR /var/www/html

# Expose port (Railway will set PORT env var)
EXPOSE 8080

# Use the Procfile command (which calls start.sh)
CMD ["bash", "start.sh"]

