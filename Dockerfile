FROM php:8.3.7-alpine3.18

ARG COMPOSER_VERSION=2.7.4
ARG XDEBUG_VERSION=3.3.2

RUN apk add --no-cache \
    linux-headers \
    autoconf g++ make \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    postgresql-client \
    postgresql-dev \
    libzip-dev \
    imap-dev \
    dovecot \
    shadow \
    icu-data-full

RUN docker-php-ext-configure gd --with-webp --with-jpeg
RUN NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
    docker-php-ext-install -j${NPROC} \
        mysqli \
        pdo \
        pdo_mysql \
        bcmath  \
        intl \
        gd \
        calendar \
        zip \
        imap \
        pcntl

RUN pecl install xdebug-${XDEBUG_VERSION} && \
    pecl install redis openswoole && \
    docker-php-ext-enable redis openswoole && \
    apk del autoconf g++ make && \
    rm -rf /usr/share/man /tmp/* /var/cache/apk/* /tmp/pear && \
    sed -i 's|www-data:/sbin/nologin|www-data:/bin/sh|g' /etc/passwd

# install composer
RUN curl https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar \
    > /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

WORKDIR /app
# COPY . /app

CMD php artisan serve --host=0.0.0.0
