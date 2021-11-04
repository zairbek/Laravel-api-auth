FROM php:8-fpm

###########################################################################
# Base:
###########################################################################
RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    pecl channel-update pecl.php.net; \
    apt-get install -y --no-install-recommends \
            apt-utils \
            curl \
            libmemcached-dev \
            libz-dev \
            libpq-dev \
            libssl-dev \
            libmcrypt-dev \
            libonig-dev \
            vim


###########################################################################
# ZIP module
###########################################################################

RUN apt-get install -yqq \
        zip \
        unzip \
        libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

###########################################################################
# Composer:
###########################################################################

ARG INSTALL_COMPOSER=true

RUN if [ ${INSTALL_COMPOSER} = true ]; then \
    curl -sS https://getcomposer.org/installer | php -- \
            --install-dir=/usr/local/bin \
            --filename=composer \
       && chmod +x /usr/local/bin/composer \
;fi


WORKDIR /var/www