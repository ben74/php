<? #simple lightweight crashproof bus message queuying system, todo : use mysql or redis or clustered files if the users.json becomes big at a time
#todo : deletion de messages upon $_POST['del'] ou cleanup by expiration ( file not modified since 14 days .. )
$a = $_SERVER;
$mirrorTimeout = 1;#attention ne doit pas créer un timeout pour le put initial
if ($_POST) {
    $d='messages/';
    $users = json_decode(file_get_contents('users.json'), 1);
    $user = $a['HTTP_USER'];
    $pass = $a['HTTP_SECRET'];#sha1 ?
    if(!isset($users[$user]))ko('!user');
    if($users[$user]['p'] != $pass)ko('!pass');
    $f=explode(',',$users[$user]['f']);
    $p=$_POST;
    if(!isset($p['folder']))ko('!folder');
    if(!in_array($p['folder'],$f))ko('!folderPerm');
    if (!is_dir($d.$p['folder']))ko('!folderNotExists');
    if(isset($p['content']) and 'writes'){
        $content=$p['content'];
        if(isset($p['callback'])){#and function_exists($p['callback']) ou autoloading action alors passer le message en transformation dans cette fonctino
            if(!function_exists($p['callback']))ko('!callback:'.$p['callback']);
            $content=$p['callback']($content);
        }
        $ok=file_put_contents($d.$p['folder'].'/'.$p['msgid'].'.msg',$content,8);#appends
        $mirrorsOk = 0;
        if(isset($p['mirror'])){
            $mirrors=json_decode($p['mirror'],1);
            if(!$mirrors)ko('!mirrors');
            require_once '../app/fun.php';
            foreach ($mirrors as $mirror) {
                #Attention au timeout ici !!! & aux empilements de xdebug
                $mirrorReturn = curl($mirror, [], ['mirrored'=>1]+$p, ['user: autreUser', 'secret: AutrePass', 'Cookie: XDEBUG_SESSION=1'], $mirrorTimeout);
                if ($mirrorReturn['info']['http_code'] == 200) {
                    $mirrorsOk++;
                } else {#todo:use this for re-queing at least 3 times
                    #for re-queying 3 retries
                    file_put_contents('logs/mirror-errors.log', "\n" . $mirror . "\t" . json_encode($p) . "\t" . $mirrorReturn['error'], 8);
                }
                #echo $mirrorReturn['content'];
                $a = 1;
            }
            if ($mirrorsOk) {
                $ok .= ',mirrorsok:' . $mirrorsOk;
            }
        }
        ok('ok:'.$ok);
    }elseif($p['msgid'] and 'reads'){
        $f=$d.$p['folder'].'/'.$p['msgid'].'.msg';
        if(!is_file($f))_error('!file');
        $starts=0;$len=filesize($f);header('filesize: '.$len);
        if(isset($p['starts']))$starts=$p['starts'];
        if(isset($p['len']))$len=$p['len'];#not += boljemoï !
        $c=file_get_contents($f,FALSE,null,$starts,$len);
        if($starts and $len){
            $a=1;
        }
        ok($c);
    }elseif('return folder index'){
        chdir($d . $p['folder']);
        $x=glob('*');
        foreach($x as &$v)$v=str_replace('.msg','',$v);unset($v);
        ok(implode(',',$x));
    } else{
        $a=1;
        ko('!what??');
    }
}
ko('!np');
function hyphenWrapper($x){return"--$x--";}
function ok($x){die($x);}
function ko($x){header('HTTP/1.0 404 Not Found', 1, 404);die($x);}
return; ?>

users.json:


-- todo : handle TCP connexions via sockets like ZMQ

storage could be redis, memcached, mysql, sqlite, /dev/shm .. but here we choose the main disk so the system handles its available ram caching the most asked disk files blocks .. resulting in same performances .. so this system is crashproof ( either served via restarting php -S or apache or nginx, which shall be restarted in case of OOM error :)

user + pass stored into json file
- users and folders permissions : for read/write
- creates new folder/msgId
- read folder/msgId ( option is startoffset / end offset )
- returns folder/msg Index
