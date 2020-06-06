<?
class phpServerController{}# extends base{}
#cd /home/mvc/controllers;php -S 127.0.0.1:1983 phpServerController.php
if (php_sapi_name() != 'cli-server'){
    die("Please run it using the absolute path : \n\tphp -S 127.0.0.1:1983 ".__file__."\nThen :\n\tphp ".__DIR__."/client.php\n");
}
if (php_sapi_name() == 'cli-server' and 1 and 'does responds this') {
    $a=microtime(1);
    $c=trim(file_get_contents('php://input'));#could be regular postdata .. what for cli ?
    echo strlen($c);
    echo"\n\nTook:".((microtime(1)-$a)*1000)."ms\n\n";
}
die();
