<?php
class tcpServer2Controller extends base{}/*
phpgit;log phpgit_php74;netstat -a
clear;php /home/cli.php tcpServer
clear;php /home/cli.php tcpClient
*/
ini_set('memory_limit',-1);$maxLength=8192;
$adr='tcp://0.0.0.0:1983';$fl=pow(2,13);
$_w=$_e=NULL;
$nbr=$killed=$cum=$sleep=0;$waitNextConnectionEach=1;
$socket = stream_socket_server($adr,$errno,$errstr,STREAM_SERVER_BIND);#
stream_set_blocking($socket, 0);
$read=$connections=[$socket];
$msg=[];

if (!$socket) {echo "Error : no socket : $errstr ($errno)<br />\n";} else {
    echo "\nServer listening on $adr.. $fl";
    while (!$killed) {
        $read = $connections;
        $mod_fd=stream_select($read, $_w, $_e, $waitNextConnectionEach);#socket_select vs stream_select
        if($_w){
            print_r($_w);}
        if($_e){
            print_r($_e);}## Jamais vus ..
        if ($mod_fd === FALSE) {break;}#0 if none pending => Loops
        if ($mod_fd === 0) {
            if($msg){
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
            }
            $cum=0;
        }

        for ($i = 0; $i < $mod_fd; ++$i) {
            if ($read[$i] === $socket) {#is the master socket
                while ($buf = stream_socket_recvfrom($socket, $fl, 0, $peer)) {#peer:
                    $len = strlen($buf);
                    $cum += $len;
                    $msg[$peer] .= $buf;
                    echo "\n$peer,line:$j;len:" . strlen($buf) . ',' . $cumulated;
                    $j++;
                }
            if ($msg[$peer]) {
                $a = 1;
                stream_socket_sendto($socket, '--dssd');
                socket_write($socket, '---ssdfssfs');
            }
            if ($i > 0) {
                fclose($read[$i]);
            }

                $a=1;
            } else {
                echo "\n - 2) " . date('H:i:s') . ' : ';
                #$tot.=$sock_data=socket_read($read[$i], $fl);
                while($buf=stream_socket_recvfrom($read[$i], $fl, 0, $peer)) {
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
