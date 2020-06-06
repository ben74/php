<?
$a=1;
if(!isset($argv)){?>
<pre>
Usage :
cd mvc/controllers;
    php -S 127.0.0.1:1983 phpServerController.php
    php cli.php phpSendCurlToServer 0;#send payload to regular http server
php cli.php tcpServer;#set tcp Server ons
php cli.php tcpClient 20000000 2000000;#transmit data to tcp Server
</pre>
<?die;
}
if(count($argv)<2)die('argv<2');
chdir(__DIR__);
$_SERVER['HTTP_HOST']='php.home';
$_ENV['argv']=$argv;array_shift($_ENV['argv']);
require_once'404.php';
?>
