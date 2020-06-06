<?
$title='Racetrack :: php samples and quick demos';
require_once 'z/header.php';
?>    
	<fieldset><legend><h1>&lcub;&lcub; Racetrack :: php samples and quick demos &rcub;&rcub;</h1></legend>
<li><a href='Fastbus'>Fastbus</a>
<?
$d='mvc/controllers/';$ext='.php';
$y=glob($d.'*'.$ext);
$x=glob('*.php');
foreach($y as $v){
    $x[]=str_replace([$d,'Controller',$ext],'',$v);
}
arsort($x);
foreach($x as $v){
    if(in_array($v,explode(',','_notes.php,shell.php,menu.php,index.php,404.php,index')))Continue;
    if(preg_match('~client|server~i',$v))Continue;
    $v2=str_replace('.php','',$v);
    echo"<li><a href='$v'>$v2</a>";
}
?>
<li><a href="securitytest/injectionStatsFileUpload.php">Security tests : injection by Page Url, Query String, User Agent, Remote Host Referer, Cookie</a></li>
	</fieldset>
<hr><center>    
<pre> &copy; <a target=1  href='//alpow.fr#o:stackiml' title='alpow'>Alpow <?=date('Y')?></a></pre></center>
</body>
</html>
