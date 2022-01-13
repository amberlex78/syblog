FROM php:8.1-fpm

# First
RUN export DEBIAN_FRONTEND="noninteractive" && \
    apt-get update && apt-get install -y dialog apt-utils libpq-dev zip unzip curl wget git && \
    echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

# Imagick and GD
RUN apt-get update && apt-get install -y --no-install-recommends \
    libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libgmp-dev && \
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    docker-php-ext-configure gd --with-freetype=/usr/include && \
    docker-php-ext-install gd

# Packages
RUN apt-get update && apt-get install -y --no-install-recommends \
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
    libnss3-tools \
    libssl-dev \
    libsqlite3-dev \
    libzip-dev \
    libonig-dev \
    exiftool && \
    rm -rf /var/lib/apt/lists/*

# PHP Extensions
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd && \
    docker-php-ext-install pdo_mysql \
    bcmath \
    bz2 \
    exif \
    intl \
    opcache \
    soap \
    sockets \
    xsl \
    zip

# Versions: https://xdebug.org/download/historical
ENV XDEBUG_VERSION 3.1.2
RUN pecl install xdebug-$XDEBUG_VERSION && docker-php-ext-enable xdebug

# NodeJS with NPM, Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get update && apt-get install -y nodejs
RUN npm i -g yarn

# Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer -q

# Config PHP
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
COPY ./conf.d $PHP_INI_DIR/conf.d

# Config Git
RUN git config --global user.email "username@email.com" && \
    git config --global user.name "username"

# Symfony
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony/bin/symfony /usr/local/bin && \
    echo 'alias s="php bin/console"' >> ~/.bashrc

WORKDIR /app
