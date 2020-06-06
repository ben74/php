<?
class phpSendCurlToServerController extends base{}
#phpx /home/cli.php phpSendCurlToServer 0
$nb=intval($GLOBALS['argv'][1]);
$put=$nb;#implode(',',$GLOBALS['argv']);
#$put='{"raw postdata to body":1}';#implode(',',$GLOBALS['argv']);
$mtu=pow(2,16);
if($nb%100==0){
    $put='';
    while($tot<20000000){
        $re="\n".$i.'-';$i++;
        $put.=$re.str_repeat('a',$mtu-strlen($re));
        $tot+=$mtu;
    }
    #$put.="\n".str_repeat('a',$totlen);#21 200 000 = 21Mo / 42 ==> 500ko each
    #$put.=';'.str_repeat('a',16000);#21 200 000 = 21Mo / 42 ==> 500ko each
}

$a=cup('http://127.0.0.1:1983',[],$put,['Cookie: XDEBUG_SESSION=1;']);
$_a=$a["contents"];
_die($_a);
