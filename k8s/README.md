put files in volume, set volume into

k apply -f rainbowGracefulShutdown.yml

run either :
- php -r '$a=0;while(1){$a++;$a--;}' cpuMeltdown &
- php -r 'while(1){sleep(1);file_put_contents("a",1);}' diskUser &
- php -r 'while(1){sleep(1);file_get_contents("https://google.com",1);}' netWorkUser &

- or even a huge ffmpeg upscaling ..
  php -r '$f=fopen($argv[2],"w");$c=curl_init($argv[1]);curl_setopt($c,CURLOPT_FILE,$f);curl_exec($c);curl_close($c);fclose($f);' http://1.x24.fr/a/uploads/966.mp4 966.mp4
- fmpeg -y -i '966.mp4' -vf scale=80000:40000 -preset slow long.mp4 2>1 &>/dev/null & &

then 
k delete -f rainbowGracefulShutdown.yml

Pod won't be killed until each of those process is killed, as he expect to have 0 cpu usage, 0 disk usage, 0 network usage, and has no ffmpeg processes pending