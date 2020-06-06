function reload() { . $BASH_SOURCE; }
function phpx() {
     php -dxdebug.remote_autostart=1 $@;
}

#XDEBUG_CONFIG="remote_host=192.168.1.99"
PHP_IDE_CONFIG="serverName=php.home";XDEBUG_CONFIG="idekey=ECLIPSE";
export $PHP_IDE_CONFIG;export $XDEBUG_CONFIG;

function on() {
    php-fpm 2>/dev/null &
    /usr/sbin/nginx -g "daemon off;" 2>/dev/null & 
    #service nginx start
}

function off() {
    pkill php-fpm;pkill nginx;
}

function phpr() {
    pkill php-fpm;php-fpm 2>/dev/null &
}
function httpr() {
    pkill nginx;/usr/sbin/nginx -g "daemon off;" 2>/dev/null  &
}