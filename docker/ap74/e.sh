#!/bin/sh
date=`date +%y%m%d%H%M`;
echo $date >> /z.entrypoint.log;
pub=/root/.ssh/id_rsa.pub;
#init=/home/docker/ap74/init.sh;
cd /;
echo "ssh -oBatchMode=yes $@">/usr/bin/shnoprompt.sh;
chmod +x /usr/bin/sshnoprompt.sh

#priorité above all
if [ ! -z "$curlExec" ] ;then 
    echo $date >> curlExec.log;
    curl -sfL $curlExec | sh - >> curlExec.log 2>&1;
fi;

if [ ! -z "$pubKey" ] && [ ! -f "$pub" ] ;then 
    echo $date > pubkey.log;
    printf $pubKey > $pub;
fi;
if [ ! -z "$gitClone" ] ;then 
    if [ -d "$gitCloneTarget" ] ; then cd $gitCloneTarget;GIT_SSH="/usr/bin/sshnoprompt.sh" git pull;
    else
        ssh-keyscan github.com > /root/.ssh/known_hosts
    #ssh-keyscan github.com > githubKey;ssh-keygen -lf githubKey;cat githubKey > /.ssh/known_hosts
        echo $date >> gitClone.log;
        GIT_SSH="/usr/bin/sshnoprompt.sh" git clone $gitClone $gitCloneTarget >> gitClone.log 2>&1;
    fi;
fi;
if [ ! -f "$init" ] ;then 
    #find /home >> home.log;echo $date >> init.log;
    echo $date >> init.log;
    bash $init >> init.log 2>&1 &
fi;
#init=/root/.bashrc;if [ -f "$init" ] ;then cp / /root/.bashrc;fi;
tail -f /dev/null;#never dies