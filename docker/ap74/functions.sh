#/usr/sbin/vsftpd 2>/dev/null & 
#DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )";
function reload() { . $BASH_SOURCE; }; function re() { reload; };

lp=/z/logs/${HOSTNAME};
bg='1>/dev/null 2>&1 &';
#machine=`cat /whoami`;
machine=php74;whoami=php74;export machine=php74;export whoami=php74;
export PS1='$(whoami)@$(pwd)> ';
f="/home2/conf/$machine.sh";if [ -f $f ]; then . $f;fi;#ne pas dépendre de WINSCP lorsque l'on déconnecte le serveur sftp !!!

#x=/rsync.lock;lockf $x;rm $x;
function lockf() {
  if [ -f "$1" ] ;then
    filemtime=`stat -c %Y $1`;
    currtime=`date +%s`;
    diff=$(($currtime-$filemtime));#securite si lock et bash plante
    if [ $diff -gt 3600 ];then echo "rmlock older than 3600";rm $1;#60 mins max lock -- why ???
    else echo "locked";return;fi;
  fi;
  touch $1;
}

#f='/variables.sh';if [ -f $f ]; then . $f;fi;#or as a RKV Json Storage into /z/$machine/
#echo ". /home2/conf/gfunctions.sh;echo 'welcome "$machine"';" >> /root/.bashrc;  
function restarer() { off;on; };
  #pkill php-fpm;php-fpm > $lp/php.log 2>&1 &ls 

function lastmod() { a=${1:-5};
    find / -not -path '/proc/*' -not -path '/sys/*' -type f -mmin -$a;
}
#launch the home sync at first usage
#fn *sendmail*
function fn() { #find shorthand
  #c=`pwd`;cd /;cd $c;
  find . -not -path '/all/*' -not -path '/home2/*' -not -path '/var/*' -not -path '/tmp/*' -not -path '/z/*' -not -path '/proc/*' -not -path '/sys/*' -type f -iname "$1";
};

function search() { query=$1;ext=${2:-};dir=${3:-.};flags=${4:-ruIli};
#find -not -path '/home/*' -not -path '/var/*' -not -path '/tmp/*' -not -path '/z/*' -type f -name ''
    output=`fn "$PWD//$query"`'.search';
    #strpos $ext '*.' || ext="*.$ext";# && echo $ext;    
    if [ $ext ]; then 
        case $ext in *\**)echo 'ya-wildcarded';;esac;#*)ext="*.$ext";;
        ext="--include=$ext";
    else ext="--include={*.xml,*.phtml,*.php,*.py,*.js,*.css,*.mjs}";fi;
    echo "grep -$flags $ext --exclude-dir={/z,/proc,/sys} \"$query\" $dir";
    grep -$flags $ext  "$query" $dir | tee /search.$output;
    #grep -rIli --exclude={/all,/bin,/dev,/etc,/lib,/media,/mnt,/mysql,/proc,/root,/run,/sbin,/srv,/sys,/tmp,/usr,/z,/var} --include={*.php} 'benjamin fontaine'
};


#if [ -z "$functionsLoaded" ];then echo "welcome back brother ben";functionsLoaded=1;synchome;fi;
#PHP_IDE_CONFIG="serverName=php.home";XDEBUG_CONFIG="idekey=ECLIPSE";export $PHP_IDE_CONFIG;export $XDEBUG_CONFIG;
function phpx() { php -dxdebug.remote_autostart=1 $@; }

#XDEBUG_CONFIG="remote_host=192.168.1.99"
function on() { phpr;mysqlon;aon; }
#php-fpm 2>/dev/null &
#/usr/sbin/nginx -g "daemon off;" 2>/dev/null & 
#service nginx start

function off() { pkill rsync;phpoff;aoff;mysqloff;   }

function phpoff() { pkill php-fpm; }
function phpr() { phpoff;phpon; };  # >> $lp/php-fpm.log    }


function aoff(){ pkill httpd;x=/var/run/apache2/httpd.pid;if [ -f "$x" ];then rm $x;fi; }
function ar(){ aoff;aon; }
function httpr() { ar; }
function restart(){ off;on; }

function dump() { mysqlon;c;dat;name=${1-};f=/home2/$name.$date.dump.sql;mysqlon;mysqldump -u a -pb $name > $f;gzip $f;d; }    #gzip in background .. 


function mysqlback() { name=${1-};mysqloff;while pgrep mysqld_safe; do printf "..";sleep 1;done;cd /z/mysql;dattar cfz mysqlback.$name.$date.sql.tgz 1;mysqlon; }
function dat() { date=`date +%y%m%d%H%M`; }
alias back=mysqlback;


function mysqloff() { pkill mysqld;rm /z/mysql/1/*.err > /dev/null 2>&1;rm /z/mysql/1/*.pid > /dev/null 2>&1; }
function mysqlon() { x=`pgrep mysqld_safe`;if [ ! "$x" ];then /usr/bin/mysqld_safe --datadir='/z/mysql/1' >> $lp/mysql.log 2>&1 & fi; }

function phpon() { $(php-fpm 2>/dev/null &); }
function aon(){ $(/usr/sbin/httpd >> $lp/httpd.log 2>&1 &); }