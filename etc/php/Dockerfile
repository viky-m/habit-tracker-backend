FROM php:8.4-fpm

ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --no-install-recommends apt-utils

# ==============================================================================
# basic software
RUN apt-get update && apt-get install -y \
    apt-transport-https \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxml2-dev \
    libxslt-dev \
    libpng-dev \
    zlib1g-dev \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    curl \
    cron \
    mariadb-client \
# and admin tools
    sudo \
    openssh-client \
    net-tools \
    iputils-ping \
    telnet \
    nmap \
    git \
    git-flow \
    vim \
    nano \
    mc

# ==============================================================================
# PHP extensions
RUN \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
    gd \
    bcmath \
    gettext \
    iconv \
    pdo_mysql \
    mbstring \
    pcntl \
    soap \
    intl \
    xsl \
    zip \
    exif \
    pcntl

# install Imagemagick & PHP Imagick ext
RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends

# install imagick (Latest stable version for PHP 8.4)
RUN pecl install imagick \
    && docker-php-ext-enable imagick;

# ==============================================================================
# install xdebug
RUN \
    pecl install xdebug \
    && docker-php-ext-enable xdebug
RUN \
        echo "xdebug.mode=debug,coverage" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=\${XDEBUG_REMOTE_AUTOSTART}" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=\${XDEBUG_REMOTE_HOST}" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=\${XDEBUG_REMOTE_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.idekey=\${XDEBUG_IDE_KEY}" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/xdebug.ini

# ==============================================================================
# install composer
RUN \
    curl -s https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

RUN apt-get update && apt-get install openssh-server -y

# remove temp files
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# ==============================================================================
COPY php_config.ini /usr/local/etc/php/conf.d/config.ini

# ==============================================================================
# User settings
ARG COMPOSE_USER_ID
ARG COMPOSE_GROUP_ID

RUN addgroup --gid $COMPOSE_GROUP_ID xdocker \
    && useradd -ms /bin/bash --gid $COMPOSE_GROUP_ID -u $COMPOSE_USER_ID xdocker \
    && adduser xdocker sudo \
    && echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers \
    && usermod -a -G www-data xdocker \
    && usermod -a -G root xdocker

# Permissions for Laravel
RUN chmod gu+rw /var/run
RUN chmod gu+s /usr/sbin/cron

COPY cron.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/cron.sh

ENTRYPOINT ["cron.sh"]

USER xdocker

WORKDIR /var/www
VOLUME /var/www

EXPOSE 22


