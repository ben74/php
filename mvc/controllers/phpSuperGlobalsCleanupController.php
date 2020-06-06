<?php
class phpSuperGlobalsCleanupController extends base{}
/*
>> Never trust any user input !!
But => échapper en premier lieu les quotes pouvant mener à des injections sql, puis remplacer les variables plus spécifiquement, quite à placer ce fichier en auto_prepend_file afin de sécuriser l'ensemble d'une application Legacy
a:qques regex permettant de valider certaines variables selon leurs spécificités
*/

$regex = [
    'domain' => '~[^a-z0-9\-\.]~i','letnum' => '~[^a-z0-9]~i','letnums' => '~[^a-z0-9 ]~i',
    'spe1' => '~=|\(|\)|[\'"]~', 'spe2' => '~=|\(|\)|[\'"]|[\/\\\\]~', 'mail' => '~[^a-z0-9@\.\-_]~i', '!letnu.m' => '~[^\p{L}0-9 ]~i', 'letters' => '~[^\p{L} ]~i'];
$regexReplace=[
    'City'=>$regex['letnum'],
    'HTTP_HOST'=>$regex['domain'],
    'mail'=>$regex['mail'],
];#per variable name : city might be : NewYork2 but not New"_'!/%*$York_4
$_ENV['esc']=[['"',"'"],['”','`']];#`,'`' are latin 1 ready :)
#$_ENV['esc']=[['"',"'"],['”','΄']];#utf8 :: swap double quote and quote per second and prime
function __str($x){if(is_array($x)){foreach($x as $k=>&$v){$v=__str($v);}unset($v);return $x;}
    if(strpos($x,$_ENV['esc'][0][0])===FALSE and strpos($x,$_ENV['esc'][0][1])===FALSE)return $x;
    return str_replace($_ENV['esc'][0],$_ENV['esc'][1],$x);
} function __unstr($x){if(is_array($x)){foreach($x as $k=>&$v){$v=__unstr($v);}unset($v);return $x;}if(strpos($x,$_ENV['esc'][1][0])===FALSE and strpos($x,$_ENV['esc'][1][1])===FALSE)return $x;return str_replace($_ENV['esc'][1],$_ENV['esc'][0],$x);}#kit replace

require_once 'z/header.php';

$_ENV=$changed=[];
$_FILES=['multipleContainingPhpSHell'=>['name'=>[0=>'ZzIMqQ.php'],'type'=>[0=>''],'tmp_name'=>[0=>'/tmp/phpt2CljR'],'error'=>[0=>0],'size'=>[0=>49261]],'simpleJpg'=>['name'=>'123.jpg','type'=>'','tmp_name'=>'/tmp/jpg','error'=>0,'size'=>49261]];
#$_FILES=['simple'=>['name'=>'ZzIMqQ.php','type'=>'','tmp_name'=>'/tmp/phpt2CljR','error'=>0,'size'=>49261]];
$_GET=['City'=>'New"_\'!/%*$York_4'];
$_POST=['mail'=>'userEmail@gmail.com#Contains doublequotes" and quote \''];
$_COOKIE=['userName'=>'Contains doublequotes" and quote \''];
$_SERVER=['HTTP_HOST'=>"' UNION ALL() like an sql injection for multisite --",'USER_AGENT'=>"' UNION ALL() like an sql injection for multisite --"];

if(1 and 'main'){
    $superGlobals=['SERVER'=>$_SERVER,'ENV'=>$_ENV,'FILES'=>$_FILES,'GET'=>&$_GET,'REQUEST'=>&$_REQUEST,'POST'=>&$_POST,'COOKIE'=>&$_COOKIE];#passage en référence sinon inutile    
    echo'Input data<textarea>';print_r(array_filter($superGlobals));echo'</textarea>';

    foreach($superGlobals as $_k=>&$_superGlobal){
        if($_k=='FILES'){#trim all possible references containing an php upload
            $phpFoundWithinArray=recursive_array_search('.php',$_superGlobal,'like');
            echo"Keys leading to found .php upload : ".implode(',',$phpFoundWithinArray)." , length of uploads then :";
            unset($_superGlobal[$phpFoundWithinArray[0]]);
            $_superGlobal=array_filter($_superGlobal);
            #echo"found php upload within \$_FILES array : ".$phpFoundWithinArray;#die;
            #echo"<br>unset(\$_FILES$phpFoundWithinArray);";
            #eval("unset(\$_FILES$phpFoundWithinArray);");            
            print_r($_superGlobal);
            $changed['FILES']=$_superGlobal;
            #$_superGlobal=null;
        }
        foreach($_superGlobal as $_k2=>&$_c){
            $_b=$_c;$_c=__str($_c);
            if($_c!=$_b){
                $changed[$_k][$_k2]=$_c;
            }
        }unset($_c);
    }unset($_superGlobal);

    foreach($regexReplace as $_k=>$r){
        foreach($superGlobals as $_k2=>&$_superGlobal) {
            if (isset($_superGlobal[$_k]) and preg_match($r, $_superGlobal[$_k])) {
                $_b=$_superGlobal[$_k];$_c=$_superGlobal[$_k] = preg_replace($r, '', $_superGlobal[$_k]);
                $changed[$_k2][$_k]=$_c;
            }
        }unset($_superGlobal);#toujours penser à unsetter ses passages en référence
    }   
    echo'<hr>Cleaned Up :: what has changed ? <textarea style=height:50vh>';print_r($changed);echo'</textarea><style>textarea{font-size:1.4rem}</style>';
}
return;

#todo:parcours objet non recursif avec spl_object_hash et reconstruction des méthodes d'accès "propres" $obj->getUser()-> ...
function recursive_array_search($needle, $haystack,$mode=0,$currentKey='',$lv=0,$keys=[]) {
    foreach($haystack as $key=>$value) {
        $kk=is_numeric($key)?$currentKey.'['.$key.']':$currentKey.'["'.$key.'"]';
        if (is_array($value)) {
            $nextKey = recursive_array_search($needle,$value,$mode,$kk,$lv+1,array_merge($keys,[$key]));
            if ($nextKey){
                return $nextKey;#finally, when found
            }
        }
        elseif($mode=='like' and stripos($value,$needle)!==FALSE)return array_merge($keys,[$key]);
        else if($value==$needle)return array_merge($keys,[$key]);
    }
    return false;
}
function recursive_array_search1($needle, $haystack,$mode=0,$currentKey='',$lv=0,$keys=[]) {
    foreach($haystack as $key=>$value) {
        $kk=is_numeric($key)?$currentKey.'['.$key.']':$currentKey.'["'.$key.'"]';
        if (is_array($value)) {
            $nextKey = recursive_array_search($needle,$value,$mode,$kk,$lv+1,$keys);
            if ($nextKey)return $nextKey;
        }
        elseif($mode=='like' and stripos($value,$needle)!==FALSE)return $kk;
        else if($value==$needle)return $kk;
    }
    return false;
}
