FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by UserFrosting
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql \
        zip \
        intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm (required for UserFrosting asset compilation)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Configure PHP to suppress deprecation warnings for PHP 8.3 compatibility
RUN echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT" > /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/html

# Copy installation script
COPY install-guidewizard.sh /usr/local/bin/install-guidewizard.sh
RUN chmod +x /usr/local/bin/install-guidewizard.sh

# Create npm cache directory and set permissions
RUN mkdir -p /var/www/.npm && \
    chown -R www-data:www-data /var/www/html /var/www/.npm

USER www-data
