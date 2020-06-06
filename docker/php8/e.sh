#!/bin/sh
#date=`date +%y%m%d%H%M`;
#echo $date > /z.entrypoint.log;
init=/home/docker/php8/init.sh;if [ -f "$init" ] ;then 
    sh $init;
fi;
#init=/root/.bashrc;if [ -f "$init" ] ;then cp / /root/.bashrc;fi;
machine=php80;whoami=php80;export machine=php80;export whoami=php80;
tail -f /dev/null;#never dies