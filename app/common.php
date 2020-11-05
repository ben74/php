<?php
/**
 * autoprepend.php
 * Goal : security check foreach incoming request for insecure or hacked websites
 * Might give you an hint for logging where the infected / modified files are standing at ..
 * ( runkit : rewriting : eval, base64_decode, shell_exec etc .. )
 */
$a = __namespace__;
$phpv = phpversion();
$mainHost = getConf('mainHost');


define('XON',isset($_COOKIE['XDEBUG_SESSION']) ? 1 : 0);

set_error_handler('myError');#/*['class','function'] ini_set('log_errors',0);*/
set_exception_handler('myException'); #restore_error_handler();
spl_autoload_register('__autoload2');
//spl_autoload_register('autoloader');
register_shutdown_function(
    function () {
        $breakpoint = 1;#what you like at shutdown : is last_error() an 500 ? in order to display custom message ?
    }
);

$_ENV['u'] = '';
$_ENV['dr'] = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/';

if (isset($argv)) {#cli
    $f = array_shift($argv);
    $_SERVER['REQUEST_URI'] = '/' . $argv[0];#then arguments..
}
#cuj https://php.home/antiddos?qs=1 0 '' 1;
if ($_SERVER['REQUEST_URI']) {
    $_ENV['u'] = $_SERVER['REQUEST_URI'];
    $qs='';
    $u=$uq = $_SERVER['REQUEST_URI'];
    $ext = explode('.', $_SERVER['REQUEST_URI']);
    $ext = end($ext);
#cuj https://php.home/antiddos? 0 0 1
    if (strpos($ext, '?')) {
        $x = explode('?', $ext);
        $ext = array_shift($x);
        $qs=implode('?',$x);
    }
    $ext=strtolower($ext);
    $u=str_replace('?'.$qs,'',$uq);
}

if ($_POST && 'check for long base64 payloads with no spaces ( might be a search string also ) && nor _ga, nor PHPSESSID cookie') {
    $acunetixAndSqlInjectionsScanValues = explode(';', '-1 OR 2+78-;78-1=0+0+0+1 --;555-666-0606;Acunetix;@email.tst');
    foreach ($_POST as $k => $v) {
        foreach ($acunetixAndSqlInjectionsScanValues as $needle) {
            if (is_string($v) and stripos($v, $needle) !== false) {
                dbM([$_ENV['u'] . ' ; ' . $needle], 'acunetix', $_ENV['lp'] . 'acunetix.log');
                r404('#c:'.__line__);
            }
        }
    }
    if (count($_POST) == 1 && !isset($_POST['AccessPaymentCode']) && strlen(end($_POST)) > 40 && preg_match('~^[a-zA-Z0-9\/\r\n+]*$~', end($_POST))) {
        dbM($_ENV['u'] . ' :: ' . end($_POST), 'b64 backdoor  post is', $_ENV['lp'] . 'secu.log');#allow AccessPaymentCode
        r404('#c:'.__line__);
    }
}

/** visiteur naturel a cookies _ga, PHPSESSID */
if (count($_COOKIE) == 1 && strlen(end($_COOKIE)) > 50 && preg_match('~^[a-zA-Z0-9\/\r\n+]*$~', end($_COOKIE))) {
    dbM($_ENV['u'] . ' :: ' . end($_COOKIE), 'b64 backdoor cookie is', $_ENV['lp'] . 'secu.log');
    die;
}

#Do not check security if Admin loggedIn ( might be ckeditor html stuff posted in )
if ($_ENV['noSecurity']) {
    ;
} elseif (stripos($_ENV['u'], 'admin/') !== false && isset($_COOKIE[session_name()]) && isset($_COOKIE[$adminLogged])) {
    $adminLoggedIn = 'no firewall then';
} elseif (1 && strpos($_ENV['u'], 'admin/') !== false && isset($_COOKIE[session_name()])) {
    $adminLoggedIn = 'neither';
} elseif ('regular php firewall') {
    if ('injection pattern along rewriting') {
        $x = injectionPattern($_ENV['u']);#check the uri along with query string .. avoiding injection via rewriting within where like requests ..
        if ($x) {
            dbm('injection pattern ' . $x . ' in ' . $_ENV['u'], 'injection', 'secu.log');
            r404('#c:'.__line__);#404 reply is the best thing to discourage any further bot attempts
        }
    }

    $rawBody = file_get_contents('php://input');
    if ($rawBody) {
        $x = injectionPattern($rawBody);#check the uri alondg with query string
        if ($x) {
            dbm('injection pattern ' . $x . ' inrawBody ' . $rawBody, 'secu.log');
            r404('#c:'.__line__);
        }
        #$stdIn = stream_get_contents(STDIN);
    }

    if ($_REQUEST && 'query string parameters goes here ..') {
        foreach ($_REQUEST as $k => $v) {
            if (in_array($k, ['contacts_societe', 'contacts_message'])) {
                continue;#skip those
            }
            $x = injectionPattern($v);
            if ($x) {
                dbM('injection pattern v ' . $x . ' in ' . $v, 'injection v', $_ENV['lp'] . 'secu.log');
                r404('#c:'.__line__);
            }
            $x = injectionPattern($k);
            if ($x) {
                dbM('injection pattern k' . $x . ' in ' . $k, 'injection k', $_ENV['lp'] . 'secu.log');
                r404('#c:'.__line__);
            }
        }
    }
    if ($_FILES and !isset($_POST['nocheckzyx9'])) {
        foreach ($_FILES as $t) {
            $t['name'] = str_replace("\0", '', $t['name']);
            if (stripos($t['name'], '.php') !== false && 'should be enough') {
                dbM('!php file upload:' . $_ENV['IP'] . $t['name'] . "\n\n" . file_get_contents($t['tmp_name']), 'php file upload', $_ENV['lp'] . 'secu.log');
                r404('#c:'.__line__);#$t['name']
                return;
            }
            #file_contains <?php tags and stuff like that ?
        }
    }
}
#cuj https://php.home/antiddos 0 0 1;
if (function_exists('runkit_function_rename') and !isset($_ENV['runkitrewrittedfunctions'])) {#might cause :: call to undefined function file_get_contents or spl_autoload_register depending the built extension
    if ('functions') {
        #todo : microtime,time
        #todo : rewrite {{ eval, base64_decode, shell_exec }} for logging potential backdoor infected websites
        function mysqli_error2($a)
        {
            $_a = $_ENV['raw'];
            $_e = mysqli_error1($a);
            if ($_e) {
                $a = 1;
            }
            return $_e;
        }

        function ini_set2($a, $b = null)
        {
            return ini_set1($a, $b);
        }

        function mysqli_query2($a, $b = null)
        {
            return mysqli_query1($a, $b);
        }

        function spl_autoload_register2($a = 0, $b = 0, $c = 0, $d = 0)
        {
            return spl_autoload_register1($a, $b);
        }

        function simplexml_load_string2($a = 0, $b = 0, $c = 0, $d = 0)
        {
            try {
                $_ret = @simplexml_load_string1($a, $b);
                if (!preg_match('~Varien_Simplexml_Element|Mage_Core_Model_Config_Element|Mage_Core_Model_Layout_Element~i', $b)) {
                    $e = 1;
                }
                if (!$_ret) {#empty that's ok
                    $e = 1;
                }
                return $_ret;
            } catch (\Exception $e) {
                $f = 1;
            }
        }

#error_reporting
        function error_reporting2($a = 0, $b = 0, $c = 0, $d = 0)
        {
            $_a = debug_backtrace(-2);
            $a = 1;
            return 1;#error_reporting1($a,$b,$c,$d),
        }

        function mail2($a, $b, $c, $d)
        {
            $subject = str_replace(['=?utf-8?B', '?='], '', trim($b));#début et fin  #fin
            #base64_decode('?TXlDb2RhZ2U6IE5vdXZlbGxlIENvbW1hbmRlICMgOTAwMDAwMjIw')
            $subject = base64_decode($subject);
            $subject = strtr($subject, ['Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y']);#strip accents
            $__a = preg_replace('~[^a-z0-9]+|\-+~i', '-', strtolower($subject));

            if (preg_match('~nouvelle-commande~', $__a)) {
                $e = 1;
            }
            return mail1($a, $b, $c, $d);
            return 1;
        }

        function curl_exec2($ch)
        {
            $a = curl_getinfo($ch);
            $_u = $a['url'];
            if (preg_match('~image-charts\.com~', $a['url'])) {
                $b = 'nothing';
            } else {
                $b = 1;
            }
            return curl_exec1($ch);
        }

        function file_get_contents2($f, $b = null, $c = null, $d = null)
        {
            $a = file_get_contents1($f, $b, $c, $d);
            if (preg_match('~\.xml~', $f)) {#magento
                $e = 1;
            }
            return $a;
        }

        function file_put_contents2($f, $b, $c = null, $d = null)
        {
            if (preg_match('~thalgo\.log~', $f)) {
                return;
            } elseif (preg_match('~filepath.xml|thalgo\.log~', $f)) {
                $a = 1;
            }
            return file_put_contents1($f, $b, $c, $d);
        }

        function session_start2($o='')
        {
            return session_start1($o);
        }

        function header2($a = '', $b = true, $c = 0)
        {
            if (!$isMedia and !preg_match('~\.jpg~', $a) and !preg_match('~\?image=~', $_ENV['u']) and Preg_Match('~Location: ~i', $a)) {
                $d = 1;
            }
            header1($a, $b, $c);#les 404 not found de PR également suivent ce chemin
        }

        function session_destroy2()
        {
            return session_destroy1();
        }
    }
    $funcs = explode(',', 'ini_set,curl_exec,file_get_contents,file_put_contents,mysqli_error,session_destroy,header,mysqli_query,session_start,mail,spl_autoload_register,simplexml_load_string');#error_reporting fait planter les headers curieusement :)
    #$funcs = explode(',', 'curl_exec,file_get_contents,file_put_contents,mysqli_error,mysqli_query,mail,simplexml_load_string');
    foreach ($funcs as $x) {
        runkit_function_rename($x, $x . '1');
        runkit_function_rename($x . '2', $x);
    }
}


return;
#functions realm
function injectionPattern($x)
{
    /* recursive returns first positive match */
    if (is_array($x)) {
        foreach ($x as $v) {
            $res = injectionPattern($v);
            if ($res && 'returns first found') {
                return $res;
            }
        }
        return false;
    }

    /* most common possible injection patterns '--', '||',  'grant ','create ',  */
    $sqlInjectionPatterns = ['/*', '*/', 'sleep(', 'GET_HOST_NAME', 'drop ', 'truncate ', ' delete ', 'cast(', 'ascii(', 'char(', '@@'];
    foreach ($sqlInjectionPatterns as $v) {
        if (stripos($x, $v) !== false) {
            return $v;
        }
    }

    if (Preg_Match("~<script~i", $x, $m) && 'xss can be script src') {
        return 'script:' . $m[0];
    }
    if (Preg_Match("~<ifram~i", $x, $m) && 'xss can be iframe') {
        return 'iframe:' . $m[0];
    }
    if (Preg_Match("~<img~i", $x, $m) && 'xss can be img') {
        return 'img:' . $m[0];
    }
    if (Preg_Match("~' *or|\" *or|or *1 *= *1|union *all~i", $x, $m) && !Preg_Match("~[l|d]' *or~i", $x, $m) && 'pas anodin ..') {
        return $m[0];
    }

    if (Preg_Match("~url\(|data:image|/png;|base64,|option=com_xmap&view=xml&tmpl=component~i", $x, $m)) {
        return $m[0];
    }
    if (Preg_Match("~jos_users|\~root|print-439573653|/RK=|/RS=|concat\(|0x3a,password,usertype\)|http://http://|\*!union\*|plugin=imgmanager|w00tw00t|zologize/axa|HNAP1/|admin/file_manager|%63%67%69%2D%62%69%6E|%70%68%70?%2D%64+|cash+loans+|webdav/|cgi-bin|php?-d|union%20all%20select|convert%28int%2C~i", $x, $m)) {
        return $m[0];
    }
    return;#nothing found
}

function r404($x)
{
    _die($x);
}#404 response with message
function dbM($a, $b, $c)
{
    $a = 1;
}#logs debug and send mail
function autoloader($name)
{
    $namespace = '';
    $x = explode('\\', $name);
    $name = array_pop($x);
    if (count($x)) {
        $namespace = implode('/', $x) . '/';
    }
    $paths = ['code/local/', 'code/core/', '', '../'];
    foreach ($paths as $path) {
        $f = __DIR__ . '/' . $path . $namespace . $name . '.php';
        if (is_file($f)) {
            require_once $f;
            return 1;
        }
    }
    #if (stripos($name, 'Zend_Db_Statement_Pdo')!==FALSE) {#PDOStatement
    #déclarer cet autoloader avant celui de Magento ou Symfony afin de rewriter des classes du coeur sans modifier le projet
    $f = __DIR__ . '/overrides/' . $name . '.php';
    if (is_file($f)) {
        require_once $f;
        return 1;#désactive chargement magento la suite useless
    }

    if (strpos($name, '\\')) {
        $name = explode('\\', $name);
        $name = end($name);
    }#strip Unicorn
#die($name);
    $f = '/home/Alpine/alpine/' . $name . '.php';
    #/home/Alpine/alpine/Varien_Db_Statement_Pdo_Mysql.php
    if (is_file($f)) {
        $loaded[] = $name;
        #echo "<li>$name";print_r($a);
        require_once $f;
        return 1;
    }
    #pris en charge par les autoloaders suivants ..
    return;
}

function myError($no = null, $msg = null, $file = null, $line = null, $plus = null)
{
    if (in_array($no, [2, 8, 8192])) {
        return;
    }
    echo'<pre class="error">';print_r(compact('no', 'msg', 'file', 'line', 'plus'));echo'</pre>';
    $a = 1;
}

function myException($e)
{
    echo'<pre class="exception">';print_r($e);echo'</pre>';
    #echo $e->getMessage();
    $a = 1;
}

function nocache()
{
    header("Expires: on, 23 Feb 1983 19:37:15 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: text/html; charset=utf-8');
}

function r302($x)
{
    header('Location: ' . $x, 1, 302);
    die;
}

function getSimpleUnsecureSslPageContents($url)
{
    return curl($url)['contents'];
}

function curl($url, $opt = [], $post = [], $headers = [], $timeout = 10)
{
    $ch = \curl_init();
    $opts = [CURLOPT_URL => $url, CURLOPT_PORT => strpos($url, 'ttps:/') ? 443 : 80, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HEADER => 1, CURLINFO_HEADER_OUT => 1, CURLOPT_VERBOSE => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_AUTOREFERER => 1, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => $timeout, CURLOPT_HTTPHEADER => $headers];
    foreach ($opt as $k => $v) {
        $opts[$k] = $v;
    }
    #$opts[CURLOPT_HTTPHEADER][] = 'Expect:';#in case of 100 continue soft "error"
    if ($post) {
        $opts[CURLOPT_POST] = 1;
        $opts[CURLOPT_POSTFIELDS] = $post;#$url2Callback[$url]['post']
    }
    \curl_setopt_array($ch, $opts);
    $result = \curl_exec($ch);
    $info = \curl_getinfo($ch);
    $error = \curl_error($ch);
    \curl_close($ch);
    $header = substr($result, 0, $info['header_size']);
    $contents = substr($result, $info['header_size']);
    return compact('contents', 'header', 'info', 'error', 'opts');
}

function stripAccents($str)
{#operates is ascii context
    return strtr(utf8_decode($str), utf8_decode('���������������������������������������������������'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}

function get_headers_from_curl_response($response)
{
    $headers = array();
    $header_text = trim($response);#substr($response, 0, strpos($response, "\r\n\r\n"));
    foreach (explode("\r\n", $header_text) as $i => $line) {
        if ($i === 0) {
            $headers['http_code'] = $line;
        } else {
            list ($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }
    }
    return $headers;
}

function fap($file, $contents)
{
    return FPC($file, "\n" . $contents, 8);
}

function FPC($f, $d, $o = null)
{
    static $rec;
    $rec++;
    if (XON and $rec > 2) {
        $_bt = debug_backtrace();
        $err = 'recursivity';
    }
    $path = explode('/', $f);
    $end = array_pop($path);
    $folder = implode('/', $path);
    if ($folder and !is_dir($folder)) {
        $ok = mkdir($folder, 0777, 1);
        if (!$ok) {
            db('cant mkdir ' . $folder, 'anom.log');
        }
    }
    $rec--;
    return file_put_contents($f, $d, $o);
}

/** basic debug, append to error_log */
function db($x, $f = null)
{
    if (!$f) {
        $f = ini_get('error_log');
    }
    if (strpos($f, $_ENV['lp']) === false) {#anom.log
        $f = $_ENV['lp'] . $f;
    }
    $bt = bt(1);
    FPC($f, "\n}" . date('YmdHis') . ' ' . $_ENV['h'] . '/' . $_ENV['u'] . "{" . print_r(compact('x', 'bt'), 1) . json_encode(array_filter(['post' => $_POST, 'get' => $_GET, 'cook' => $_COOKIE, 'ip' => $_ENV['IP']]), 1) . "\n", 8);
}

function _die($x = null)
{
    print_r($x);
    die;
}

function fgc($file, $include_path = false, $context = null)
{
    $return = @file_get_contents($file, $include_path, $context);
    return $return;
}

function curlFile($url,$file,$name='',$post=[]){
    #die(realpath($file));
    if(!$name)$name=basename($file);#enctype : multipoart
    $files=['file' => '@' . realpath($file).';filename='.$name];
    $files=array_merge($post,['file' =>curl_file_create($file,'.jpg',$name)]);
    #$files=['nocheckzyx9'=>1,'file' =>curl_file_create($file,'.jpg',$name)];
    return cup(['url'=>$url,'post'=>$files,'headers'=>['content-type: multipart/form-data']]);
}

function cup($url, $opt = [], $post = [], $headers = [], $timeout = 10, $unsecure=1,$forcePort=0)
{
    if(is_array($url))extract($url);
    $ch = \curl_init();$headers[]='Expect:';/*100 header*/
    if(isset($opt[CURLOPT_URL]) and $opt[CURLOPT_URL]){
        $url=$opt[CURLOPT_URL];
    }
    $opts = [CURLOPT_URL => $url, CURLOPT_HEADER => 1, CURLINFO_HEADER_OUT => 1, CURLOPT_VERBOSE => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_AUTOREFERER => 1, CURLOPT_FOLLOWLOCATION => 1, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => $timeout, CURLOPT_HTTPHEADER => $headers];
    if($unsecure)$opts+=[CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false];
    if($forcePort)$opts+=[CURLOPT_PORT => strpos($url, 'ttps:/') ? 443 : 80];

    foreach ($opt as $k => $v) {
        $opts[$k] = $v;
    }
    #$opts[CURLOPT_HTTPHEADER][] = 'Expect:';#in case of 100 continue soft "error"
    if ($post) {
        $opts[CURLOPT_POST] = 1;
        $opts[CURLOPT_POSTFIELDS] = $post;#$url2Callback[$url]['post']
        #_die($post);
    }
    \curl_setopt_array($ch, $opts);
    $result = \curl_exec($ch);
    $info = \curl_getinfo($ch);
    $error = \curl_error($ch);
    \curl_close($ch);
    $header = substr($result, 0, $info['header_size']);
    $contents = substr($result, $info['header_size']);
    return compact('contents', 'header', 'info', 'error', 'opts');
}

function arrayContains($array,$contains=0,$lv=0,$bk=[]){
    $found=[];
    foreach($array as $k=>$v){
        if(is_array($v)){
            $found=array_merge($found,arrayContains($v,$contains,$lv+1,array_merge($bk,[$k])));
        }
        elseif(preg_match($contains,$v)){
            #_die(["found:$k"=>$v]);
            $found[]=[$k=>$v];
        }
    }
    return $found;
}

function searchInArrayDepths($array,$keys=0,$contains=0,$lv=0,$bk=['root']){    
    if(!$keys)$keys=explode(',','name,tmp_name');$c=count($keys);$matching=0;
    #if($lv==1)_die(compact('bk','array'));
    foreach($keys as $key){
        if(isset($array[$key])){            
            if(is_array($array[$key]) and $contains){                
                $c1=count(arrayContains($array[$key],$contains,0,$key));
                #_die($key.$contains.$c1);found twice
                #echo $c1;
                $matching+=$c1;                
            }elseif($contains){
                if(preg_match($contains,$array[$key]))$matching++;
            }else $matching++;
        }
    }
    if($matching>=$c){
        #_die("ok:$matching $c");
        return $array;
    }
    $found=[];
    foreach($array as $k=>$v){
        if(is_array($v)){
            $e=searchInArrayDepths($v,$keys,$contains,$lv+1,array_merge($bk,[$k]));#search deeper
            if($e){            
                $found=array_merge($found,$e);
                #_die("found::".$found);
            }
        }
    }
    return $found;
}

/*}caching?{*/
function memcache()
{
    if(isset($_ENV['noMemCache']))return 0;
    if (isset($_ENV['memcachedc'])) {
        return $_ENV['memcachedc'];
    }
    if (!function_exists('memcache_connect')) {
        $_ENV['memcachedc'] = 0;
        return 0;
    }
    $conn = @memcache_connect('127.0.0.1', 11211);
    if ($conn) {
        $_ENV['memcachedc'] = $conn;
    } else {
        $_ENV['memcachedc'] = 0;
    }
    return $_ENV['memcachedc'];
}

function mg($k)
{
    if (memcache()) {
        $v = memcache_get(memcache(), $k);
        return $v;
    }
    return null;
}

function ms($k, $v, $expiration = 2592000)#one year, instead of 10800, 30 days : 2592000  is max value
{
    #if(!$expiration){$expiration=time()+2592000;}
    #$expiration=time()+10800
    if (memcache()) {
        $exists=memcache_get(memcache(), $k);
        if($exists!==FALSE) {
            $set = memcache_replace(memcache(), $k, $v, 0/*MEMCACHE_COMPRESSED?*/, $expiration);
        }else{
            $set = memcache_add(memcache(), $k, $v, 0/*MEMCACHE_COMPRESSED?*/, $expiration);
        }
        if(!$set){
            $a=1;
        }
        return $set;
    }
    return null;
}

/*cacheproxy*/
function cacheGet($k)
{
    if (memcache()) {
        return mg($k);
    }
    if (isset($GLOBALS['argv'])) {
        #todo:igb, json ?
        return unserialize(FGC('z/shm/'.preg_replace('~[^a-z0-9]~is','',$k).'.cache'));
    } else {
        return apcu_fetch($k);
    }
    #-ok for php fpm
}

function cacheSet($k, $v)
{
    if (memcache()) {
        return ms($k, $v);
    }
    if (isset($GLOBALS['argv'])) {
        return FPC('z/shm/'.preg_replace('~[^a-z0-9]~is','',$k).'.cache',serialize($v));
    } else {
        return apcu_store($k, $v);
    }
}

function cacheDel($k){
    if (memcache()) {
        memcache_delete(memcache(), $k);
    }
    if (isset($GLOBALS['argv'])) {
        @unlink('z/shm/'.preg_replace('~[^a-z0-9]~is','',$k).'.cache');
    } else {
        return apcu_delete($k);
    }
}

function cacheList(){
    if (memcache()) {
        $list = array();
        $allSlabs = memcache_get_extended_stats(memcache(),'slabs');
        #$items = memcache_get_extended_stats(memcache(),'items');
        foreach ($allSlabs as $server1 => $slabs) {
            foreach ($slabs AS $slabId => $slabMeta) {
                $cdump = memcache_get_extended_stats(memcache(),'cachedump', (int)$slabId);
                foreach ($cdump AS $server3 => $arrVal) {
                    if (!is_array($arrVal)) {continue;}
                    $ak=array_keys($arrVal);
                    $arrVal=array_map('trim',$arrVal);
                    $list+=$ak;#
                }
            }
        }
        return $list;
    }else{
        $x=glob('z/shm/*.cache');
        $x=array_map(function($e){return str_replace(['z/shm/','.cache'],'',$e);},$x);
        return $x;
    }
}

function shExec($command){
    if(isset($_SERVER['WINDIR']) or isset($_SERVER['windir'])) {#windows special commands
        return pclose(popen($command, 'r'));
    }else{
        return exec($command);
    }
}

function getConf($k=null){
    if(!isset($_ENV['conf'])){
        $_ENV['conf']=json_decode(file_get_contents('app/params.json'),1);
    }
    if($k){
        if(isset($_ENV['conf'][$k])){
            return $_ENV['conf'][$k];
        }
        return null;
    }
    return $_ENV['conf'];
}

function setConf($k,$v){
    $_ENV['conf'][$k]=$v;
    file_put_contents('params.json',json_encode($_ENV['conf']));
}

function __autoload2($name){
    $name=str_replace('\\','/',$name);
    $tried=[];
    $paths=['app/code/local/', 'app/code/core/', '', '../'];#above autoload in common.php
    foreach($paths as $path){
        $tried[]=$f=$path.$name.'.php';
        if(is_file($f)){
            $_ENV['_autoloader2found'][$name]=$f;
            require_once $f;return 1;
        }
    }
    echo $name."-\n";
    print_r($tried);
    $_ENV['_autoloader2Notfound'][]=$name;
}
