f='/zzz.install.1';if [ ! -f "$f" ]; then
    ln -s /home/docker/.bashrc /root/.bashrc;
    #ln -s /home/docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini;#8 has none
    rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    touch $f;
fi;