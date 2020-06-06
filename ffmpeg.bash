#Few ffmpeg usefull functions
mute=0
function screenrec() { fps=${1:-10};kf=${2:-$fps};offset_x=${3:-0};fn=${4:-s2};w=${5:-1920};#noAudioOutput=${6:-0};
vs=$w"x1080";dat;
echo "mute:$mute";
if [ "$mute" == "1" ]; then 
    echo "no audio output recorded";
    ffmpeg -hide_banner -loglevel error -y -f dshow -rtbufsize 100M -i audio="Réseau de microphones (Realtek High Definition Audio)" -offset_x $offset_x -offset_y 0  -f gdigrab -framerate $fps -video_size $vs -draw_mouse 1 -i desktop  -force_key_frames "expr:gte(t,n_forced*$kf)" $desk/$dat-mute-$fn.mp4;
#elif ;then    
else 
    echo "has audio output rec -- good for meetings"; 
    ffmpeg -hide_banner -loglevel error -y -f dshow -rtbufsize 100M -i audio="Mixage stéréo (Realtek High Definition Audio)" -f dshow -i audio="Réseau de microphones (Realtek High Definition Audio)" -offset_x $offset_x -offset_y 0  -f gdigrab -framerate $fps -video_size $vs -draw_mouse 1 -i desktop  -force_key_frames "expr:gte(t,n_forced*$kf)" -filter_complex "[0:a][1:a]amerge=inputs=2[a]" -map 2 -map "[a]" $desk/$dat-$fn.mp4;
fi;
    mute=0;
}

function tuto(){ mute=1;screen $1; }#record screen without realmix output ( music, or other people chatting in a confcall )
function screen(){ s=${1:-2};fps=${2:-10};kf=${3:-$fps};
    case $s in
        bf) battlefield;;
        1) screenrec $fps $kf -1920 s1;;#screen 1 is left
        12) screenrec $fps $kf -1920 s12 3840;;#1+2
        123) screenrec $fps $kf -1920 s123 5760;;#all screens
        2) screenrec $fps $kf 0 s2;;#middle
        23) screenrec $fps $kf 0 s23 3840;;
        3) screenrec $fps $kf 1920 s3;;#right
        *) echo "autres";;
    esac;
}

function battlefield() { fps=${1:-24};kf=${2:-240};dat;ffmpeg -hide_banner -loglevel error -y -f dshow -rtbufsize 100M -i audio="Mixage stéréo (Realtek High Definition Audio)" -f gdigrab -framerate $fps -i title="BF1942 (Ver: Tue, 09 Apr 2013 11:49:36)" -force_key_frames "expr:gte(t,n_forced*$kf)" $desk/$dat-battlefield.mp4; }; #records specific window by title

function normalize { z=${1:-};w=4096;h=2160;crf=24;fps=24;x=`ls`;for i in $x;do ffmpeg -n -i $i -c:v libx264 -r $fps -vf scale=$w:$h -crf $crf -x264-params keyint=48:scenecut=0 ../$z.$i.No.mp4;done;say "$z normalized";tone; }

function concatNoAudio { 
    cd $1/$2;printf "file '%s'\n" *.$3 > $2.list;  
    #echo "ffmpeg -f concat -i $2.list -c copy -an ../$2.$3;";return;
    ffmpeg -y -f concat -i "$2.list" -c copy -an -copytb 1 -err_detect ignore_err ../$2.$3;mv $2.list ../$2.list;cd ..;echo "$2 no audio";tone; 
}

function concatMp3 { concat . $1 $2 $2 mp3; }
function concat { 
  sortie=${4:-$3};
  audio=${5:-"-acodec copy"};
  if [ "$audio" == "mp3" ];then audio="-codec:a libmp3lame -q:a 2";fi;#-b:a 320k #is max #-qscale #9 is minimal quality
  cd $1/$2;printf "file '%s'\n" *.$3 > $2.list;
  cmd="ffmpeg -f concat -i $2.list -c copy $audio ../$2.$sortie";
  echo ">> $cmd";
  eval $cmd;
  rm $2.list;cd ..;tone;#echo "$2 with audio";
}
