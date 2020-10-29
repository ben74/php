FROM php:8.0-rc-fpm-alpine
#make it writable please
VOLUME /sys/fs/cgroup
#x=phpgit_php8;stop $x;docker rm $x;docker image rm $x;fig up -d $x;log $x;
 
RUN apk add --no-cache bash htop libzip-dev memcached redis nginx openrc less vim nano zlib-dev autoconf gcc g++ make libpng-dev
RUN docker-php-ext-install gd zip mysqli pdo pdo_mysql opcache sockets pcntl
RUN echo 'php80' > /whoami && sed -i 's#listen = 127.0.0.1:9000#;listen = 127.0.0.1:9080#gi' /usr/local/etc/php-fpm.d/www.conf && sed -i 's#listen = 9000#listen = 9080#gi' /usr/local/etc/php-fpm.d/zz-docker.conf;

RUN wget https://github.com/FriendsOfPHP/pickle/releases/download/v0.6.0/pickle.phar && mv pickle.phar /usr/local/bin/pickle
RUN chmod +x /usr/local/bin/pickle
RUN pickle install lzf
RUN pickle install apcu
RUN pickle install igbinary

#make erreor
#RUN pickle install memcache
#RUN pickle install xdebug
#RUN pickle install swool

#/usr/local/lib/php/extensions/no-debug-non-zts-20200930/redis.so,apcu,igbinary

RUN pickle install redis

RUN rc-update add nginx default
RUN mkdir /run/nginx/

RUN mkdir /run/openrc && touch /run/openrc/softlevel
RUN adduser -D -g 'www' www && chown -R www:www /var/lib/nginx && chown -R www:www /home

EXPOSE 9080 80 443
CMD ["php80"]

COPY e.sh /usr/local/bin/entry
ENTRYPOINT ["entry"]
WORKDIR /

ENV TERM="xterm" LANG="C.UTF-8" LC_ALL="C.UTF-8"

RUN apk add vsftpd
RUN cp /etc/vsftpd/vsftpd.conf /etc/vsftpd/vsftpd.conf.back
RUN rm -rf /var/cache/apk/*
