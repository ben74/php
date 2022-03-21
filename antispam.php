<?// should be called after a page which ideally sets the cookie, don't apply on regular page or the one the bots get on
$failThreshold=3;
$riddles=['which color is the white horse of Joseph Stalin ?'=>['white','blanc'],'how many fingers do you have in your hand ?'=>['five',5,'cinq']];

$d='ipFails';if(!is_dir($d))mkdir($d);$fincr=$d.'/'.$_SERVER['REMOTE_ADDR'].'.hits';
if(isset($_FILES) && $_FILES){
    if(strpos(json_encode($_FILES),'.php'))die('deny some extensions upload : remote shells');
}

//Has_algos(), xxh3, crc32c, crc32
$hash='crc32b';
$var=hash($hash,date('Ymd'));
if(!isset($_COOKIE[$var])){// Ensure client has cookies enabled
    setcookie($var,1);
    if(!is_file($fincr) or filemtime($fincr)< (time()-1800) )$x=0;else{$x=file_get_contents($fincr);}
    if($x>5){die('Please enable your cookies, too much connection errors, limitation restarts in 30 minutes');}
    file_put_contents($fincr,$x+1);r302('?#nsn:0');
    r302('?#nc');
}

if('leverage session start usage here : make sure client support session cookies for secure storage'){
    @session_start();
    if(!isset($_COOKIE[session_name()])){
        if(!is_file($fincr))$x=0;/*elseif(filemtime($fincr)<(time()-1800))$x=0;*/else{$x=file_get_contents($fincr);}
        if($x>5)die('Please enable your cookies, too much connection errors, limitation restarts in 30 minutes');
        file_put_contents($fincr,$x+1);r302('?#nsn:0');
    }
}

$val=hash($hash,$_SERVER['REMOTE_ADDR'].date('YmdH'));
$val2=hash($hash,$_SERVER['REMOTE_ADDR'].date('YmdH',strtotime('1 hour ago')));

if($_POST){
    if(!isset($_POST[$var])){$_SESSION['fails']+=10;r302('?#nt:0');}
    elseif(isset($_POST[$var]) && !in_array($_POST[$var],[$val,$val2])){die(json_encode($_POST));$_SESSION['fails']+=5;r302('?#nv:1');}
    if(!isset($_POST['login']) or !isset($_POST['pass']) or !$_POST['login'] or !$_POST['pass']){$_SESSION['fails']+=1;r302('?#empty');}
   
    if($_SESSION['fails']>$failThreshold){
        $ok=false;
        foreach($_POST as $k=>$v){
            if(strpos($k,':'.$var) and substr($k,0,1)=='r'){
                $rn=substr(str_replace(':'.$var,'',$k),1);
                $ak2=array_keys($riddles);
                if(in_array(trim(strtolower($v)),$riddles[$ak2[$rn]])){
                    $ok=true;
                }
            }
        }
        if(!$ok){
            $_SESSION['fails']+=2;
            r302('?#testFailed');
        }
    }

    if(isset($_POST['login']) && isset($_POST['pass']) and $_POST['login'] and $_POST['pass']){
        if(0 and 'fails'){// Test login here, please note to stripslashes a escape mysql arguments
            $_SESSION['fails']+=3;
            r302('?#badLoginOrPass');
        }else{// finally logged in
            $_SESSION['fails']=0;
            r302('?#ok');
        }
    }else{
        die(json_encode($_POST));
    }
}

if(!isset($_SESSION['fails']))$_SESSION['fails']=0;
?>
<form method=post action='?'><div id=form>
login : <input name=login><br>
pass : <input name=pass><br></div>
<input type=submit accesskey=s>
</form>
<script>document.querySelector('#form').innerHTML+="<input name='<?=$var?>' value='<?=$val?>' type='hidden'/>";
<?
if($_SESSION['fails']>$failThreshold){
    $rn=rand(0,count($riddles)-1);$ak=array_keys($riddles);?>
    document.querySelector('#form').innerHTML+="<?=$_SESSION['fails']?> : Proove you're not a bot : <br><?=$ak[$rn]?><input name='r<?=$rn.':'.$var?>' placeholder='your response'/>";
<?}?>
</script>
<?

function r302($a){header('Location:'.$a,1,302);die;}
return;?>
php -S 0.0.0.0:81#http://127.0.0.1:81/antispam.php

a) redirects to login.php if cookie not set
b) sets session