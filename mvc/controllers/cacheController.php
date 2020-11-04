<?php
#will wait n
class cacheController extends base{
#phpx cli.php cache 2
    static function Main($args=null){
        #$_ENV['noMemCache']#try with filecache only
        $k='k2';#.date('YmdHis');
        $set=cacheSet($k,3);#replace
        $ep=cacheGet('ep');
        $get=cacheGet($k);

        $_cachedValues=[];
        $set2=cacheSet($k,2);
        $get2=cacheGet($k);

        $_a=cacheList();#once listed with memcache, can't get values ..
        foreach($_a as $k2){
            $_cachedValues[$k2]=cacheGet($k2);
        }
        $get3=cacheGet($k);

        #$_cachedValues[$k]=cacheGet($k);
        print_r(compact('ep','set','set2','get','get2','get3','_cachedValues'));
    }
}
return;?>
