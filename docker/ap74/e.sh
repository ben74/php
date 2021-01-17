#!/bin/sh
date=`date +%y%m%d%H%M`;
echo $date >> /z.entrypoint.log;
pub=/root/.ssh/id_rsa.pub;
#init=/home/docker/ap74/init.sh;
cd /;
#Pseudo-terminal will not be allocated because stdin is not a terminal.
echo "ssh -oBatchMode=yes $@">/usr/bin/sshnoprompt.sh;
chmod +x /usr/bin/sshnoprompt.sh

#priorité above all
if [ ! -z "$curlExec" ] ;then 
    echo $date >> zz_curlExec.log;
    curl -sfL $curlExec | sh - >> zz_curlExec.log 2>&1;
fi;

if [ ! -z "$pubKey" ] && [ ! -f "$pub" ] ;then 
    echo $date > zz_pubkey.log;
    printf $pubKey > $pub;
fi;
if [ ! -z "$gitClone" ] ;then 
    echo $date >> zz_gitClone.log;
    if [ -d "$gitCloneTarget" ] ; then cd $gitCloneTarget;
    #GIT_SSH="/usr/bin/sshnoprompt.sh" git pull;
        git pull >> zz_gitClone.log;
    else
        ssh-keyscan github.com > /root/.ssh/known_hosts
        ssh-keyscan bitbucket.org >> /root/.ssh/known_hosts
    #ssh-keyscan github.com > githubKey;ssh-keygen -lf githubKey;cat githubKey > /.ssh/known_hosts        
        #GIT_SSH="/usr/bin/sshnoprompt.sh" 
        git clone $gitClone $gitCloneTarget >> zz_gitClone.log 2>&1;
    fi;
fi;
if [ -f "$igniter" ] ;then 
    #find /home >> home.log;echo $date >> init.log;
    echo $date >> zz_init.log;
    bash $igniter >> zz_init.log 2>&1 &
fi;
#init=/root/.bashrc;if [ -f "$init" ] ;then cp / /root/.bashrc;fi;
tail -f /dev/null;#never dies