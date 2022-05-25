<?php #aka :: frontController
spl_autoload_register('__autoload1');
function __autoload1($name){
    $name=str_replace('\\','/',$name);
    $tried=[];
    $paths=['mvc/controllers/','mvc/models/','mvc/views/'];#above autoload in common.php
    foreach($paths as $path){
        $tried[]=$f=$path.$name.'.php';
        if(is_file($f)){
            $_ENV['_autoloader1-404Router-Found'][$name]=$f;
            require_once $f;return 1;
        }
    }
    #echo $name."-\n";print_r($tried);
    $_ENV['_autoloader1-404Router-Notfound'][]=$name;
}

#cu https://php.home/z/tn-h100-b1.jpg
require_once 'app/common.php';
require_once 'app/functions.php';
$mediaExt=['ico','webp','png', 'jpg', 'gif', 'jpeg'];
$mediaJsCssExt=['css','jpg','pdf','ico','webp','png', 'jpg', 'gif', 'jpeg'];#and files so..
$imagesExt=['webp','png', 'jpg', 'gif', 'jpeg'];

if(strpos($uq, '/tn-') && in_array($ext, $imagesExt) && 'is thumbnail not calculated yet cause 404') {
    \Unicorn\thumbgen::i();#getinstance
    $h=$w=0;
    $o=$target=ltrim($uq, '/');
    $x = \Unicorn\thumb2params($o);
    extract($x);#_die(compact('x','o'));
#cuj https://php.home/z/tn-w200-h50-alpow.png.webp a '' 1
    $ext2='';
    $overext='webp';
    if(!is_file($filename) && strpos($filename,'.'.$overext)){
        $ext2=$overext;
        $f2=str_replace('.'.$ext2,'',$filename);
        if(!is_file($f2)){die('#nf:'.getcwd().'/'.$filename);}
        $filename=$f2;
    }
    #$target = thumbPath($filename,$w,$h,$ext2);
    $ok = \Unicorn\thumbgen::main(compact('filename', 'target', 'h', 'w','ext2'));
    if ($ok) {#generation stellaire, right away, right here, right now
        if(strpos($target,'.webp')){header('Content-Type: image/webp');}
        else header('Content-Type: image/jpeg');
        readfile($target);die;
        r302('/'.$target.'?generated=1#');die;
    }

#https://aws2.127.0.0.1.xip.io/a/tn/tn-_-w200_-h200-DSC00456.JPG?opc=1
#cu https://aws2.127.0.0.1.xip.io/a/tn/tn-_-w150-wilson_logo-50px.png
    die("#ng : $ok ".is_file($target)." => $target");
    #/a/tn/tn_-h300-DSC00456.JPG
    return compact('filename', 'w', 'h');
}

if(in_array(strtolower($ext), $mediaJsCssExt)){
    die("/*404*/");
}

$c=str_replace('.php','',ltrim($u,'/')).'Controller';
if(is_file('mvc/controllers/'.$c.'.php')){
    header("HTTP/1.1 200 OK",1,200);
    if(isset($argv)){
        return $c::main($argv);
    }
    return $c::main();
}

header("HTTP/1.0 404 Not Found",1,404);
### end of special things here :)
$title='404 - not found';
require_once'z/header.php';
?>
<fieldset><legend>404 - not found</legend>
    <a href="/#">Sorry - back to home ?</a>
</fieldset>
<?

return;
