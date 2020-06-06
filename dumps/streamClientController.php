<?php
/*
clear;php /home/cli.php streamClient 500000 1000000
*/
$adr='udp://127.0.0.1:1983';$i=0;
$rmtu=1500;
$mtu=4096;
$nb=intval($GLOBALS['argv'][1]);
$split=intval($GLOBALS['argv'][2]);#$mtu;#200;#round($mtu*0.1);#max around 500k
if($split>$nb)$split=$nb;
$put=str_repeat('.',$nb);#implode(',',$GLOBALS['argv']);


/*0ms for mut=4096
1492
Took:1837ms
fastbus/messages/20201019153131-.msg
len:20774086
*/

/*
phpgit;log phpgit_php74;
phpx /home/cli.php streamServer
php /home/cli.php streamClient 1
for i in {0..9999};do php cli.php streamClient $i & done;
stream socket client fwrite in multiple connections
*/
class streamClientController extends base{}

if($nb==20000000){
    $put='';
    while($tot<$nb){
        $re="\n".$i.'-';$i++;
        $put.=$re.str_repeat('.',$mtu-strlen($re));
        $tot+=$mtu;
    }
    #$put.="\n".str_repeat('a',$totlen);#21 200 000 = 21Mo / 42 ==> 500ko each
    #$put.=';'.str_repeat('a',16000);#21 200 000 = 21Mo / 42 ==> 500ko each
}

#Protocol "udp" not supported or disabled in libcurl
#$a = curl("udp://$host", [CURLOPT_PORT => $port,CURLOPT_HTTPHEADER, ['Expect:']], ['udpClient' => 'sentThis']);_die();
#stream_socket_client blocked fread

# STREAM_CLIENT_CONNECT (défaut), STREAM_CLIENT_ASYNC_CONNECT et STREAM_CLIENT_PERSISTENT.
$fp=stream_socket_client($adr, $errno, $errstr, 30);#stream_socket_client fwrite
#$fp=stream_socket_client($adr, $errno, $errstr, 30);#stream_socket_client fwrite
#stream_socket_client ( string $remote_socket [, int &$errno [, string &$errstr [, float $timeout = ini_get("default_socket_timeout") [, int $flags = STREAM_CLIENT_CONNECT [, resource $context ]]]]] ) : resource
#stream_set_blocking($fp, 0);#pour être certain de tout transmettre !

if (!$fp) {echo "$errstr ($errno)<br />\n";} else {
    #fwrite($fp,$put);die;#17ms avec défauts ..
#stream_socket_sendto($fp,$put);#does nothing#fwrite_stream($fp,$put);
    $len=strlen($put);$nb=$sent=$over=0;
    while(!$over && $sent<$len){
        $nb++;
        $sentNow=fwrite($fp,substr($put,$sent,$split),$split);#new connection each time ??
        $sent+=$sentNow;
        if(!$sentNow)$over=1;
    }
_die(strlen($put)."\n");
    $buf=stream_socket_recvfrom($fp, 1024, 0, $peer);
    if($buf){file_put_contents('response.log',"\n".json_encode(compact('g','buf')));}
    print_r(compact('nb','peer','buf','sent'));

    fclose($fp);_die();
    #fwrite($fp,$put,strlen($put));
    #fflush($fp);

    #echo stream_get_contents($fp);
    #echo fread($fp,6);

    #
    $read = [$fp];
    $null = NULL;
//wait a quarter second (250ms) to see if $apns has something to read
    $nChangedStreams = @stream_select($fp, $null, $null, 0, 250000);
    if ($nChangedStreams === false) {
        $a=1;
        //ERROR: Unable to wait for a stream availability.
    } else if ($nChangedStreams > 0) {
        $a=1;
        //there is something to read, time to call fread
        $response = fread($apns,6);
        $response = unpack('Ccommand/Cstatus_code/Nidentifier', $response);
        //do something with $response like:
        if ($response['status_code'] == '8') { //8-Invalid token
            //delete token
        }
    }
    fclose($fp);
    _die();
    echo fread($fp);
    _die();
    echo fread($fp, 1024);#never completing
    fclose($fp);/*
    while (!feof($fp)){
        $x=fgets($fp, 1024);
        if($x==FALSE)break;
    }
    fclose($fp);*/
}
_die();

function fwrite_stream($fp, $string) {
    for ($written = 0; $written < strlen($string); $written += $fwrite) {
        $fwrite = fwrite($fp, substr($string, $written));
        if ($fwrite === false) {
            return $fwrite;
        }
    }
    return $written;
}
