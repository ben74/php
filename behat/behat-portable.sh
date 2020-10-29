#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";t=$DIR;cd $DIR;#position it to be sure
#0) cd "C:\Users\ben\home\phpgit\behat\behat-portable";
#For windows :: find the right chromedriver version here :: https://chromedriver.chromium.org/downloads
#1) start chromedriver : pskill java.exe;/d/@sym/_Java/jdk-11.0.1/bin/java.exe -Dwebdriver.chrome.driver=chromedriver-v86.exe -jar selenium.jar &
#2) sh behat-portable.sh

#c
#Acceptance tests != 
#https://www.seleniumhq.org/download/#thirdPartyDrivers
#-server-standalone-3.141.59
#cd ~/home/behat-portable;pskill java.exe
#  
#   java -Dwebdriver.gecko.driver=D:\Behat\geckodriver.exe -jar selenium-server-standalone-3.4.0.jar;#firefox
#   run selenium & chrome either on localhost or docker
#   bash behat-portable.sh win|docker 2 3;

#Could be run directlyt from localhost, huh ?
#windows php imagemagick dll => https://mlocati.github.io/articles/php-windows-imagick.html
#https://michaelheap.com/behat-selenium2-webdriver/
#everything;fig up -d e3;log e3;#selenium on
phpe='/c/prog/xamp7/php71/php';#php executable with imagick installed
cd=`pwd`;bd=$(dirname $0);cd $bd;date=`date +%y%m%d%H%M`;sn='home';

config='localhost';#target docker host or localhost ?

for i in "$@";do n=$((n+1));eval "a$n=$i''";echo "$n:$i";if [ "$i" == "--xdebug" ]; then xd=$xdebug;fi;done;#echo "a$n=$i"; #set -- "$a1" "$a2" "$a3" "$a4";

if [ "$a1" = "win" ]; then 
  xd='-ddisplay_errors=1 -dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=127.0.0.1';#xdebug.remote_connect_back=1 xdebug.idekey=E530
fi;
if [ "$a1" = "docker" ]; then #bash behat-portable.sh docker
    phpe='/usr/local/bin/php';#php executable with imagick installed
    xd='-ddisplay_errors=1';xdebug='-dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=192.168.99.1';
    echo " using >> docker conf"
    config='docker';
fi;

#echo $xd;
export PHP_IDE_CONFIG="serverName=$sn";
#using behat lang tag to pass the additional configuration file uses behat.yml by default
echo "$phpe $xd behat.php -f pretty benz --tags benz --lang='$config.json' --config $config.yml;# -vvv";
$phpe $xd behat.php -f pretty benz --tags benz --lang="$config.json" --config $config.yml;# -vvv
exit;
exit;






#cd ~/home/behat-portable;export PHP_IDE_CONFIG="serverName=home";php -dxdebug.remote_enable=1 -dxdebug.remote_mode=req -dxdebug.remote_port=9000 -dxdebug.remote_host=127.0.0.1 vendor/behat/behat/bin/behat -f pretty --tags "benz" benz --lang='file.json';

#imagick setup - if not present, works for docker environments
#x=$(php -m | grep imagick);if [ -z "$x" ]; then echo "imagick setup .. sit down and enjoy, as this might take 3 minutes ..";apt-get install -y wget htop libmagickwand-dev imagemagick;printf "\n" | pecl install imagick;fi;
