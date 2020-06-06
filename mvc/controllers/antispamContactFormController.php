<?php
class antispamContactFormController{static function main(){}}
/*
0) Honeypots
1) CSRF token
2) Simple Question
3) Image Captcha !
*/
session_start();
require_once 'app/common.php';#has outputs ..
$questions2responses=["de quelle couleur était le cheval blanc d'Henri 4 ?"=>'blanc',"combien font 2+3 ?"=>5,"combien font 2*3 ?"=>6,"quel est le nom de l'actuel président de la république ?"=>'macron',"en quelle année est décéde mickael jackson ?"=>'2009',"qui dirigait l'union soviétique de 1922 à 1953"=>'joseph staline'];
if($_POST){
    $honeypots=explode(';','city;mail;adress;zip');
    foreach($honeypots as $v){if(isset($_POST[$v]) and $_POST[$v]){_die('!honeypot:'.$v);}}
    if(empty($_SESSION['token']))_die('!notoken in session -- wtf ??');
    if($_SESSION['token'] != $_POST['token'])_die('!bad:token');
    if(strtolower(trim($_POST['reponse'])) != strtolower($_SESSION['reponse']))_die('!bad reply');
    echo"ok-passed !<pre>";print_r($_POST);
}

if('Q&R,CSRF'){
    $rd=rand(0,count($questions2responses)-1);
    $question=array_keys($questions2responses)[$rd];$reponse=$questions2responses[$question];$_SESSION['reponse']=$reponse;
    if(!$_SESSION['token']){$_SESSION['token']=bin2hex(random_bytes(32));}$token=$_SESSION['token'];
}
$title='dynamic thumbnails';
require_once 'z/header.php';
?><form method=post><input name=token type=hidden value='<?=$_SESSION['token']?>'>
<fieldset><legend>Honeypot fields : dont fill them, make them unvisible if possible</legend>
city:<input name=city> - mail:<input name=mail> - adress :<input name=adress> - zip :<input name=zip>
</fieldset>
<br>Whatever : <input name=whatever>
<br>Please reply this question : <?=$question?><input name=reponse placeholder="<?=$reponse?>">
<br><input type=submit>
</form>
<?_die();return;?>
