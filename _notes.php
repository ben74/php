<?die;#notes using syntax color & so ..
"C:\prog\memcached\memcached.exe";
c:/prog/xamp7/php74/php -dxdebug.remote_autostart=1 C:/Users/ben/home/phpgit/cli.php async 2

php74 -dxdebug.remote_autostart=1 C:/Users/ben/home/phpgit/cli.php async 2

php74 -i | grep apc

phpgit;log phpgit_php74;
cd /home/mvc/controllers;php -S 127.0.0.1:1983 phpServerController.php;#easier 20Mo postdata => took : 45ms
php /home/cli.php streamServer;
php /home/cli.php streamClient 0;#20m =>Took:388ms

fastbus/messages/20201018165410-127.0.0.146154.msg
len:9308160

x0=str.trim().replace(/\s+/gi,' ').replace(/> </gi,'><');
x1=b64EncodeUnicode(x0);
sc('clickAndCollect',x1);

#python comment is weird in php8 ..
>> php #[ interprêté       as     #[ExampleAttribute]
fig up -d $vm;log $vm

#addgroup ftpusers;
user=ben;password=ploplo
mkdir -p /home/ftp/$user
adduser -D -h /home/ftp/$user -s /bin/false $user 21
echo -e "$password\n$password\n" | passwd $user


echo ben:ploplo | chpasswd
pkill vstfpd;/usr/sbin/vsftpd 2>/dev/null &

stop $vm;docker container rm $vm;docker image rm $vm;fig up -d $vm;log $vm


docker inspect
/opt/bin/entry_point.sh
log

docker machine no space left on device
docker run -d -p 4444:4444 -v /dev/shm:/dev/shm selenium/standalone-chrome:4.0.0-alpha-7-prerelease-20201009

docker network create 1
docker volume rm $(docker volume ls -qf dangling=true);#remove non used docker volumes
docker system prune
docker-machine ssh 1
df -h# then


pskill java.exe;/d/@sym/_Java/jdk-11.0.1/bin/java.exe -Dwebdriver.chrome.driver=chromedriver-v86.exe -jar selenium.jar &
bash behat-portable.sh
Element not found with xpath, //html (WebDriver\Exception\NoSuchElement)

https://github.com/docksal/behat/issues/7
#---
docker exec -it phpgit_php74 "/bin/bash  /home/behat/behat-portable.sh docker" --rm
log phpgit_php74;bash /home/behat/behat-portable.sh docker

fig up -d log phpgit_php74;bash /home/behat/behat-portable.sh docker
