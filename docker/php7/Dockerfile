FROM php:8.0.0-fpm

RUN apt-get -y update
# RUN curl -L -C - --progress-bar -o /usr/local/bin/composer https://getcomposer.org/composer.phar
# RUN curl -L -C - --progress-bar -o /usr/local/bin/composer https://getcomposer.org/download/latest-1.x/composer.phar
RUN curl -L -C - --progress-bar -o /usr/local/bin/composer https://getcomposer.org/download/latest-stable/composer.phar
RUN chmod 755 /usr/local/bin/composer
RUN apt-get install -y git
RUN docker-php-ext-install pdo_mysql mysqli
# RUN composer start
# RUN pecl install xdebug-2.9.6 && docker-php-ext-enable xdebug
