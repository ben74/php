FROM php:7.4-fpm-alpine

RUN apk add bash htop
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install opcache
RUN docker-php-ext-install sockets

RUN echo 'php74' > /whoami && sed -i 's#listen = 127.0.0.1:9000#;listen = 127.0.0.1:9074#gi' /usr/local/etc/php-fpm.d/www.conf && sed -i 's#listen = 9000#listen = 9074#gi' /usr/local/etc/php-fpm.d/zz-docker.conf;
#stop php74;run php74;log php74

EXPOSE 9074
CMD ["php-fpm"]

COPY e.sh /usr/local/bin/entry
ENTRYPOINT ["entry"]
WORKDIR /

#RUN docker-php-ext-install apcu
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug && docker-php-ext-enable xdebug && docker-php-ext-install pcntl


