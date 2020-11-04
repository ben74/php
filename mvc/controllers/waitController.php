<?php
class waitController extends base{
#phpx cli.php async 2
    static function Main($args=null){
        #$_ENV['noMemCache']=1;#works with simple files too
        $debug='';
        if(!$args){
            die('<pre>use php cli.php
php cli.php wait touch file.log');
        }
        if(0 and 'apcutest'){
            $ok[]=apcu_add('f1',__line__);$b1=__line__;#doesn't rewrite
            $ok[]=apcu_add('f1',__line__);
            $ok[]=apcu_store('foo',__line__);
            $ok[]=apcu_store('foo', __line__);$a1=__line__;
            $ok[]=apcu_store('f1', __line__);$b1=__line__;#allows rewrite above add
            $a=apcu_fetch('foo');if($a != $a1)die('#'.__line__);#rewritess
            $b=apcu_fetch('f1');if($b != $b1)die('#'.__line__);#writes once
        }
        $started=microtime(1);
        $glued=implode(' ',$args);
        $f=preg_replace('~[^a-z0-9]~is','-',$glued).'.log';
#START /B php74 C:/Users/ben/home/phpgit/cli.php wait touch temoin.txt
        if($args[1]=='touch') {
            touch($args[2]);die;
#php cli.php wait var ep 0
        }elseif($args[1]=='var'){
            $sleepCycles=0;$sleepTime=200000;
            while(cacheGet($args[2]) != $args[3]){
                $sleepCycles++;usleep($sleepTime);#µs => 200ms wait
            }
            $ms=($sleepCycles * $sleepTime)/1000;
            $took=(microtime(1)-$started)*1000;
            $debug="\n".date('Y/m/d H:i:s')."\n$ms ms sleeping ( $took ms)";
#php cli.php wait sleep 20;#seconds
        } elseif($args[1]=='sleep'){
            sleep(intval($args[2]));
        } elseif($args[1]=='msleep'){
            usleep(intval($args[2]*1000));
        } elseif($args[1]=='usleep'){
            usleep(intval($args[2]));
        }else{
            file_put_contents($f.'.warning','noaction !!!');die;#
        }

        $x=cacheGet('ep');
        file_put_contents($f,$x.$debug);#

        $x--;
        cacheSet('ep',$x);

        $took=microtime(1)-$started;

        $times=cacheGet('times');if(!$times)$times=[];
        $times[$glued]=$took;
        cacheSet('times',$times);

        if($x==0){#could also launch action from these :)

        }
        #phpx cli.php wait sleep
        #
    }
}
return;?>

