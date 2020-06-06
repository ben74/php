<?php class tcpClientController extends base{}
#phpx /home/cli.php tcpClient 20000000 20000000
$nb=intval($GLOBALS['argv'][1]);
$split=intval($GLOBALS['argv'][2]);#$mtu;#200;#round($mtu*0.1);#max around 500k
if($split>$nb)$split=$nb;$put=str_repeat('.',$nb);
$adr='tcp://127.0.0.1:1983';$i=0;$mtu=4096*2;
$a=microtime(1);
#If socket_set_nonblock was called in prior, and PHP_BINARY_READ is used, socket_read will return false
$fp=stream_socket_client($adr, $errno, $errstr, 30);
#socket_set_nonblock($fp,1);socket_set_block($fp,1);
if (!$fp) {echo "$errstr ($errno)<br />\n";} else {
    $len=strlen($put);$nb=$sent=$over=0;
    if(0)$sent=socket_write($fp,$put,strlen($put));
    else{
        while(!$over and $sent<$len){
            $nb++;
            #$sentNow=socket_write($fp,substr($put,$sent,$split),$split);#nah
            $sentNow=fwrite($fp,substr($put,$sent,$split),$split);
            $sent+=$sentNow;
            if(!$sentNow){$tapeloop=$over=1;}
        }
    }
$rec=$over=0;$tot='';
while(!$over) {$rec++;
    #$line=fread($fp,$mtu);
    #socket_recv($fp,$line, 1024);
    #$line=fread($fp,$mtu);#Fread is blocking .. PHP_NORMAL_READ - la lecture s'arrête aux \n et \r
    $line=socket_read($fp,$mtu,PHP_BINARY_READ);#PHP_NORMAL_READ - la lecture s'arrête aux \n et \r
    echo"\n".strlen($line). ' :: '.$line;
    $tot.=$line;
    if(!$line)$over=1;
/*$message = parseLine($line);
if($message->type === 'QUIT') {
    echo"quit";
    break;
}*/
}
echo"\nsocket error:";print_r(socket_strerror(socket_last_error($fp)));echo"\n";

socket_close($fp);
#$buf=fread($fp,10);
    $b=microtime(1);
    $ms=round(($b-$a)*1000);
    print_r(compact('ms','rec','nb','peer','tot','sent'));




    _die();

    $buf=stream_socket_recvfrom($fp, 1024, 0, $peer);
    if($buf){file_put_contents('response.log',"\n".json_encode(compact('g','buf')));}


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
