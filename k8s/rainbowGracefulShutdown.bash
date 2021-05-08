#!/bin/bash
#exited with 137 message --> augmenter :: terminationGracePeriodSeconds: 99999
#debug=1;
#defaults
  exclude='rainbowPreStop';
  nbKilledProcesses=0;
  interval=0;
  nbProc=0;
  cpuUsage=0;
  sum1=0;
  sum2=0;
  net1=0;
  net2=0;

usage() { echo "$0 usage:" && grep " .)\ #" $0; exit 0; }

options=$(getopt -l "h,debug::,interval::,sighup::,command::,maxCpuUsage::,maxDiskUsage::,maxNetUsage::,countNbProc::" -o "hdb:i:s:c:u:d:n:p:" -a -- "$@")
eval set -- "$options"

while true; do
  case $1 in
  -h|--help)
      usage
      exit 0
      ;;
  -i|--interval) # --interval 10 #retests conditions each n seconds.
      shift
      export interval=$1
      ;;
  -s|--sighup) # --sighup "^php artisan lapin" # send sighup to this regex.
      shift
      export sighup="$1"
      #set -xv  # Set xtrace and verbose mode.
      ;;
  -c|--command) # --command "infomaniak-docker-php-entrypoint php artisan node:down;" # exec this command anyways.
      shift
      export command="$1"
      ;;
  -u|--maxCpuUsage) # --maxCpuUsage 1 #stays up while cpu usage above this limit.
      shift
      export maxCpuUsage=$1
      ;;
  -d|--maxDiskUsage) # --maxDiskUsage 1 #stays up while disk usage above this limit.
      shift
      export maxDiskUsage=$1
      ;;
  -n|--maxNetUsage) # --maxNetUsage 1 #stays up while net usage above this limit.
      shift
      export maxNetUsage=$1
      ;;
  -p|--countNbProc) # --countNbProc 'ffmpeg|ffprobe' #stays up while these processes Regex exists.
      shift
      export countNbProc=$1
      ;;
  -db|--debug) # --debug 1 #debug on.
      shift
      export debug=1
      ;;
  --) #not recognized ?
      shift
      break;;
  esac
  shift
done

if [ ! -z $debug ]; then
  echo "arguments list : countNbProc : $countNbProc, interval : $interval, maxNetUsage : $maxNetUsage,maxDiskUsage : $maxDiskUsage,maxCpuUsage : $maxCpuUsage, sighup : $sighup, command : $command";
fi;

#/bin/bash /var/www/html/scripts/rainbowPreStop.sh -interval 10 -maxCpuUsage 1 -maxDiskUsage 1 -maxNetUsage 1 -countNbProc 'ffmpeg|ffprobe' -sighup "^php artisan ben sleepForever" -command "infomaniak-docker-php-entrypoint php artisan node:down;"
# éviter les interactions sur les pods colloqués ..
# garde fou de respawn si le sighup ne passe pas pour je ne sais quelle raison -- attenti : le SIGHUP ne doit pas interrompre le job en cours .. voir du côté async si cela passe peut servir si cela tente encore de respawner
  u=`uname -n`;
  te=/tmp/$u.terminated;
  touch $te;

# par défaut les process recoivent par défaut un sigterm si non handlé la fonction de gestion devant retourner : return; si l'on souhaite que le main process continue ..
# nb : cela ne passe évidément pas par le shutdown quand le process est finalement killé
# supervisorctl signal HUP all;
# Je n'ai pas reçu les signaux via supervisord même avec stopsignal=HUP

  if [ ! -z "$sighup" ]; then pkill -HUP -f "$sighup";  fi;
  if [ ! -z "$command" ]; then `$command`; fi; #too many arguments

# Somme usage cpu + cast to int

  if [ ! -z $maxCpuUsage ]; then cpuUsage=`top -b -n 1 | awk 'NR>7 { sum += $9; } END { print sum; }'`;  cpuUsage=${cpuUsage%.*}; fi;
  if [ ! -z "$countNbProc" ]; then
    nbProc=`ps -ax | grep -iE "$countNbProc" | grep -v 'grep -iE' | grep -v $exclude | wc -l`;
  fi;
#Somme utilisation IO et network : permet de qualifier à 100% qu'il ne se passe rien !!!!

  if [ $interval -gt 0 ]; then
    if [ -v maxDiskUsage ]; then sum1=`cat /proc/[0-9]*/io | grep -E 'read_bytes|write_bytes' | grep -v cancelled | cut -d" " -f 2 | awk '{ sum += $1; } END { print sum; }'`; fi;
    if [ -v maxNetUsage ]; then net1=`cat /proc/[0-9]*/net/netstat | grep "IpExt: 0" | cut -d" " -f 8,9 | awk '{ sum += $1;sum += $2; } END { print sum; }'`; fi;
    sleep $interval
    if [ -v maxDiskUsage ]; then sum2=`cat /proc/[0-9]*/io | grep -E 'read_bytes|write_bytes' | grep -v cancelled | cut -d" " -f 2 | awk '{ sum2 += $1; } END { print sum2; }'`; fi;
    if [ -v maxNetUsage ]; then net2=`cat /proc/[0-9]*/net/netstat | grep "IpExt: 0" | cut -d" " -f 8,9 | awk '{ sum += $1;sum += $2; } END { print sum; }'`; fi;
  fi;

((dDisk = sum2 - sum1))
((dNet = net2 - net1))
if [ $dDisk -lt 0 ]; then $nbKilledProcesses=1;fi;
if [ $dNet -lt 0 ]; then $nbKilledProcesses=1;fi;

if [ ! -z $debug ]; then
  if [ -v maxDiskUsage ]; then echo "maxDiskUsage::$maxDiskUsage;"; fi;
  echo "Debug::$countNbProc,$interval,$nbProc,$dDisk,$sNet";
fi;

# tant que 1 process ou que le processeur est utilisé à plus de 1%
# le process ffmpeg peut parfois n'utiliser quedalle de cpu lorsqu'il parcours le fichier ..
  while [ $nbKilledProcesses -gt 0 ] || [ $dDisk -gt $maxDiskUsage ] || [ $dNet -gt $maxNetUsage ] || [ $nbProc -gt 0 ] || [ $cpuUsage -gt $maxCpuUsage ] ; do
    if [ $interval -gt 0 ]; then
      if [ -v maxDiskUsage ]; then sum1=`cat /proc/[0-9]*/io | grep -E 'read_bytes|write_bytes' | grep -v cancelled | cut -d" " -f 2 | awk '{ sum += $1; } END { print sum; }'`; fi;
      if [ -v maxNetUsage ]; then net1=`cat /proc/[0-9]*/net/netstat | grep "IpExt: 0" | cut -d" " -f 8,9 | awk '{ sum += $1;sum += $2; } END { print sum; }'`; fi;
      sleep interval;
      if [ -v maxDiskUsage ]; then sum2=`cat /proc/[0-9]*/io | grep -E 'read_bytes|write_bytes' | grep -v cancelled | cut -d" " -f 2 | awk '{ sum2 += $1; } END { print sum2; }'`; fi;
      if [ -v maxNetUsage ]; then net2=`cat /proc/[0-9]*/net/netstat | grep "IpExt: 0" | cut -d" " -f 8,9 | awk '{ sum += $1;sum += $2; } END { print sum; }'`; fi;
    fi;

    ((dDisk = sum2 - sum1))
    ((dNet = net2 - net1))
    if [ $dDisk -lt 0 ]; then $nbKilledProcesses=1;fi;
    if [ $dNet -lt 0 ]; then $nbKilledProcesses=1;fi;

    if [ ! -z $maxCpuUsage ]; then cpuUsage=`top -b -n 1 | awk 'NR>7 { sum += $9; } END { print sum; }'`;  cpuUsage=${cpuUsage%.*}; fi;
    if [ ! -z "$countNbProc" ]; then nbProc=`ps -ax | grep -iE "$countNbProc" | grep -v 'grep -iE' | grep -v $exclude | wc -l`;  fi;

  done;

if [ ! -z $debug ]; then
  echo "$countNbProc,$interval,$nbProc,$dDisk,$dNet";
fi;
exit 0
# then SIGHUP to process 1, then waits $TERMINATIONGRACEPERIOD, then all processes killed
