<?php class tcpServerController extends base{}
$maxLength=8192;#pkill php;pkill 1;phpx /home/cli.php tcpServer
#netstat -a;pkill php;pkill 1;php /home/cli.php tcpServer
if (($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) _die("Couldn't create socket" . socket_strerror(socket_last_error()) . "\n");
if (socket_bind($socket,'0.0.0.0',1983) === false)_die( "Bind Error " . socket_strerror(socket_last_error($sock)) . "\n");
if (socket_listen($socket, 5) === false) _die("Listen Failed " . socket_strerror(socket_last_error($socket)) . "\n");

#socket_set_nonblock($socket,1);#shall use select
$killed=$c=0;
while(!$killed) {
    $tot='';$tlen=$loops=0;
    if (($msgsock = socket_accept($socket)) === false) {echo "Error: socket_accept: " . socket_strerror(socket_last_error($socket)) . "\n";continue;}
    echo "\nNew conn : $c \n";
    $a=microtime(1);
    while(!$killed) {
        $buf = socket_read($msgsock, $maxLength, PHP_BINARY_READ);#PHP_NORMAL_READ
        $len = strlen($buf);
        if ($buf===false) {#Binary ?
            echo "socket read error: " . socket_strerror(socket_last_error($msgsock)) . "\n";
            continue 2;
        }
        if ($len==0/*!$buf = trim($buf)*/) {
            break;
        }
        $tot.=$buf;$tlen+=$len;$loops++;
        #echo "\n".$talkback;
    }
    $b=microtime(1);
    $ms=round(($b-$a)*1000);
    $reply="$c : $tlen bytes transferred in $ms ms with $loops loops";#36ms
    echo "\nover:$reply:";
    #fwrite($msgsock, $reply);
    #echo",sent:reply:".fwrite($msgsock, $reply, strlen($reply));#nb bytes sent
    echo",sent:reply:".socket_write($msgsock, $reply, strlen($reply));#nb bytes sent
    socket_close($msgsock);
    $c++;
}
socket_close($socket);
_die('over');
?>
