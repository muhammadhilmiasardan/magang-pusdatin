# ============================================================
# Stage 1: Build frontend assets (Vite + Tailwind)
# ============================================================
FROM node:20-alpine AS node-builder

WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY . .
RUN npm run build

# ============================================================
# Stage 2: PHP 8.3 + Apache (Laravel)
# ============================================================
FROM php:8.3-apache

# ============================================================
# 1. Install system dependencies & PHP extensions
# ============================================================
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        intl \
        mbstring \
        bcmath \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ============================================================
# 2. Enable Apache mod_rewrite (Laravel needs URL rewriting)
# ============================================================
RUN a2enmod rewrite

# ============================================================
# 3. Configure Apache to serve from /var/www/html/public
#    Laravel's entry point is public/index.php
# ============================================================
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Allow .htaccess overrides (Laravel uses this for routing)
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf

# ============================================================
# 4. Install Composer
# ============================================================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ============================================================
# 5. Set working directory & copy source code
# ============================================================
WORKDIR /var/www/html
COPY . .

# ============================================================
# 6. Copy built frontend assets from Stage 1
# ============================================================
COPY --from=node-builder /app/public/build ./public/build

# ============================================================
# 7. Install PHP dependencies (production only)
# ============================================================
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# ============================================================
# 8. Set proper permissions for Laravel
# ============================================================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# ============================================================
# 9. Create storage symlink (public/storage -> storage/app/public)
# ============================================================
RUN php artisan storage:link

# ============================================================
# 10. Create entrypoint script for runtime initialization
#     (migrations run at container start, not build time)
# ============================================================
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Run migrations\n\
php artisan migrate --force\n\
\n\
# Clear & cache config for performance\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
\n\
# Start Apache in foreground\n\
exec apache2-foreground' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
