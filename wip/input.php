<?php
/*
#£:todo: callback sur la réponse sur d'autres scripts ou socket fire and forget reply .. ? N'a aucun utilité, autant passer un timeout au niveau de ce script sans callback, ce dernier va simplement mourrir au terme d'un timeout
pskill php
cd C:\Users\ben\home\phpgit\wip
pskill php;phpx $web/phpgit/wip/input.php 1
START /B php -ddisplay_errors=1 -dxdebug.remote_autostart=1 input.php {\"42\":1,\"2\":1,\"41\":1,\"52\":1,\"19913\":1,\"58\":1,\"81\":0,\"64\":0,\"13\":30,\"78\":30,\"47\":true,\"10002\":\"https:\/\/c\/postreflector.php\",\"10015\":\"coucou%5Ba%5D%5Bb%5D=2\"} >yo.log 2>err.log
*/
$___a=$argv;
if(count($argv)<2)die('#<2argv');
$argv[1]=trim($argv[1],"'");#preservation json windows
$a=1;
function success($contents,$info,$header,$error){global $f;if($info['http_code']==200){
    echo"success:";
    unlink($f);}
}
chdir(__DIR__);$isJson=0;
if('0: lancer les envois en background'){
    #unset PHPRC;phpx $web/phpgit/wip/input.php 1
    if(isset($argv) and isset($argv[1])){#sends it !
        if($argv[1]=='out'){die('#nah'.file_get_contents('php://stdin'));}#might
        if(substr($argv[1],0,1)=='{'){$isJson=1;
            #die($argv[1]);#START /B php input.php {"yolo":1} >ya.log 2>err.log
        }else{
            $data=http_build_query(['coucou'=>['a'=>['b'=>2]]]);
            $__url='https://c/postreflector.php';
            $o=[CURLOPT_HEADER=>1,CURLINFO_HEADER_OUT=>1,CURLOPT_VERBOSE=>1,CURLOPT_FOLLOWLOCATION=>1,CURLOPT_RETURNTRANSFER=>1,CURLOPT_AUTOREFERER=>1,CURLOPT_SSL_VERIFYHOST=>0,CURLOPT_SSL_VERIFYPEER=>0,CURLOPT_TIMEOUT=>30 ,CURLOPT_CONNECTTIMEOUT=>30,CURLOPT_POST=>true,CURLOPT_URL=>$__url,CURLOPT_POSTFIELDS=>$data];#CURLOPT_USERPWD
            $j=json_encode($o);

            $pre=$end='';$winOrPipe='|';


            $phpe='C:/prog/xamp7/php72x64/php -dxdebug.remote_autostart=1';
            $phps='C:/Users/ben/home/phpgit/wip/input.php > C:/Users/ben/home/phpgit/wip/'.time().'.log';
            if(isset($_SERVER['WINDIR'])){
                $pre="START /B ";
                $phpe='php ';
                $phps='C:/Users/ben/home/phpgit/wip/input.php';                
                $end=" >".time().".log 2>err.log";
            }else{
                $end=' &';
            }
            $j=str_replace('"','\"',$j);#à cause du bash
        $cmd=$pre.$phpe.' '.$phps.' '.$j.' '.$end;
        #$cmd=$pre.'echo '.$j.' '.$winOrPipe.' '.$phpe.' '.$phps.$end;
        print_r(exec($cmd));
        die("\nlauncher over");
        }#end not json payload
    }
}
#echo "{"42":1,"2":1,"41":1,"52":1,"19913":1,"58":1,"81":0,"64":0,"13":30,"78":30,"47":true,"10002":"https:\/\/c\/postreflector.php","10015":{"coucou":{"a":{"b":2}}}}" | php -dxdebug.remote_autostart=1 C:/Users/ben/home/phpgit/wip/input.php
$pid=getmypid();
#$inputJSON = trim(file_get_contents('php://input'));#plain post body
#echo phpversion();print_r(get_loaded_extensions());
if($isJson){
    $x=$argv[1];
}else{
    $x='erreur';
    #$x=file_get_contents('php://stdin');
}
$f='payloads/'.$pid.'-'.time().'.json';
$f=$pid.'-'.time().'.json';
file_put_contents($f,$x);
$j=json_decode($x,1);
if(!$j)die('#bad json payload:'.$x);
/*
todo:#£:+Option :: {{ Fire And Forget Request -- don't give a heck for the response .. }}
 */
$__a=cuo($j);
extract($__a);
success($contents,$info,$header,$error);
if(md5($__a["contents"])=='5906996c2b81b90b2721628f17705a09'){
    $ok='valid';
}
#echo"\n".memory_get_usage(1);
die("\n\nstdin:".$x);#echo "plain text for async stuff" | phpx $web/phpgit/wip/input.php

function cuo($opts){
    $c = curl_init();curl_setopt_array($c, $opts); $result = \curl_exec($c);$info = \curl_getinfo($c);$error = \curl_error($c);\curl_close($c);$header = substr($result, 0, $info['header_size']);$contents = trim(substr($result, $info['header_size']));
    return compact('contents','info','header','error');#$a=1;
}
return;?>

export PHPRC=/c/Users/ben/home/phpCliMiniCurl.ini
php -i | grep parsed;
php -i | grep -e "Loaded Configuration File" -e "Scan this dir for additional .ini files" -e "Additional .ini files parsed"
