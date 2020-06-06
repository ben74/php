<?php
class antiddosController extends base{}/* Simple LightWeight Anti Http DDos script : but, le placer en auto_prepend en cas de siège, ou en amont d'une réponse de page http standard, que ce soit 404 ou 200, les robots de scan de failles ayant la facheuse tendance à déclencher des 404 à la volée sur des chemins de vulnérabilités connues, un crawler, lui, va génèrer bcp plus de 404, et souvent adopter un user_agent étant l'imposture d'un moteur de recherche .. */
$questions2responses=[
    "de quelle couleur était le cheval blanc d'Henri 4 ?"=>'blanc',
    "combien font 2+3 ?"=>5,
    "combien font 2*3 ?"=>6,
    "quel est le nom de l'actuel président de la république ?"=>'macron',
    "en quelle année est décéde mickael jackson ?"=>'2009',
];

$ipf='z/ip/'.$_SERVER['REMOTE_ADDR'].'.json';#{'hits':{1597735832:3,1597735842:3}} 3 salves des hits toutes les 10 secondes
$seuilDeHits=2;
$eachNseconds=50;#fenêtre temporelle
$challengeExpirationTime=600;
$time=intval(time()/$eachNseconds);
if(isset($_GET['clearChallenge'])){@unlink($ipf.'.challengeOk'); }

if(isset($_POST['challenge']) && $_POST['challenge']){#0 == 'anyStringWtf?'
    $_POST['challenge']=strtolower(trim($_POST['challenge']));
    $f=$ipf.'.expectedResponse';
    if(is_file($f)){
        $r=file_get_contents($f);
        if($_POST['challenge'] && $_POST['challenge'] == $r){
            touch($ipf.'.challengeOk');
        }
    }
}

#compter globalement le nombre de hits, peut notamment être la clé d'un hashmap ou array sur redis .. memcached <== car chaque fichier va occuper 4k ( un bloc de disque dont la taille peut varier .. memcached parait le plus rapide )
$ipf='z/ip/'.$_SERVER['REMOTE_ADDR'].'.json';#{'hits':{1597735832:3,1597735842:3}} 3 salves des hits toutes les 10 secondes
$fmt=[];if(is_file($ipf))$fmt=json_decode(file_get_contents($ipf),1);
$fmt[$time]++;
if($fmt[$time] > $seuilDeHits){
    $lastChallengeOk=@filemtime($ipf.'.challengeOk');#if more than 10 minutes ...
    if($lastChallengeOk < time() - $challengeExpirationTime ){
        #session_start();#ou autre fichier temporaire
        $qnb=rand(0,count($questions2responses)-1);
        $q=array_keys($questions2responses)[$qnb];
        $r=$questions2responses[$q];
        file_put_contents($ipf.'.expectedResponse',$r);
        die("<fieldset><legend>Merci de prouver que vous n'êtes pas un robot ..</legend><form method=POST>".ucfirst($q)." : <input name=challenge><input type=submit></form></fieldset><link rel='stylesheet' href='/z/styles.css'/>");
    }
    /*check if your not a robot :: peut être une simple opération ou un ban définitif*/
}else{
    $b64=base64_encode(date('YmdH'));
    setCookie('yUi',$b64,intval(time()+3600),'/');#1er seuil légétimité :: les attaques basées curl se sont pas écrites à priori pour répéter les cookies
}
file_put_contents($ipf,json_encode($fmt));#de toutes façons

echo"<center>";
$f=$ipf.'.challengeOk';if(is_file($f)){echo "<hr><li>Challenge response ok : user is no more considerated as a bot .. <a href='?clearChallenge=1'>clear Challenge response</a><hr>";}
echo"<a href='?'>To be auto prepended in case of siege -- Please hit this page $seuilDeHits times within $eachNseconds seconds window to fire a challenge form confirming you're not an active HTTP DDOS bot, this challenge response will expire in $challengeExpirationTime seconds </a>";
echo"</center><link rel='stylesheet' href='/z/styles.css'/>";
/*
*/
return;?>
Anti Http Ddos Simple Strategy -- cas du siège ..
auto_prepend.php
Per Ip visit++ => in fast storage expires each hour
Count 200 regular page visits / minut

- A) Cloudflare
- B) Set cookie within any 200 response page ( b64 ( date(YmdH) ) )
- C) If absence kein de cookie on second hit or More 3 Pages per minut => please confirm your not a robot => and also flag the IP.file

Si heavy siège => Please confirm your not a robot at first
