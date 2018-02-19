FROM php:7.1-apache

ARG DEV_USER_UID=1000

ARG PHING2_VERSION=2.16.0
ARG PHPUNIT5_VERSION=5.7
ARG PHPUNIT6_VERSION=6.3

MAINTAINER Adam Gąsowski <adam.gasowski@gander.pl>

RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
    openssh-client \
    postgresql-client \
    mysql-client \
    sudo \
    git \
    wget \
    curl \
    cron \
    nano \
    libicu-dev \
    libmcrypt-dev \
    libpq-dev \
    libpng12-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxslt-dev \
    libtidy-dev \
    && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-install bcmath \
    && docker-php-ext-install gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install opcache \
    && docker-php-ext-install pcntl \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql \
    && docker-php-ext-install sockets \
    && docker-php-ext-install zip \
    && docker-php-ext-install soap \
    && pecl install xdebug \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

ADD rootfs /

RUN curl -LsS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
RUN curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony && chmod a+x /usr/local/bin/symfony

RUN curl -LSs https://box-project.github.io/box2/installer.php | php && mv box.phar /usr/local/bin/box

RUN curl -LsS http://robo.li/robo.phar -o /usr/local/bin/robo && chmod +x /usr/local/bin/robo

RUN curl -LsS http://www.phing.info/get/phing-${PHING2_VERSION}.phar -o /usr/local/bin/phing && chmod +x /usr/local/bin/phing

RUN curl -LsS https://phar.phpunit.de/phpunit-${PHPUNIT5_VERSION}.phar -o /usr/local/bin/phpunit5 && chmod +x /usr/local/bin/phpunit5
RUN curl -LsS https://phar.phpunit.de/phpunit-${PHPUNIT6_VERSION}.phar -o /usr/local/bin/phpunit6 && chmod +x /usr/local/bin/phpunit6

RUN ln -s /usr/local/bin/phpunit5 /usr/local/bin/phpunit

RUN a2enmod rewrite && a2enmod vhost_alias && a2enconf vhost-alias

RUN printf "alias l='ls -CF'\nalias la='ls -A'\nalias ll='ls -alF'\n" >> /etc/bash.bashrc
RUN printf "if [ -d \"\$HOME/.composer/vendor/bin\" ]; then\n    PATH=\"\$HOME/.composer/vendor/bin:\$PATH\"\nfi" >> /etc/bash.bashrc

RUN adduser --disabled-password --gecos '' --uid ${DEV_USER_UID} dev \
    && adduser dev sudo \
    && printf "dev ALL=(ALL) NOPASSWD: ALL\n" > /etc/sudoers.d/dev

RUN printf "export APACHE_RUN_USER=dev\nexport APACHE_RUN_GROUP=dev\n" >> /etc/apache2/envvars

RUN chown -R dev:dev /var/lock/apache2 /var/log/apache2

USER dev

VOLUME /www
WORKDIR /www

EXPOSE 80
CMD ["sudo", "apache2-foreground"]
