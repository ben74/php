<?php
/*
needs your youtube.com_cookies.txt to be exported from your nav once cliqued approval on graphic content such as : https://www.youtube.com/watch?v=Mb3_R__r7Go

desk;i=0;nbProcesses=6; while [ $i -lt $nbProcesses ]; do i=$((i+1));php yt.php yt.list $i $nbProcesses & done;
*/

array_shift($argv);
$list=array_shift($argv);
$nb=array_shift($argv);
$tot=array_shift($argv);

$x=explode("\n",trim(file_get_contents($list)));
foreach($x as $k=>$v){
    if($k%$tot==$nb){
        echo"\nDownloading .. $v";
        shell_exec("youtube-dl --cookies youtube.com_cookies.txt ".trim($v));
    }
}