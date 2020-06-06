#!/bin/sh
#date=`date +%y%m%d%H%M`;
#echo $date > /z.entrypoint.log;
init=/home/docker/init.sh;if [ -f "$init" ] ;then 
    sh $init;
fi;
#init=/root/.bashrc;if [ -f "$init" ] ;then cp / /root/.bashrc;fi;
machine=php74;whoami=php74;export machine=php74;export whoami=php74;
tail -f /dev/null;#never dies