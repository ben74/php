f='/zzz.install.1';if [ ! -f "$f" ]; then
    mkdir /run/nginx;
    #printf 1 > /run/nginx/nginx.pid
    rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    rm /etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf
 
    ln -s /home/docker/.bashrc /root/.bashrc;    
    ln -s /home/docker/php8/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini;#8 has none xdebug installed ( pickle, pecl .. )
    
    ln -s /home/conf/nginx/nginx.conf /etc/nginx/nginx.conf
    ln -s /home/conf/nginx/default.conf /etc/nginx/conf.d/default.conf    
    touch $f;
fi;