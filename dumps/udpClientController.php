<?php
class udpClientController extends base{}

#enable extension sockets first
#phpx $web/phpgit/client.php
require_once'app/functions.php';
$host = "127.0.0.1";
$port = 1983;
$timeout = 30;

$a = curl("http://$host", [CURLOPT_PORT => $port,CURLOPT_HTTPHEADER, ['Expect:']], ['udpClient' => 'sentThis']);
echo $a['contents'];
#print_r($a);
_die();
$a=1;
return;
?>
$sk = fsockopen($host, $port, $errnum, $errstr, $timeout);
if (!is_resource($sk)) {
exit("connection fail: " . $errnum . " " . $errstr);
} else {
echo "Connected";
}
