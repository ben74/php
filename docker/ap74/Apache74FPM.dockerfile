FROM php:7.4-fpm-alpine
#stop ap74;docker-compose build ap74;fig up -d ap74;log ap74;
#docker compact image layers => squash layers
#docker image build --squash	
VOLUME /sys/fs/cgroup
RUN apk update \
    && NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && apk add iproute2 procps mailx postfix certbot git curl tar openssh openssl bash htop autoconf apache2 redis rsync make gcc mariadb-client mysql apache2-utils apache2-ssl apache2-proxy gzip sudo libressl nano vim libsodium-dev curl-dev libmcrypt-dev libmemcached-dev libpng-dev openssh-server openrc ${PHPIZE_DEPS} \
    && mkdir /var/run/sshd && pecl channel-update pecl.php.net && pecl install apcu igbinary memcached redis mcrypt \
    && pecl install xdebug && docker-php-ext-enable xdebug && docker-php-ext-install pcntl \
    && docker-php-ext-install -j$NPROC gd \
    && docker-php-ext-install mysqli && docker-php-ext-install pdo_mysql && docker-php-ext-install opcache && docker-php-ext-install sockets && docker-php-ext-enable sodium \
    && apk del make gcc autoconf libsodium-dev curl-dev libmcrypt-dev libmemcached-dev libpng-dev ${PHPIZE_DEPS} \
    && rc-update add sshd && rm -rf ~/.pearrc && rm -rf /var/cache/apk && rm -rf /tmp && mkdir /var/cache/apk && mkdir /tmp
#rm -rf /var/lib/{apt,dpkg,cache,log}
#RUN apk add .phpize-deps ${PHPIZE_DEPS}
#RUN apk add .memcached-deps ${MEMCACHED_DEPS}

#usr/local/lib/php/extensions/no-debug-non-zts-20190902/apcu.so
#usr/local/lib/php/extensions/no-debug-non-zts-20190902/redis.so
#usr/local/lib/php/extensions/no-debug-non-zts-20190902/igbinary.so
#extension=/usr/local/lib/php/extensions/no-debug-non-zts-20190902/memcached.so
#RUN docker-php-ext-enable igbinary && docker-php-ext-enable memcached && 
#&& docker-php-ext-install pdo 
RUN chmod -R 777 /tmp && echo 'php74' > /whoami && sed -i 's#listen = 127.0.0.1:9000#;listen = 127.0.0.1:9074#gi' /usr/local/etc/php-fpm.d/www.conf && sed -i 's#listen = 9000#listen = 9074#gi' /usr/local/etc/php-fpm.d/zz-docker.conf;
#stop php74;run php74;log php74

RUN x=`cat /etc/passwd | grep www-data`;if [ -z "$x" ]; then adduser -u 82 -D -S -G www-data www-data;fi;
RUN mkdir -p /usr/src && mkdir -p /run/apache2/ && mkdir -p /run/mysqld/ && chmod -R 777 /run/mysqld/;
RUN (crontab -l ; echo "* * * * * /home/1mincron.sh")| crontab -;
RUN mkdir -p /run/mysqld /run/apache && chmod -R 777 /run;
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/sbin --filename=composer;

#RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-png-dir=/usr/include/ --with-jpeg-dir=/usr/include/; 
# && docker-php-ext-install mcrypt
#RUN printf "\n" | pecl install imagick
#RUN docker-php-ext-install imagick
#imagemagick imagemagick-dev 
# -f /tmp/sshkey
RUN mkdir -p "/run/openrc/" && touch /run/openrc/softlevel
RUN echo "" | ssh-keygen -b 2048 -t rsa -q -N ""

#echo "init=/home/docker/ap74/init.sh;  if [ -f "$init" ] ;then find /home -type f > home.log;bash $init > init.log 2>&1 &;fi; tail -f /dev/null;">/usr/local/bin/entry
RUN echo "coucou3";
COPY e.sh /usr/local/bin/entry
CMD ["php-fpm"]
ENTRYPOINT ["entry"]
WORKDIR /