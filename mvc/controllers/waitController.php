<?php
#phpx cli.php async 2
class waitController extends cli{
    static function Main($args=null){
        #$_ENV['noMemCache']=1;#works with simple files too
        $debug='';
        if(!$args){
            die('<pre>use php cli.php
php cli.php wait touch file.log');
        }

        $started=microtime(1);
        $glued=implode(' ',$args);
        $f=preg_replace('~[^a-z0-9]~is','-',$glued).'.log';
#START /B php74 C:/Users/ben/home/phpgit/cli.php wait touch temoin.txt
        $i=0;while($args){${'a'.$i}=array_shift($args);$i++;}
        if($a1=='touch') {
            touch($a2);die;
#php cli.php wait var ep 0 200000 waitCallback waitCallbackMethod arg1 arg2
        }elseif($a1=='var'){
            #$ob=base::i(['cachedValueRemainingProcesses'=>cacheGet($a2)]);
            $ob=base::set(['a'=>date('YmdHis'),'cachedValueRemainingProcesses'=>cacheGet($a2)]);
            #$ob=base::set('cachedValueRemainingProcesses'=>cacheGet($a2));
            $sleepCycles=0;$sleepTime=intval($a4);
            $cpuBurn=2000;
            if($sleepTime<$cpuBurn)$sleepTime=$cpuBurn;
            while(cacheGet($a2) != $a3){
                $sleepCycles++;usleep($sleepTime);#µs => 200ms wait
            }
            $ms=($sleepCycles * $sleepTime)/1000;
            $started=microtime(1);
            if(isset($a5) and isset($a6)){#has Callback with arguments
                $callbackArgs=[];
                $i=7;while(isset(${'a'.$i})){$callbackArgs[]=${'a'.$i};$i++;}
                $ret=call_user_func_array([$a5,$a6],$callbackArgs);
            }
            #waitCallback waitCallbackMethod arg1 arg2
            $took=(microtime(1)-$started)*1000;
            $debug="\n".date('Y/m/d H:i:s')."\n$ms ms sleeping ( action callback : $took ms)";
#php cli.php wait sleep 20;#seconds
        } elseif($a1=='sleep'){
            sleep(intval($a2));
        } elseif($a1=='msleep'){
            usleep(intval($a2*1000));
        } elseif($a1=='usleep'){
            usleep(intval($a2));
        }else{
            file_put_contents($f.'.warning','noaction !!!');die;#
        }

        $x=cacheGet('ep');
        file_put_contents($f,$x.$debug);#
        $x--;#aboutissant à la décrementation de cette variable todo : cacheDec, cacheInc(rement) variable
        cacheSet('ep',$x);

        $took=microtime(1)-$started;

        $times=cacheGet('times');if(!$times)$times=[];
        $times[$glued]=$took;
        cacheSet('times',$times);
/* l'action peut être également déclenchée par l'un des "sleeping process" */
        if($x==0){#could also launch action from these :)

        }
        #phpx cli.php wait sleep
    }
}
return;?>

if(0 and 'apcutest'){
$ok[]=apcu_add('f1',__line__);$b1=__line__;#doesn't rewrite
$ok[]=apcu_add('f1',__line__);
$ok[]=apcu_store('foo',__line__);
$ok[]=apcu_store('foo', __line__);$a1=__line__;
$ok[]=apcu_store('f1', __line__);$b1=__line__;#allows rewrite above add
$a=apcu_fetch('foo');if($a != $a1)die('#'.__line__);#rewritess
$b=apcu_fetch('f1');if($b != $b1)die('#'.__line__);#writes once
}
