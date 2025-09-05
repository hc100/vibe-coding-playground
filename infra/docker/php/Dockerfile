FROM php:8.4-fpm-bookworm

# Install system dependencies and PHP extensions commonly required by Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git curl unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath opcache zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Create a non-root user matching common host UID/GID for dev (optional)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data || true

# Copy entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

