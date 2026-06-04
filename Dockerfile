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
# 9. Configure OPcache — bisa di-override via environment var
#    PHP_OPCACHE_VALIDATE_TIMESTAMPS=1 → file changes langsung aktif (dev)
#    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 → cache agresif (prod)
# ============================================================
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.validate_timestamps=${PHP_OPCACHE_VALIDATE_TIMESTAMPS:-0}'; \
    echo 'opcache.revalidate_freq=${PHP_OPCACHE_REVALIDATE_FREQ:-2}'; \
    echo 'opcache.save_comments=1'; \
} > /usr/local/etc/php/conf.d/opcache.ini

# ============================================================
# 10. Create storage symlink (public/storage -> storage/app/public)
# ============================================================
RUN php artisan storage:link

# ============================================================
# 11. Create entrypoint script for runtime initialization
#     (migrations run at container start, not build time)
# ============================================================
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Apply OPcache settings from environment variables\n\
sed -i "s/opcache.validate_timestamps=.*/opcache.validate_timestamps=${PHP_OPCACHE_VALIDATE_TIMESTAMPS:-0}/" /usr/local/etc/php/conf.d/opcache.ini\n\
sed -i "s/opcache.revalidate_freq=.*/opcache.revalidate_freq=${PHP_OPCACHE_REVALIDATE_FREQ:-2}/" /usr/local/etc/php/conf.d/opcache.ini\n\
\n\
# Refresh composer autoload (agar class baru langsung ke-detect)\n\
composer dump-autoload --optimize --no-interaction 2>/dev/null || true\n\
\n\
# Run migrations\n\
php artisan migrate --force\n\
\n\
# Clear all caches\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan cache:clear\n\
\n\
# Start Apache in foreground\n\
exec apache2-foreground' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/entrypoint.sh"]
