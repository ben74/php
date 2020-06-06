<?php class streamServerController extends base{}/*
phpgit;log phpgit_php74;
clear;php /home/cli.php streamServer
log phpgit_php74;clear;php /home/cli.php streamClient 0
*/
function ec($x){return;echo $x;}
ini_set('memory_limit',-1);$maxLength=8192;#8192 is the maximal
$adr='udp://0.0.0.0:1983';$fl=pow(2,13);#Max Transmitted per connection :: //*12:4k,16:64k*/;#splitted each
/*
for i in {0..9999};do echo $i;php /home/cli.php streamClient $i & done;
https://www.binarytides.com/socket-programming-streams-php/
clear;php56  -dxdebug.remote_autostart=1 cli.php streamServer
*/
$_w=$_e=NULL;
$nbr=$killed=$cum=$sleep=0;$waitNextConnectionEach=1;
/*$context = stream_context_create();
stream_context_set_option($context, 'ssl', 'local_cert', $pemfile);
stream_context_set_option($context, 'ssl', 'passphrase', $pem_passphrase);
stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
stream_context_set_option($context, 'ssl', 'verify_peer', false);
$server = stream_socket_server('ssl://0.0.0.0:9001', $errno, $errstr, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $context);*/
$socket = stream_socket_server($adr,$errno,$errstr,STREAM_SERVER_BIND);#
stream_set_blocking($socket, 0);
$read=$connections=[$socket];
$msg=[];

#php stream_socket_server stream_select
if (!$socket) {
    echo "Error : no socket : $errstr ($errno)<br />\n";
} else {
    echo "\nServer listening $adr .. $fl";
    $tot='';$j=0;
    while (!$killed) {
        $read = $connections;#??
#toujours réalisés les uns après les autres ... sauf si l'un d'entre eux fait un gros gros truc
#stream_socket_recvfrom() and stream_socket_sendto().
        $mod_fd = stream_select($read, $_w, $_e, $waitNextConnectionEach);#socket_select vs stream_select
        if($_w){print_r($_w);}if($_e){print_r($_e);}## Jamais vus ..
        if ($mod_fd === FALSE) {break;}#0 if none pending => Loops
        if ($mod_fd === 0) {
            if($msg){
                echo"\n\nTook:".round(($last-$time)*1000).'ms';
                foreach($msg as $peer=>&$data){
                    $p=preg_replace('~[^a-z0-9\.\-_]~is','',$peer);
                    $f='fastbus/messages/'.date('YmdHis').'-'.$p.'.msg';
                    echo"\n$f";
                    $len=strlen($data);
                    echo "\nlen:$len";
                    file_put_contents($f,$data);
                    $data=null;
                }unset($data);
                $msg=array_filter($msg);
                $time=null;#ready new chrono
            }
            $cum=0;
        }
$a=1;
        for ($i = 0; $i < $mod_fd; ++$i) {
#Long message is 42 connections with nothing sent .. mhhh ...
            if ($read[$i] === $socket) {#is the master socket
#WONT WORK
if(0){
    while ($conn = stream_socket_accept($read[$i],30,$peer)) {
        socket_set_blocking($conn, 0);
        $connections[] = $conn;
        echo "new client! id: " . array_key_last($connections) . " peername: {$peername}\n";
    }
}/*
#Run 2 php74 docker images
#$conn = stream_socket_accept($socket,ini_get('default_socket_timeout'),$ip);#$connections[] = $conn;Loops like heel
#transmission splitted
*/
ec("\n - $mod_fd pending for $nbr th connection ");
#stream_socket_recv()
if(!$time)$time=microtime(1);
#http_chunk = fread($backend_socket_connect, 8192);
while($buf=fread($socket,$fl)){#peer:
#while($buf=stream_socket_recvfrom($socket,$fl,0,$peer)){#peer:
#while($buf=stream_socket_recvfrom($socket,$fl,0,$peer)){#peer:
    $len=strlen($buf);$cum+=$len;$msg[$peer].=$buf;
    #ec("\n$peer,line:$j;len:".strlen($buf).','.$cumulated);$j++;
    if(!$msg[$peer]){echo"\tnothing sent $peer -- timeout ?";}
    if(0 and $len<$maxLength){#$over=0;
        echo"\n\t\tover";$over=1;$tot='';$j=0;
        $len=strlen($msg[$peer]);
        $a=1;
        /*
        file_put_contents('fastbus/messages/'.date('YmdHis').'-'.$peer.'.msg',$tot,8);
        $md5=mD5($tot);
        #file_put_contents('server.log',"\n$i:$tot",8);
        stream_socket_sendto($read[$i],'>'.$md5,0,$peer);#socket_close($socket);
        fwrite($socket,'>'.mD5($tot));
        */
    }
}
#ec("\n -- end $nbr th connection ");#then .. Where the fuck does it goes
$nbr++;
$last=microtime(1);
#stream_socket_sendto($socket,'--dssd');
#socket_write($socket, '---ssdfssfs' );
/*
#$x=stream_get_contents($socket);
// from here you need to do your database stuff
// and handle the response
// Display output  back to client
#$input = socket_read( $socket, 1024000 );
*/
                if($i>0){
                    fclose($read[$i]);
                }
                $a=1;
            } else {
                echo "\n - 2) " . date('H:i:s') . ' : ';
                #$tot.=$sock_data=socket_read($read[$i], $fl);
                while($buf=socket_read($read[$i], $fl)) {
                #while($buf=stream_socket_recvfrom($read[$i], $fl, 0, $peer)) {
                    $tot .= $buf;continue;
                }
                file_put_contents('server.log',"\n$i:$tot",8);
                $md5=mD5($tot);
                stream_socket_sendto($read[$i],'>'.$md5,0,$peer);#socket_close($socket);
                fwrite($socket,'>'.mD5($tot));
                fclose($read[$i]);

                $sock_data = fread($read[$i], $fl);
                $key_to_del = array_search($read[$i], $connections, TRUE);
                #var_dump($sock_data);#bool(false)
                if ($sock_data === FALSE) {
                    echo'.';
                    fclose($read[$i]);unset($connections[$key_to_del]);
                    break;#connection finished
                }  elseif (strlen($sock_data) === 0) { // connection closed
                    fclose($read[$i]);unset($connections[$key_to_del]);
                } else {
                    echo "The client has sent :"; echo $sock_data;
                    fwrite($read[$i], "You have sent :[".$sock_data."]\n");
                    fclose($read[$i]);
                    unset($connections[array_search($read[$i], $connections)]);
                }
            }
        }#end for
        $a=1;
    }#end vhile
}
_die('ned');
