DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )";
. $DIR/functions.sh
#touch /init.log
#. /home/docker/ap74/init.sh
if [ ! -d /x ]; then mkdir /x;fi;
f='/x/0.installed';if [ ! -f "$f" ]; then    
    touch $f;cd /;mkdir /backup;#les setups de base, 
    ln -s /home/docker/ap74/.bashrc /root/.bashrc;
    rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    ln -s /home/docker/ap74/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini;#8 has none
#backup base configurations     :: tar -tvf backup.tgz
#tar -zxvf backup.tgz usr/local/etc/php/
    tar czf backup.tgz /usr/local/etc/php-fpm.d/ /etc/apache2 /usr/local/etc/php /etc/my.cnf.d/mariadb-server.cnf /etc/ssh/sshd_config /etc/postfix/main.cf;
    cp /etc/my.cnf.d/mariadb-server.cnf /backup/maria.cnf;cp /etc/ssh/sshd_config /backup/sshd_config;
    #rm /etc/ssh/sshd_config;cp /backup/sshd_config /etc/ssh/sshd_config
#Is Main Volume Created ?
fi;



#f='/x/1.rsync';if [ ! -f "$f" ]; then ;fi;
#rm -rf /z/conf/*;rm /x/moveConfigurationFilesToVolume.1;. /home/docker/ap74/init.sh
f='/x/moveConfigurationFilesToVolume.1';if [ ! -f "$f" ]; then    
    d=/z/logs/${HOSTNAME}/;if [ ! -d "$d" ]; then mkdir -p $d;fi;
    d=/z/logs/${HOSTNAME}/mailsent/;if [ ! -d "$d" ]; then mkdir -p $d;fi;
    d=/z/conf;if [ ! -d "$d" ]; then mkdir $d;fi;
    #d=/z/apache2;if [ ! -d "$d" ]; then mkdir $d;fi;
    d=/z/mysql;if [ ! -d "$d" ]; then mkdir $d;fi;
    d=/z/sessions;if [ ! -d "$d" ]; then mkdir $d;chmod 777 -R /z/sessions/;fi;
    d=/z/home;if [ ! -d "$d" ]; then mkdir $d;fi;
#get confs from main folder    
    rsync -avuz --inplace /home/docker/ap74/conf /z/   
    #move folder s=base;z=target/;tar -tvf apache2.tgz;rm -rf /z/conf;tar xf apache2.tgz
    z2=apache2;s=/etc/;z=/z/conf/;  if [ ! -d "$z$z2" ]; then mv $s$z2 $z;echo "mv $s$z2 => $z$z2";fi;if [ ! -L "$s$z2" ]; then echo "$z$z2 => $s$z2";rm -rf $s$z2;ln -s $z$z2/ $s$z2;fi;
#tar -zxvf backup.tgz usr/local/etc/
    z2=php-fpm.d;s=/usr/local/etc/;z=/z/conf/;  if [ ! -d "$z$z2" ]; then mv $s$z2 $z;fi;if [ ! -L "$s$z2" ]; then  echo "$z$z2 => $s$z2";rm -rf $s$z2;ln -s $z$z2/ $s$z2;fi;       
    z2=php;s=/usr/local/etc/;z=/z/conf/;  if [ ! -d "$z$z2" ]; then mv $s$z2 $z;fi;if [ ! -L "$s" ]; then  echo "$z$z2 => $s";rm -rf $s$z2;ln -s $z$z2/ $s$z2;fi; 
#PerFILE ::    
        s=/etc/postfix/main.cf;z=/z/conf/postfix.cnf;   if [ ! -f "$z" ]; then mv $s $z;fi;if [ ! -L "$s" ]; then  echo "$z => $s";rm $s;ln -s $z $s;fi;   
        #cat /usr/local/etc/php-fpm.d/www.conf
        #rm -rf /etc/my.cnf.d/mariadb-server.cnf /z/conf/mysql.cnf;cp /backup/maria.cnf /etc/my.cnf.d/mariadb-server.cnf;
        s=/etc/my.cnf.d/mariadb-server.cnf;z=/z/conf/mysql.cnf;   
         if [ -f "$z" ]; then rm $s;cp $z $s;fi;#remove original configuration, 
        #if [ ! -f "$z" ]; then mv $s $z;fi;if [ ! -L "$s" ]; then  echo "$z => $s";rm $s;ln -s $z $s;fi;chown mysql:mysql /etc/my.cnf.d/mariadb-server.cnf /z/conf/mysql.cnf
        
        
        #rm /etc/ssh/sshd_config;cp /backup/sshd_config /etc/ssh/sshd_config
        s=/etc/ssh/sshd_config;z=/z/conf/sshd.config;   if [ ! -f "$z" ]; then mv $s $z;fi;if [ ! -L "$s" ]; then  echo "$z => $s";rm $s;ln -s $z $s;fi;
        s=/usr/local/etc/php-fpm.conf;z=/z/conf/phpfpm.conf;   if [ ! -f "$z" ]; then mv $s $z;fi;if [ ! -L "$s" ]; then  echo "$z => $s";rm $s;ln -s $z $s;fi;
        
        #s=/etc/ssmtp/ssmtp.conf;z=/z/conf/stmp.conf;   if [ ! -f "$z" ]; then mv $s $z;fi;if [ ! -L "$s" ]; then  echo "$z => $s";rm $s;ln -s $z $s;fi;chmod 640 /etc/ssmtp/ssmtp.conf;chown root:mail /etc/ssmtp/ssmtp.conf
        ##PubkeyAuthentication yes#.ssh/authorized_keys    
    #HOSTNAME
        
    echo 'root:toor' | chpasswd
#/etc/init.d/sshd start    
    sed -i 's/#Port 22/Port 1983/' /etc/ssh/sshd_config    
    #sed -i 's/UsePAM yes/#UsePAM no/' /etc/ssh/sshd_config;#not recognized
    #sed -i 's/#UsePAM no/UsePAM yes/' /etc/ssh/sshd_config;#not recognized
    sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
    sed -i 's/#SyslogFacility AUTH/SyslogFacility AUTH/' /etc/ssh/sshd_config
    sed -i 's/#LogLevel INFO/LogLevel INFO/' /etc/ssh/sshd_config
    touch /run/openrc/softlevel
    rc-update add sshd
    rc-update add postfix
    rc-status    
#sed -i 's/#AddressFamily any/AddressFamily any/' /etc/ssh/sshd_config
#sed -i 's/#ListenAddress 0.0.0.0/ListenAddress 0.0.0.0/' /etc/ssh/sshd_config
#/etc/init.d/sshd restart
#/etc/init.d/sshd start
#sshd: no hostkeys available -- exiting.
    touch $f;   
    chmod 777 -R /z/logs/${HOSTNAME}/
    chmod 777 -R /var/log/    
fi;

#rm /x/mysqlsetup.1;. /home/docker/ap74/init.sh
f='/x/mysqlsetup.1';if [ ! -f "$f" ]; then #mysql et volume
  touch $f;
  s=/var/lib/mysql;z=/z/mysql/1/;
  if [ -L "$s" ] ; then echo "link $s";fi;
  if [ -d "$z" ] ; then echo "is folder $z";fi;
  if [ ! -L "$s" ] && [ -d "$z" ]; then 
    echo "then linkit";
    ln -s $z $s;#just link it another time :) la base étant déjà crée
  elif [ ! -d "$z" ]; then #no sql present dans volume cible
    echo '1st run :: symlink mysql folder to volume, then populate database, create user and zip it !:)';
    mkdir -p $z;mv $s $z;ln -s $z $s;
  #run & install mysql
    /usr/bin/mysqld_safe --datadir='/z/mysql/1' > /logs/mysql.log 2>&1 &
    mysql_install_db --user=mysql --basedir=/usr --datadir=/z/mysql/1;
    h='%';mysql -e "CREATE USER 'a'@'$h' IDENTIFIED BY 'b';GRANT ALL PRIVILEGES ON *.* TO 'a'@'$h'";
    h='localhost';mysql -e "CREATE USER 'a'@'$h' IDENTIFIED BY 'b';GRANT ALL PRIVILEGES ON *.* TO 'a'@'$h'";
    h='127.0.0.1';mysql -e "CREATE USER 'a'@'$h' IDENTIFIED BY 'b';GRANT ALL PRIVILEGES ON *.* TO 'a'@'$h'";
    mysql -e "FLUSH PRIVILEGES";
    cd /z;tar cfz mysql-just-installed.tgz mysql/1;
  fi;#move and symlink the sql data to the main volume
fi;

if [ "cheese" == "burgerface" ]; then
    exec /usr/bin/mysqld_safe > /dev/null 2>&1 &
fi;

#anyways then
#touch /run/openrc/softlevel
/etc/init.d/sshd start
/etc/init.d/postfix start
on;#put all services online once started :)