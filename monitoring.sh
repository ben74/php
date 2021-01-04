#!/bin/bash
#sudo chown netdesign:webadmins monitoring.sh;sudo chmod 644 monitoring.sh# <== Permissions so no shell_exec might rewrite this file !!
#a1=`date +%y%m%d`;b1=`date +%d%m%y`;
#todo:: extraction de variables passées en environnement : env
timeout_var=5;cpuidle_avg=$(timeout ${timeout_var} vmstat 1 > /tmp/checkcpu.tmp; awk '$15~/[0-9]/ {print $15}' /tmp/checkcpu.tmp | awk -v t=$timeout_var '{sum+=$1} END {print sum / t}');
apache80=`ss -ant | grep :80 | wc -l`;#current apache connections > seuil 40 : ddos ?
apache443=`ss -ant | grep :443 | wc -l`;#thalgo:55
pq=`postqueue -p | tail -n 1`;#postqueue
df2=`df --output=pcent /dev/sda2 | tail -n 1`;
topmem=`ps -eo pid,ppid,cmd,%mem,%cpu --sort=-%mem | head | tr '\n' '~'`;
topcpu=`ps -eo pid,ppid,cmd,%mem,%cpu --sort=-%cpu | head | tr '\n' '~'`;
freeMem=`cat /proc/meminfo | grep MemAvailable`;#centos
loadavg=`cat /proc/loadavg`;#aVg load -> si premier chiffre > 20 alerte
processlist=`mysql -e "show processlist" | tr '\n' '~'`;

#diskio=`iostat -x -p sda | tr '\n' '~' | tr '\(' ' ' | tr '\)' ' ' | tr '%' ' '`;# => to - leads to problems .. | tr ' ' '_'
#d1=`iostat -x -p sda | sed -e 's/[^a-zA-Z0-9 _\.% {2,}]/ /g' | tr '\n' '~'`;#json='[{"v":"'$d1'"}]';curl -sSLk -b "a1=$a1;b1=$b1" -d "$json" https://1.x24.fr/a/logCollector.php;
d1=`iostat -xy 1 1  | sed -n '8p' | sed -e 's/[^a-zA-Z0-9 _\.% {2,}]/ /g' | tr '\n' '~'`;#json='[{"v":"'$d1'"}]';curl -sSLk -b "a1=$a1;b1=$b1" -d "$json" https://1.x24.fr/a/logCollector.php;

json='[{"host":"pr","type":"metric","k":"apache80","v":"'$apache80'"},{"host":"pr","type":"metric","k":"apache443","v":"'$apache443'"},{"host":"pr","type":"metric","k":"cpuidle_avg","v":"'$cpuidle_avg'"},{"host":"pr","type":"metric","k":"postqueue","v":"'$pq'"},{"host":"pr","type":"metric","k":"dfsda2","v":"'$df2'"},{"host":"pr","type":"metric","k":"topmem","v":"'$topmem'"},{"host":"pr","type":"metric","k":"topcpu","v":"'$topcpu'"},{"host":"pr","type":"metric","k":"freeMem","v":"'$freeMem'"},{"host":"pr","type":"metric","k":"loadavg","v":"'$loadavg'"}]';#,{"host":"pr","type":"metric","k":"diskio","v":"'$d1'"}
#json='[]';
pk=$(echo -n $logCollectorSecret`date +$logCollectorSeed` | md5sum | awk '{print $1}');#echo $pk;
curl -sSLk -b "pk=$pk" -d "$json" $logCollectorUrl

#todo : pas certain du tout du résultat d1 de busyBox
