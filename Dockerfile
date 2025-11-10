# Use PHP 8.2 CLI
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

# Copy all application files
COPY . /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod +x /var/www/html/start.sh

# Install PHP dependencies
WORKDIR /var/www/html/backend
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install frontend dependencies and build assets
WORKDIR /var/www/html/frontend
RUN npm install && npm run build

# Copy built assets from frontend/public/build to root public/build
WORKDIR /var/www/html
RUN mkdir -p public/build && \
    if [ -d "frontend/public/build" ]; then \
        cp -r frontend/public/build/* public/build/; \
    fi

# Optimize autoloader
WORKDIR /var/www/html/backend
RUN composer dump-autoload --optimize

# Go back to root directory
WORKDIR /var/www/html

# Expose port (Railway will set PORT env var)
EXPOSE 8080

# Use the Procfile command (which calls start.sh)
CMD ["bash", "start.sh"]
