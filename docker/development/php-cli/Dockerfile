FROM php:8.0-cli

RUN export DEBIAN_FRONTEND="noninteractive" && \
    apt-get update && apt-get -y install dialog apt-utils libpq-dev zip unzip && \
    echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer -q

# Versions: https://xdebug.org/download/historical
ENV XDEBUG_VERSION 3.0.4
RUN pecl install xdebug-$XDEBUG_VERSION && docker-php-ext-enable xdebug

# NodeJS with NPM, Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get update && apt-get install -y nodejs
RUN npm i -g yarn

# GD (need for bin/console)
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgmp-dev && \
    docker-php-ext-configure gd --with-freetype=/usr/include && \
    docker-php-ext-install gd

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libbz2-dev \
    libxml2-dev \
    libxslt-dev \
    zlib1g-dev \
    libncurses5-dev \
    libldb-dev \
    libldap2-dev \
    libicu-dev \
    libmemcached-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    libsqlite3-dev \
    libzip-dev \
    libonig-dev \
    exiftool \
    curl \
    wget && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
    docker-php-ext-install pdo_mysql && \
    docker-php-ext-configure mysqli --with-mysqli=mysqlnd && \
    docker-php-ext-install mysqli \
    bcmath \
    bz2 \
    exif \
    intl \
    opcache \
    soap \
    sockets \
    xsl \
    zip

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
COPY ./conf.d $PHP_INI_DIR/conf.d

WORKDIR /app
