FROM php:8.3-fpm

# Arguments
ARG user=www-data
ARG uid=1000

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
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Fix www-data UID/GID to match the host user UID (passed via build arg).
# php:8.3-fpm ships www-data with UID 33; useradd would silently fail on a
# duplicate name, leaving php-fpm running as UID 33 which cannot write to
# host-mounted volumes owned by the developer's account.
RUN usermod -u $uid www-data && groupmod -g $uid www-data
RUN mkdir -p /home/www-data/.composer && \
    chown -R www-data:www-data /home/www-data

# Set working directory
WORKDIR /var/www/backend

# Copy existing application directory contents
COPY . /var/www/backend

# Copy existing application directory permissions
COPY --chown=$user:$user . /var/www/backend

# Change current user to www-data
USER $user

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
