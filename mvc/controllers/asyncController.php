<?php
#will wait n
class asyncController extends base{
#phpx cli.php async 2
    static function Main($args=null){
        #$_ENV['noMemCache']=1;#works with simple files too
        if(!$args){
die('<pre>use php cli.php
phpx cli.php async 2
#or for windows : target your executable path first and change $phpExecPath
c:/prog/xamp7/php74/php C:/Users/ben/home/phpgit/cli.php async 2

then look at /z/shm/wait-var-ep-0.log for the real time spent by sub-process to complete its delegated action which should be close of 3000 milliseconds ( max sleep time of background processes )
');
        }

        $_cachedVars=[];
        $background=' > /dev/null 2>&1 &';$phpExecPath='php';$pwd=$_SERVER['PWD'];
        if(isset($_SERVER['WINDIR']) or isset($_SERVER['windir'])){#windows special commands
            $background=' > NUL 2> NUL';
            $phpExecPath='start /B c:/prog/xamp7/php74/php';
        }
        print_r($args);
        $start=microtime(1);

        $ck='ep';
        $set=cacheSet($ck,3);
        if(!$set)die('error setting cache variable : '.$ck);
        echo "set:".$set;
        echo "\nget:".cacheGet($ck);
#apcu_store('expectedAsyncProcessesToComplete',2);
        $s=$phpExecPath.' '.$pwd.'/cli.php wait var '.$ck.' 0'.$background;echo"\n".$s;shExec($s);#this one will trigger the job at the end
        $s=$phpExecPath.' '.$pwd.'/cli.php wait touch temoin.log'.$background;echo"\n".$s;shExec($s);
        $s=$phpExecPath.' '.$pwd.'/cli.php wait sleep 1'.$background;echo"\n".$s;shExec($s);
        $s=$phpExecPath.' '.$pwd.'/cli.php wait msleep 3000'.$background;echo"\n".$s;shExec($s);
        $s=$phpExecPath.' '.$pwd.'/cli.php wait usleep 3200000'.$background;echo"\n".$s;shExec($s);
        /*
        $_cachedVars=[];$cacheList=apcu_cache_info()['cache_list'];
        foreach($cacheList as $t)$_cachedVars[$t['info']]=apcu_fetch($t['info']);
        */
        $ep=cacheGet($ck);
        $timeTaken=microtime(1)-$start;
        $times=cacheGet('times');
        $_cachedVars=cacheList();
        echo"\n\n";print_r(compact('ep','timeTaken','times','_cachedVars'));
#apcu_fetch('expectedAsyncProcessesToComplete')
        $a=1;
    }
}
return;?>
Postulat : l'async n'existe pas en php à moins de forker les process, ou de créer des workers managés par un orchestrateur ( how much ? en fonction de l'usage CPU / Ram / Io : rien ne sert d'en rajouter lorsque toutes les ressources sont monopolisées ) et de les alimenter via une queue .. le but étant de ne former aucun goulot d'étranglement ..

La plupart des programmes se disent async lorsque le traitement de données / queues sont segmentées, ce qui constitue parfois un avantage : ex : lire 8ko d'une connexion A, la passer dans le fichier B, lire 8k de B, le passer dans A et rebolote en flushant la ram / ou pas .. du moins cela peut soulager certaines connexions et ne pas passer l'ensemble de ces tampons en RAM lorsqu'il faut la conserver

La fonction php qui s'en approche le plus naturellement est curl_multi_exec .. Le gain de temps "asynchrone" et constitué sur le temps d'attente des données, le premier arrivé sera le premier servi, en revanche les traitement à réception des données, eux, ne sont pas asynchrons d'où l'intérêt de dispatcher à des process en second plan si l'ensemble peut s'avérer bloquant pour le drainage de la queue ( le constat et simple 30 requêtes 'simultanées' ne sont pas 30 fois plus rapides lorsque lancées en même temps , le gain de perf est plus proche de 7, et visiblement réalisé sur le temps d'attente + réception des données )

---

apcu in cli mode is useless, memory is trashed as each process is over .. might only be ok for neverending processes
needs apc.enable_cli=on
considering apcu (shm:32mo) is same as memcached, and even the same as writing a file to /dev/shm/serializedVariable.igb (igb is faster and zips )
