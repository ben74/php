#!/bin/sh
#date=`date +%y%m%d%H%M`;
#echo $date > /z.entrypoint.log;
init=/home/docker/ap74/init.sh;if [ -f "$init" ] ;then 
    #touch /entry.temoin
    sh $init;
fi;
#init=/root/.bashrc;if [ -f "$init" ] ;then cp / /root/.bashrc;fi;
tail -f /dev/null;#never dies