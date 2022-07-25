<?php // Better than Memcached or Redis ?? Achieving Same times, but using opcache, in the end, will result into better times ( if out of memory LFU files will be discarded from memory but will remain upon filesystem hence completing its purpose )
opcache_invalidate(__FILE__);// when developing on a porcelain environment
//phpinfo();die;
$memcached = true;
$redis = true;
$write = $invalidate = false;//phpinfo();die; writing the file automatically invalidates it if revalidate_file set, otherwise put true on porcelain environments ( on fixed neverending opcache )
$data = [time()];
$f = 'opcached.php';
/*
Porcelain Example for microservice serving opcached sql queries :

opcache.enable=1
opcache.enable_cli=1;when using php server
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.memory_consumption=8;minimal value
opcache.interned_strings_buffer=2;

; Interesting
;opcache_compile_file()
;opcache.preload
 */

if ($memcached) {
    try {
        $last = microtime(1);
        $m = new \Memcached();
        $m->addServer('localhost', 11211);
        $statuses = $m->getStats();
        if (isset($statuses['localhost:11211'])) {

            $now = microtime(1);
            $a['m:connect'] = round(($now - $last) * 1000000);//    85 ùs

            $last = microtime(1);
            $x = $m->get($f);
            echo json_encode($x);
            $now = microtime(1);
            $a['m:get1'] = round(($now - $last) * 1000000);//    654 à 1080 ùs

            $x = new \StdClass;
            $x->a = time();
            $x = [time()];

            $last = microtime(1);
            $m->set($f, $x);
            $now = microtime(1);
            $a['m:set'] = round(($now - $last) * 1000000);  //  115 ùs

            $last = microtime(1);
            $x = $m->get($f);
            echo json_encode($x);
            $now = microtime(1);
            $a['m:get2'] = round(($now - $last) * 1000000); //  88 ùs
        }else{
            echo',no connection to memcached';
        }
    } catch (\Throwable $e) {
        echo ',memcached:' . $e->getMessage();#
    }
}

if ($redis) {
    try {
        $last = microtime(1);
        $r = new \Redis();
        $r->connect('127.0.0.1', 6379);
        $now = microtime(1);
        $a['r:connect'] = round(($now - $last) * 1000000); //  186 ùs

        if ($r->exists($f)) {
            $last = microtime(1);
            $r->get($f);
            $now = microtime(1);
            $a['r:get1'] = round(($now - $last) * 1000000);
        }
        $last = microtime(1);
        $r->set($f, json_encode($data));
        $now = microtime(1);
        $a['r:put'] = round(($now - $last) * 1000000);

        $last = microtime(1);
        $r->get($f);
        $now = microtime(1);
        $a['r:get2'] = round(($now - $last) * 1000000);
    } catch (\Throwable $e) {
        echo ',redis:' . $e->getMessage();#
    }

}


$last = $start = microtime(1);
if (is_file($f)) {
    $last = microtime(1);
    $first = $z = require $f;
    $now = microtime(1);
    //echo json_encode($z).' , ';
    $a['opc:get'] = round(($now - $last) * 1000000);
}
if ($write or !is_file($f)) {
    $last = microtime(1);
    file_put_contents($f, '<?php return ' . var_export($data, 1) . ';');
    $now = microtime(1);
    $a['opc:put'] = round(($now - $last) * 1000000);
}
if ($invalidate) {
    $last = microtime(1);
    opcache_invalidate($f, true);//each 2 sec otherwise (revalidate_freq).. opcache_compile_file()  opcache.preload
    $now = microtime(1);
    $a['opc:inv'] = round(($now - $last) * 1000000);
}

if (is_file($f)) {
    $last = microtime(1);
    $z = require $f;
    echo "=" . json_encode($z);
    $now = microtime(1);
    $a['opc:get2'] = round(($now - $last) * 1000000);
}


echo json_encode($a);
die;


?>
o /usr/local/etc/php/8.1/php.ini
pkill -9 -f  nginx;nginx -c $bf/ben/127nginx.conf &>/dev/null &     pkill -9 -f php-fpm;    /usr/local/Cellar/php/8.1.3_1/sbin/php-fpm -dxdebug.start_with_request=1 &

curl -L http://127.0.0.1/php/opcacheVsRedisVsMemcached.php
curl -s https://raw.githubusercontent.com/amnuts/opcache-gui/master/index.php -o opcachegui.php

redis-server &

brew install libmemcached
brew install zlib
pecl install memcached
brew install memcached

memcached &

pkill -9 -f memcached;pkill -9 -f redis
memcached -V
telnet localhost 11211

brew services stop redis
brew services stop memcached


echo 'set foo 0 900 5' | nc localhost 11211
echo 'get foo' | nc localhost 11211
get foo
quit


echo 'flush_all' | nc localhost 11211