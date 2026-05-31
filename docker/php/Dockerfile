FROM php:8.4-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        zlib1g-dev \
    && docker-php-ext-install \
        bcmath \
        intl \
        opcache \
        pcntl \
        pdo_pgsql \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/autoestoque.ini

RUN groupmod -g ${GROUP_ID} www-data \
    && usermod -u ${USER_ID} -g ${GROUP_ID} www-data

USER www-data

CMD ["php-fpm"]
