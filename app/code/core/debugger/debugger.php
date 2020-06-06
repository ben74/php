<?php
#From salomon symfony's project spl_object_hash
namespace debugger;

if (isset($argv) && strpos($argv[0], 'recursiveBulletProofDebugger') and 'isTest : php debugger.php') {
    echo debugger::d($longObjectWithNestedRecursiveRelations);
}

$d = ['BFD' => 1 /*disables alls debugger*/, 'BFH' => ''];
foreach ($d as $k => $v) {
    define($k, $v, 1);
}
$d = null;
if (1) {
    $_ENV['callbacks'] = $_ENV['debugf'] = [];
    $currentDate = date('YmdHis');
}
/**
 * _pre(compact(explode(',','')));
 * rsync to 1.x24.fr/aez
 * Beware of those function within the code :: or disable auto_prepend for real testing !!
 * todo:Closure objects cannot have properties
 * $x=func_get_args();\Debug::d(compact('x','params','user','author'),1,0,BFH.time().'.debug.log');
 * \Debug::d(compact('x','params','user','author'),1,0,BFH.time().'.debug.log');
 * $bt=debug_backtrace();_pre(compact('bt','view','childs'),__file__.__line__,1);####
 */

if (1) {
    $ab = function ($x) {
        return '#a';
    };
    $_ENV['callbacks'] = [
        'Locale_Model_Locale' => $ab,
        'Fusion_Request_Http_Default' => $ab,
        #'Fusion_View_Php'=>function($x){return ['parent'=>$x->getParent(),'request'=>$x->getRequest(),'file'=>$x->getFile(),'channelEngine'=>$x->getChannelEngine()];},
        #'Fusion_View_ChannelEngine'=>function($x){return['param'=>$x->getParam('mainDataConfiguration')];},
        #'Fusion_View_Manager_Parameter'=>function($x){$x=['mainconf'=>$x->getParam('mainDataConfiguration')];return   $x;print_r([$x['_params']['mainDataConfiguration']['domain']['title'],$x]);die;},
    ];
}
/**
 * !!!![_request] => Fusion_Request_Http_Default Object
 * [_channelEngine] => Fusion_View_ChannelEngine Object
 */

if (1) {#conf
    function prer(&$x)
    {
        if (!is_string($x)) {
            $x = htmle($x);
            $x = print_r($x, 1);
            #amf($x);
            #foreach html_entities ??? <img <a => array_map_recursive
        }

        $x = str_replace(["\t", '    '], ' ', $x);#and multiples
        $x = str_replace([':protected', ':private'], '', $x);

        $x = preg_replace("~Array\s+\(~is", '[', $x);
        $x = preg_replace("~ +\)\n~is", ']', $x);#suppr end of array
        $x = preg_replace("~\[\n\s+\)\n~u", '[]', $x);
        return $x;
    }

    /**
     * todo:liens cumulés en haut à droite de la page d'appel
     */
#_pre($x,__CLASS__.__line__);
#_die(compact(explode(',','name,view')),__CLASS__);####
    function _pre($x = null, $file = null, $datedRun = 0, $append = 0)
    {#little biscuit, white tiger
        if (!BFD) {
            return;
        }
        static $i = [];
        if (!$file) {
            $bt = debug_backtrace();
            $file = str_replace('.php', '.' . $bt[0]['line'] . '.php', $bt[0]['file']);
        }
        $file = str_replace('/', '_', trim($file, '/'));
        #$file=explode('/',$file);$file=end($file);#die($file);
        $ext = explode('.', $file);
        list($fn, $ext, $ext2) = $ext;
        if (is_numeric($ext)) {
            $ext = $ext . '.dlog';
        }
        if (!$ext or $ext == 'html' or strpos($ext, 'php') !== false) {
            $ext = 'dlog';
        }#die($file);
        if ($datedRun) {
            $fn .= '.' . $GLOBALS['currentDate'];
        }
        $file2 = $fn . '.' . $ext;
        correctFilePath($file2);#die($file2);
        $file3 = str_replace(BFH, '', $file2);
        #if($append)$append=8;
        /*
            $i=0;
            while(is_file($file2) and $doNotOverwrite){
              $i++;$file2=$fn.".$i.".$ext;
            }
        */
        #if(in_array(gettype($x),['array','object']) and $file2){$x=['file'=>"#<a target=debug1 href='/$file2?debugger=1'>$file2</a>#run#$i","debug"=>$x];}
        prer($x);

        if ($file3 and !isset($i[$file3])) {
            $i[$file3] = 0;
            $x = U . "@" . DATE . "{\n" . $x;
        }

        if ($file3) {
            if (is_file($file2) and $i[$file3]) {
                $append = 8;
                $x = "#run#" . $i[$file3] . "{\n\n" . $x . "}";
            }#écriture plusieurs runs sur m^eme
            fpc($file2, $x, $append);
            $i[$file3]++;
            if (!in_array($file3, $_ENV['debugf'])) {
                $_ENV['debugf'][] = $file3;
            }
        } else {
            echo $a . $x . '</pre>';
        }
        if ($die) {
            die;
        }
    }

    function _die($x, $file = null)
    {
        _pre($x, $file, 1);
    }#_die(func_get_args()); _die(compact(explode(',','name,view')));

    #echo"<pre>";print_r($_ENV);die;
    if (!$_SERVER or !isset($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = 'cli';
    }
    if (!function_exists('redef')) {
        function redef($a, $b)
        {
            if (!defined($a)) {
                DEFINE($a, $b, 1);
            }
        }
    }
    function correctFilePath(&$x)
    {
        if (substr($x, 0, 1) != '/') {
            $x = BFH . str_replace(['/', '\''], '-', $x);
        }
        if (!strpos($x, '.')) {
            $x .= '.log';
        }#no extensions
        return $x;
    }

    function FPC($f, $x = '', $append = 0)
    {
        if (!BFD) {
            return;
        }
        correctFilePath($f);
        if (is_array($x)) {
            $x = json_encode($x);
        }
        if ($append) {
            $append = 8;
            $x = "\n\n" . $x;
        }
        file_put_contents($f, $x, $append);
    }

    function FAP($f, $x = '', $a = 0)
    {
        if (!BFD) {
            return;
        }
        FPC($f, $x, 8);
    }

    function df($data, $file)
    {
        fpc($file, $data);
    }

    function de($debug, $file = 0)
    {
        if (!BFD) {
            return;
        }
        $res = ['__class' => get_class($debug), '__methods' => get_class_methods($debug)];
        foreach ($debug as $k => $v) {
            $type = gettype($v);
            $res[$k] = $type;
            if (in_array($type, ['string', 'integer', 'float'])) {
                $res[$k] .= " : " . $v;
            }
            if ($type == 'function' && strtolower($v) == 'getid') {
                $res[$k] .= " : " . $v();
            }
        }
        $ms = explode(',', 'getId');
        foreach ($ms as $m) {
            if (array_search($m, $res['__methods'])) {
                $res[$m] = $debug->$m();
            }
        }

        $debug = print_r($res, 1);
        if ($file) {
            $s = '.debug.log.html';
            if (!strpos($file, $s)) {
                $file .= $s;
            }
            fpc($file, "<style>*{color:#FFF;background:#000;}</style><pre>" . $debug);
        }
    }#shorthand

    function d($debug, $file = 0)
    {#ahah
        if (!BFD) {
            return;
        }
        $debug = var_debug($debug);
        if ($file) {
            $s = '.debug.log.html';
            if (!strpos($file, $s)) {
                $file .= $s;
            }
            fpc($file, "<style>*{color:#FFF;background:#000;}</style><pre>" . $debug);
            return;
        }
        new Debug($debug, 1, 0, $file);
    }#shorthand

    function bt($die = 0, $trim = 1)
    {#file
        if (!BFD) {
            return;
        }
        $bt = debug_backtrace();
        if (!$file) {
            $file = explode('/', $bt[0]['file']);
            $file = str_replace('.php', '', end($file)) . '.' . $bt[0]['line'] . '.dlog.html';
        }#'.l.html';}
        #array_shift($bt);
        foreach ($bt as &$v) {
            $v['file'] .= ':' . $v['line'] . '->' . $v['function'];
            unset($v['line'], $v['function']);
            if ($trim) {
                $v = $v['file'];
                #unset($v['type'],$v['args'],$v['class'],$v['object'],$v['type']);
            }
        }
        unset($v);#function
        krsort($bt);
        _pre($bt, $file);
    }
}
#die(__file__);
#\\Debug\d(compact('x','params','user','author'),1,0,'/shared/'.time().'.log');
#tests/localhost okdie(__file__);
#die(__file__);#namespace bf;#used by magento
#/home/tests/debug.php
/*bf\Debug\d($array,$cond,$dies)
  standalone portable object debugger
  £: todo : log interface
  £: within serialized conf file
  #require_once __DIR__.'/../debug.php';echo \bf\Debug::d($obj);die;
  ordre de listage : variables publiques par ordre de déclaration, puis les protected \0*\0 && private
*/
$_ENV['stop'] = $_ENV['prevkey'] = null;/*inner debuggers tagged with #*innertial stabilizer*/
$_ENV['ignored'] = ['enfants', '__initializer__', '__cloner__', '__isInitialized__', 'connection', 'configuration'];#'data',
$_ENV['castings'] = ['datetime' => ['format' => 'Y-m-d H:i:s']];
$params = [
    'PRINTMETHODS' => 1
    ,
    'PRIV' => chr(0) . chr(42) . chr(0),
    'CLEANULL' => 1,
    'CLEANEMPTYARRAYS' => 1,
    'PERSISTENTCOLL' => 0,
    /*better*/
    'MAXTIME' => 5,
    'MAXIT' => 9900,
    'DEPTH' => 8,
    'STRLEN' => 250,
    'WIDTH' => 200,
    /*max array listed columns*/
    'MAXOUTPUTSIZE' => 20000,
    'MAXRAM' => 100,
    'MAXTIMEPEROBJ' => 1,
    'STRIPNAMESPACE' => 'mcBundle\\Entity\\',
#PersistentCollection,_select,
    'FILTERS' => strtolower('_cacheAdapter,_attributesById,_ddlCache,_attributes,_profiler,_typeInstanceSingleton,_urlModel,_typeInstance,_factory,_cache,_resources,_connections,_conn,_parts')
/*#strtolower('stock_item,_cache_editable_attributes,_lockedAttributes,Mage_Customer_Model_Resource_Address,_read,_conn,_adapter,_connections,#undefined,*_paramSources,reflectionclass,reflectionproperty,abstracthydrator,_initializer,_cloner,EntityManager,DebugHandler,:owner,:association,\MetadataBag,\ClassMetadata,\ArrayCache,:loadedAnnotations,AttributeBag,\FlashBag,\FormFactory,\HeaderBag,Hydration,ObjectHydrator,ReflectionClass,ReflectionProperty,AbstractHydrator,Closure,FrozenParameterBag,ORM\Mapping,EntityManager\conn,Validator\Constraints,maxMessage,exactMessage,minMessage,FormTypeValidatorExtension,ContainerAwareEventManager,Constraints\Length,DocParser\lexer,AnnotationReader,Constraints\UniqueEntity,Product:children,proxyClassTemplate,SubsidiaryadvancedProductPages,Navigationsubsidiaries,NavigationcreatedBy,NavigationmodifiedBy,PersistentCollectionem,PersistentCollectionowner,PersistentCollectionassociation,parameterbag,Mage_core,global/blocks,_xml')
[modules][],,_xml*/

];#    if (in_array($classname, ['request', 'parameterbag']))return $var;
#ArrayCollection

function debug2html($debug)
{
    if (!BFD) {
        return;
    }
    $length = [];
    cleanNullOrMaxDepthArrays($debug);
    $debug = htmle($debug);
    $debug = print_r($debug, 1);
    $debug = str_replace('    ', ' ', $debug);#basic indent : then %3 %5 %7 ... etc ..
    preg_match_all("~Array\n( *)\(~Us", $debug, $ms);
    if (!$ms) {
        die($debug);
    }
    ?>
    <style>#predb {
            z-index: 1002;
            resize: both;
            overflow: auto;
            position: fixed;
            top: 10px;
            left: 0px;
            width: 800px;
            height: 500px;
            text-align: left;
            white-space: pre-wrap;
            background: #000;
            color: #FFF;
        }

        .mar {
            display: inline-block;
            border: 1px dashed rgba(255, 0, 0, 0.2);
            margin-left: 5px;
            padding-left: 5px;
        }

        .s30 {
            background: #fff;
            color: #000;
        }

        .s28 {
            background: #eee;
            color: #000;
        }

        .s26 {
            background: #ddd;
            color: #000;
        }

        .s24 {
            background: #CCC;
            color: #000;
        }

        .s22 {
            background: #BBB;
            color: #000;
        }

        .s20 {
            background: #AAA
        }

        .s18 {
            background: #999
        }

        .s16 {
            background: #888
        }

        .s14 {
            background: #777
        }

        .s12 {
            background: #666
        }

        .s10 {
            background: #555
        }

        .s8 {
            background: #444
        }

        .s6 {
            background: #333
        }

        .s4 {
            background: #222
        }

        .s2 {
            background: #111
        }
    </style>
    <pre id=predb><?


    foreach ($ms[1] as $m) {
        $len = strlen($m);
        if (!isset($length[$len])) {
            $length[$len] = 0;
        }
        $length[$len]++;
        $m = null;
    }
    krsort($length);
    $it = $culevel = 0;
    $levels = [];

    foreach ($length as $len => $occurences) {
        $sl = str_repeat(' ', $len);
        $debug = str_replace("Array\n" . $sl . '(', "£$len:", $debug);
        $debug = str_replace("\n" . $sl . ')', "¤$len:", $debug);
        #$r="~Array\n".$sl."\(\n(((?!Array\n).)+)".$sl."\)\n~Us";#\n U matches more occurences !!!
        $r = "~£" . $len . ":(((?!£).)+)¤" . $len . ":\n~Us";#\n U matches more occurences !!!
        $r = "~£" . $len . ":([^£]+)¤" . $len . ":\n~Us";#210 matches !!!
        #£16:([^£]+)¤16:
        preg_match_all($r, $debug, $m);
        if (count($m[0])) {
            foreach ($m[0] as $k => $origin) {
                if (!trim($origin)) {
                    continue;
                }
                if(is_array($convert)){
                $new = str_replace(array_keys($convert), array_values($convert), "\n<div title='-' class='mar s$len'> " . trim(str_replace($sl, '', $m[1][$k])) . "</div>");
                #$new="\n".$sl."<div class='mar s$len'>len:$len</div>";#+7
                $debug = str_replace($origin, $new, $debug);#£< ¤>
                $it++;
                $its[$len]++;
                }
            }
        } else {
            $its[$len] = str_replace("\n", '\n', $r);
        }
    }

    $x = explode("\n", $debug);
    foreach ($x as &$line) {
        if (substr($line, 0, 4) == '<div') {#nouveau niveau
            #preg_match("~s([0-9]+)'> \[([^\]]+)\]~U",$line,$m);
            #if($m[2])$levels[$level]=$m[2];
            preg_match("~s([0-9]+)'>~U", $line, $m);
            $legend = '';
            $culevel = $m[1];
            foreach ($levels as $lev => $leg) {
                if ($lev >= $culevel) {
                    break;
                }
                $legend .= '[' . $leg . ']';
            }
            $line = str_replace("title='-'", "title=\"" . $legend . "\"", $line);
        }
        if (substr($line, -4, 3) === ' =>') {#nouvelle clé  /*substr($line,1,1)=='[' && */)
            preg_match('~\[([^\]]+)\] => $~U', $line, $m);
            $levels[$culevel] = $m[1];
        }
        #<div title='-' class='mar s[0-9]+'> [mage_catalog_model_product1] =>
    }
    unset($line);
    $debug = implode("\n", $x);

    return $debug;
}

#ini_set('max_execution_time',200);
if (strpos($_SERVER['REQUEST_URI'], 'debug.php') !== false) {
    $debug = unserialize(file_get_contents('debug.sdb'));
    die(debug2html($debug));
}


ini_set('memory_limit', '1000M');#erreur : limité à 277 ou 257 -> ou cela ??
$tn = $start = time();


if (0) {#McIntranet Castings
    foreach (['propositions', 'refacted', 'taches', 'factetape', 'logstatut', 'ismaster', 'users', 'propositionfactures', 'clients', 'lotenfants', 'lots', 'client_entites', 'propositionuserlock',] as $v) {
        $_ENV['castings'][$v] = 'count';
    }
    foreach (['refact', 'contact', 'achat'] as $v) {
        $_ENV['castings'][$v] = 'id';
    }#,'lot','client_entite',
    foreach ([/*'entite',*/
                 'statut',
                 'type'
             ] as $v) {
        $_ENV['castings'][$v] = 'nom';
    }
    #foreach(['user'] as $v)$_ENV['castings'][$v]='username';
    foreach (['formbuilder', 'last_result', '__initializer__', '__cloner__', '__isinitialized__', 'enfants', 'enfant', 'debug'] as $v) {
        $_ENV['castings'][$v] = 'unset';
    }
}

foreach ($params as $k => $v) {
    redef($k, $v);
}

#die(FILTERS);

class debugger
{
    static $instance;

    public static function i()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($debug = '', $cond = 1, $dies = 0, $file = 0)
    {
        if ($debug) {
            $this->d($debug, $cond = 1, $dies = 1, $file = 0);
        }
    }

    static function d($debug, $cond = 1, $dies = 0, $file = 0)
    {#returns single instance from static context to be used as an object : Debug::d($debug);
        if (!BFD) {
            return;
        }
        if ($file) {
            die($file);
        }
        if (!$cond) {
            return;
        }#$obj=self::i();
#@ob_start();\Doctrine\Common\Util\Debug::dump($debug,4);$$debug=ob_get_clean();return pr1($debug);#doctrine way
        init();
        $bt = debug_backtrace();
        #array_shift($bt);#trim one
        $call = array_shift($bt);
        if (!$call or !isset($call['file'])) {
            FPC(BFH . 'fatal.log', print_r(debug_backtrace(), 1));
            return;
        }
        $call = $call['file'] . ':' . $call['line'];
        #$tmp=explode('/',$call['file']);if(count($tmp)<2)$tmp=explode('\\',$call['file']);#nunows
        $debug = cleanRecursion($debug);
        cleanNullOrMaxDepthArrays($debug);

        if (0) {#***
            $xt = (array)end($debug);
            $y = [];
            $y = array_keys($xt);
            $split = str_split($y[1]);
            foreach ($split as &$v) {
                $v .= '§' . ord($v);
            }
            unset($v);
            pr1(['line' => __line__, 'aborted', $split, isset($xt[PRIV . 'factetape'])] + $y, 1);#enfants,statut,data,*factetape ( public from proposition );
        }
        #isSerializable, now !

        #$debug=var_debug($debug);
        if ($file or isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $tofile = compact('call', 'debug');
            amr('utf8_encode', $tofile);
            $tofile = json_encode($tofile);
            if ($file) {
                file_put_contents($file, "\n\n\n" . $tofile, FILE_APPEND);
            }#Keep a Memo

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {#AJAX
                if ($dies) {
                    die($tofile);
                }#return new JSON_response
                #cleanOut($tofile,1);
                return $tofile;#return new JSON_response
            }
            return;
            #return json_encode($debug);
        } else {
            die(debug2html($debug));
        }

        if (!isset($_SERVER['HTTP_HOST'])) {
            $debug = print_r(compact('call', 'debug'), 1);
            die(CleanOut($debug));
        }
#Failsafe for first debug ( recursivity issues ) toggles advanced display -> is now flattened
        $debug = var_debug($debug);
        $debug = print_r(compact('call', 'debug'), 1);
        CleanOut($debug);
        /* know that #client referers to first client level
        $x = preg_replace("~\n[ ]+(bool|int)\(([a-z]*[0-9]*)\)~", "\\2", $x);
        $x = preg_replace("~\n[ ]+string\([0-9]+\)~", "", $x);
        #$x=preg_replace("~\n    bool(false)~","0",$x);
        $x = preg_replace("~\n[ ]+([^\n]+\n[ ]+null|\[[^\n]+\] => null)~i", "", $x);
        $x = preg_replace("~\(([0-9]+)\) {\n[ ]+\.\.\.\n[ ]+}~", "(\\1)...", $x);
        */
        if ($dies < 0) {
            file_put_contents(ini_get('error_log'), print_r($debug, 1));
            return;
        }

        header('Content-Type: text/html; charset=utf-8', 1);
        echo "<link rel=stylesheet href='//ben/codes/debug.css'><script src='//ben/codes/debug.js' type='text/javascript'></script>";#
        if ($dies) {
            die(pr1($debug));
        }
        return pr1($debug);
    }
}

/*** end of class : global functions scope **/
function ignoreClass()
{
}

function cleanRecursion($inputvar, $params = null, $level = 0)
{
    static $t = 0, $skip;
    if (!$t) {
        $skip = explode(',', $_SESSION['filter']);
        $t = 1;
    }
    $aKeys = [];
    $keys = $lastKey = '';

    $as = $objid = $classname = null;
    static $objects, $objcloned, $iterations;

    if (is_array($params)) {
        extract($params);
    }#overrides level & others objects
    #if(is_array($params) && $keys=='[statut]')pr1([__line__,$_ENV['prevkey']]+compact('params','objects'),1);#**

    if ($level >= DEPTH) {
        return '#!maxdepth';
    }
    if (isset($_ENV['a']['kill'])) {
        return '#!kill';
    }
    if ($iterations > MAXIT) {
        return '#!maxit:' . MAXIT;
    }
    if ($x = globalTimeLimit()) {
        return '#!maxtime';
    }

    if ($x = timelimit()) {
        timelimit(1);
        return $x;
    }
    if (memory_get_usage(1) / 1000000 > MAXRAM) {
        $_ENV['a']['MAXRAM'] = 1;
        return '#!ram limit exceeded : ' . MAXRAM . 'Mo';
    }

    $type = gettype($inputvar);
    if (in_array($type, ['function', 'object']) and is_callable($inputvar)) {
        return '#!closure';
    }
    if (!in_array($type, ['array', 'object'])) {
        return $inputvar;
    }#$inputvar;#$inputvar;#string int  ['not array, nor objcet'];#

    if (!is_array($objects)) {
        if ($type == 'object') {
            $type = 'array';
            $classname = shortclass($inputvar);
            $inputvar = ['' . $classname => $inputvar];
        }#1ere dimension en tant qu'un array contenant l'objet
        $iterations = $objcloned = 0;
        $objects = [];
    }#first run, resets

    #if(!$keys)$keys='gnagna';

    if ($keys) {
        $_ENV['prevkey'] = $keys;
    } else {
        $keys = $_ENV['prevkey'];
    }#might be lost : root:proposition:data:enfants

    $aKeys = explode('[', $keys);
    foreach ($aKeys as &$v) {
        $v = trim($v, '[]');
    }
    unset($v);
    $aKeys = array_filter($aKeys);
    $classname = $lastKey = end($aKeys);#au cas où ...

    #if(!$keys)pr1([__line__,$_ENV['prevkey']]+compact('classname','level','k','keys','aKeys'),1);#enfants->_methods
    #if($keys=='[root]')pr1([__line__,$_ENV['prevkey']]+compact('aKeys','lastKey','keys','level'),1);

    $iterations++;
    $level++;
    $obj = $inputvar;

    if ($type === 'object') {
        if (is_callable($obj)) {
            return '#!closure';
        }
        $objid = null;
        $classname = shortclass($obj);

        if (strposa($classname, $skip)) {
            $_ENV['skipped'][] = $classname;
            return $classname . '#skip';
        }

        foreach ($skip as $v) {
            if (stripos($classname, $v) > -1) {
                return '#!skip:' . $v;
            }
        }

        $cast = castAction($obj, $as);
        if ($cast === 'unset') {
            return null;
        }

        if (method_exists($obj, 'getId')) {
            $objid = $obj->getId();
        } elseif (isset($obj->id)) {
            $objid = $obj->id;
        }

        if (is_numeric($objid)) {
            if (isset($objects[$classname][$objid])) {
                $rid = $objects[$classname][$objid];
                if (stripos($keys, $rid) !== false) {
                    $rid .= "($rid)*";
                }
                #if($classname=='proposition' && $objid==3)pr1(compact('keys','rid','objid')+[__line__],1);#[proposition0]*3:134
                return '#' . $rid;
            }
            $objects[$classname][$objid] = $keys;
        } else {
            $hash = spl_object_hash($obj);
            if (isset($objects[$hash])) {
                $rid = $objects[$hash];
                if (stripos($keys, $rid) !== false) {
                    $rid .= '*';
                }#die($rid.__line__);
                return '#' . $rid;
            }
            $objects[$hash] = $keys;
        }

        if ($cast) {
            return $cast;
        }

        if (!$objcloned and !in_array($classname, ['phpexcel_worksheet_celliterator'])) {
            $objcloned = 1;
            $obj = clone($inputvar);
        }#resets the internal spl_object_hash but allows further modifications

        if ($classname == 'persistentcollection') {#symfonys
            if (PERSISTENTCOLL) {
                $vals = $obj->getValues();
                if (count($vals) == 0) {
                    return null;
                }
                foreach ($vals as $k => &$val) {
                    $val = cleanRecursion($val, ['level' => $level, 'keys' => $keys . '[' . $k . ']', 'as' => $lastKey]);#limit Level 1??
                    #if(gettype($val)=='string'){if(substr($val,0,2)=='#!');elseif(substr($val,0,1)=='#')$_ENV['stop']=compact('keys','k');}

                    if (0 and isset($_ENV['stop'])) {
                        $db = debug_backtrace();
                        pr1(compact('db', 'aKeys', 'keys', 'classname', 'objid', 'k', 'type', 'typev'), 1);
                    }

                }
                unset($val);
                #$vals=(array)$vals;
                return $vals;
                return count($vals) . ' values';
                #if($parcours){foreach($vals as $val)$ret[]=$val->$parcours();return $ret;}
            } else {
                return '#pc';
            }#listent par dessus
        }
        #resets the internal spl_object_hash but allows further modifications

#En premier lieu on vérifie si l'objet est un doublon + parcours des collections, ensuite on le converti en array pour parcourir chacune de ses propriétés
        $methods = implode(', ', get_class_methods($obj));
/*#$methods=['_methods'=>implode(', ',$methods)];
#[message:protected] => count(): Argument #1 ($value) must be of type Countable|array, baseObject given*/
        $_ENV['obj'] = compact('classname', 'objid') + ['props' => 0/*count($obj)*/];
        if (!$keys) {
            pr1(['line' => __line__, $classname]);
        }
        $obj = (array)$obj;#casting it to an array, so ... :)
        if (PRINTMETHODS) {
            $obj['__methods'] = $methods;
        }
    }

    $processed = $new = [];

    if (is_array($obj)) {
        #1 : propriétés publiques de l'objet
        foreach ($obj as $k => $v) {
            $k1 = $k;
            if (in_array($k, $processed)) {
                Continue;
            }
            if (in_array($k, $_ENV['ignored'])) {
                unset($obj[$k]);
                Continue;
            }
            #if(!$keys)pr1([__line__]+compact('classname','k','keys'),1);#enfants->_methods
            #if(substr($k,0,2)=='__'){$v=null;continue;}#__initializer,__cloner
            $typev = gettype($v);

            if (substr($k, 0, 3) == "\0*\0") {
                $k = str_replace("\0*\0", '', $k);
            }#$_ENV['stop']=[$k1,$classname,__line__];
            if (stripos($k, '*') !== false) {
                $k = str_replace('*', '', $k);
            }#pour traitements suivants
            if (strpos($k, STRIPNAMESPACE) > -1) {
                $k = str_replace(STRIPNAMESPACE, '', $k);
            }
            if (gettype($k) == 'object') {
                $k = 'k:' . shortclass($k);
            }#replaces the keys
            if ($typev == 'object' and is_numeric($k)) {
                $k = shortclass($v) . (($objid) ? $objid : $k);
            }#;$i=0;while(isset($obj[$k2])){$i++;$k2=$k+''+$i;die($k2);}$k=$k2;

            #if(in_array($k,$aKeys))$v='#parent';
            #if(strpos($keys,'[proposition0][taches]')!==false){print_r(compact('k','keys'));die;}
            #foreach($skip as $v2)if(stripos($k,$v2)>-1){$obj[$k]='#badclass';continue;}
            if ($k1 != $k) {
                $obj[$k1] = null;
                unset($obj[$k1]);
            }
            $obj[$k] = $v;
            #if($k=='factetape' && $level==2)pr1(compact('lastKey','k','objects','level')+['vals'=>cleanRecursion($v),'line'=>__line__],1);
            $processed[] = $k;

            #if(in_array($k,['idproposition',PRIV.'idproposition','lotEnfants']))$_ENV['stop']=9;
        }##endfor array
        /*Tri secondaire*/
        foreach ($obj as $k => &$v) {#les effectuer après les premières
            if (strposa($k, $skip)) {
                $_ENV['skipped'][] = $k;
                $v = $k . '#skip';
                continue;
            }
            $typev = gettype($v);
            if (!in_array($typev, ['array', 'object'])) {
                Continue;
            }
            #if(substr_count($keys,']')>1)die($keys.'['.$k.']');
            $v = cleanRecursion($v, ['level' => $level, 'keys' => $keys . '[' . $k . ']', 'as' => array_unique([$k, $k1])]);##
            #if(CLEANEMPTYARRAYS && $type=='array')$v=array_filter($v);if(count($v)==0)$v=null;
            #if(gettype($obj[$k])=='string'){if(substr($obj[$k],0,2)=='#!');elseif(substr($obj[$k],0,1)=='#')pr1(compact('k','keys','rid','objid')+[$obj[$k],__line__],1);}##
            if (0 and isset($_ENV['stop'])) {
                pr1(['vals' => cleanRecursion($obj), 'stop' => $_ENV['stop'], 'line' => __line__] + compact('aKeys', 'keys', 'classname', 'objid', 'k', 'type', 'typev'), 1);
            }
        }
        if (!$obj) {
            $a = 1;
        }
        unset($v);
        #if(isset($obj['factetape']))$obj['notset']='#notset';
        #if($_ENV['stop']==9 || in_array(array_keys($obj),['status',PRIV.'status']))pr1(['line'=>__line__]+compact('k','k1','lastKey','level')+['ENVl'=>$_ENV['prevkey'],$_ENV['stop'],'akeys'=>array_keys($obj)],1);
    }

    if (0 and CLEANULL and in_array($type, ['array', 'object'])) {
        $_obj = $obj;
        amf($obj);
        $a = 1;
    }#ce n'est pas cela ...
    #level 7 of refacted(lastkey) -> targets a proposition
#arrive en dernier sur l'objet de base .. ordre déclaration des variables ?
    if (0 and (
            $level == 2
            || $keys == '[root][proposition]'
            #|| isset($_ENV['stop'])||
            #|| isset($obj[PRIV.'idproposition']) || isset($obj['idproposition'])
        )) {
        pr1(['line' => __line__] + compact('iterations', 'classname', 'keys', 'lastKey', 'objid', 'level') + ['prevkey' => $_ENV['prevkey'], 'stop' => $_ENV['stop'], 'Objkeys' => array_keys($obj)], 1);
    }
    if ($level < 2) {
        $a = 1;
    }
    return $obj;
}

function pr1($x, $dies = 0)
{
    if (!BFD) {
        return;
    }
    static $i;
    $i++;
    if (!is_string($x)) {
        $x = print_r($x, 1);
    }
    $x = "<pre class='pre debug debugger' id='db$i'>" . $x . '</pre>';
    if ($dies) {
        die($x);
    }
    return $x;
}

function globalTimeLimit()
{
    global $start;
    if ($start - time() > MAXTIME) {
        return 1;
    }
}

function timelimit($set = null)
{
    global $tn;
    if ($set) {
        $tn = time();
        return;
    }
    if ($tn - time() > MAXTIMEPEROBJ) {
        return 'maxtime:' . MAXTIMEPEROBJ;
    }
    return;
}


function var_debug($inputvar, $strlen = STRLEN, $width = WIDTH, $depth = DEPTH, $i = 0, &$objects = [])
{
    static $iterations, $init, $skip, $nkey, $j, $objcloned, $objids, $objids, $aKeys;
    if (!$iterations) {
        $iterations = 0;
    }
    $iterations++;
    if (isset($_ENV['a']['kill']) || $iterations > MAXIT || $x = globalTimeLimit()) {
        return;
    }
    if ($x = timelimit()) {
        timelimit(1);
        return $x;
    }
    if (memory_get_usage(1) / 1000000 > MAXRAM) {
        $_ENV['a']['MAXRAM'] = 1;
        return '-- ram limit exceeded : ' . MAXRAM . 'Mo';
    }

    $parentKeys = '';
    if (is_array($strlen)) {
        extract($strlen);
        $strlen = STRLEN;
    }

    if (!$init or !$i) {
        init();
        $init = 1;
        $objids = $aKeys = [];
        $skip = explode(',', $_SESSION['filter']);
        gt1('var_debug:0');
    }

    $type = gettype($inputvar);
    #if($type=='object' && \ReflectionClass::isCloneable($inputvar))die(get_class($inputvar).'clonable');
    #if(!in_Array($type,['array','object']))return $inputvar;#retourne simple string
    if (!$objcloned && $type == 'object') {
        $var = clone($inputvar);
        $objcloned = 1;
    } else {
        $var = $inputvar;
    }

#,'Symfony\Component\Form\FormBuilder'
    $search = ["\0*\0", "\0", "\a", "\b", "\f", "\n", "\r", "\t", "\v"];
    $replace = ['', '', '\a', '\b', '\f', '\n', '\r', '\t', '\v'];#'\0'
    $string = $s2 = '';

    switch (gettype($var)) {
        case 'NULL':
            return null;
            break;#$string.='null';
        case 'boolean':
            $string .= $var ? 'true' : 'false';
            break;
        case 'integer':
            $string .= $var;
            break;
        case 'double':
            $string .= $var;
            break;
        case 'resource':
            $string .= '[resource]';
            break;
        case 'string':
            if (substr($var, 0, 2) == '#!') {
                return null;
            }
            if (substr($var, 0, 1) == '#') {
                if (in_array(trim($var, '#*'), $aKeys)) {
                    return "<a class='hash' href='#" . trim($var, '#*') . "'>" . $var . '</a>' . ((strpos($var, $parentKeys) !== false) ? ' *' : '');
                    #£:marquer recursions par le dessous avec une étoile *
                }
                #die(__line__.$var);##[proposition0][logstatut][102][statut]
            }
            ##[proposition0][taches]
            $len = strlen($var);
            $var = str_replace($search, $replace, substr($var, 0, $strlen), $count);
            $var = substr($var, 0, $strlen);
            $var = htmlspecialchars(str_replace('""', '', $var));
            if ($len < $strlen) {
                $string .= $var;
            } else {
                $string .= 'string(' . $len . '): ' . $var . '...';
            }
            //('.$len.')
            #if(substr_count($string,'"')%2)$string.='"';#fermeture
            #if(preg_match('~\.{3}\n?$~',$string))$string.='"';#correcteur
            break;

        case 'array':
            $len = count($var);
            if ($i == $depth) {
                $string .= 'array(' . $len . ') #depth';
            } elseif (!$len and CLEANULL) {
                $string = '';
            } elseif (!$len) {
                $string .= 'a:null';
            } else {
                $spaces = str_repeat(' ', $i * 2);
                $j++;
                $closin = $j;
                #J Shall remain the same uh ? Nahhh :(
                $count = 0;
                #$keys = array_keys($var);foreach $keys as $key
                foreach ($var as $key => $value2) {
                    if (!is_numeric($key) and strposa($key, $skip)) {
                        $_ENV['skipped'][] = $key;
                        return $key . '#skip';
                    }
                    if (isset($_ENV['a']['MAXRAM'])) {
                        break;
                    }
                    if (is_string($value2) and substr($value2, 0, 2) == '#!') {
                        continue;
                    }
                    #if (CLEANULL && (!isset($value2) || !$value2)) {continue;}
                    if ($count === $width) {
                        $s2 .= "\n" . $spaces . "  ...";
                        break;
                    }

                    $currentKey = $parentKeys . "[$key]";
                    $aKeys[] = $currentKey;
                    $s2 .= "\n" . $spaces . " <a href='#$parentKeys'>&lt;</a><c id='" . $currentKey . "' title='$currentKey'>[$key]</c> => ";

                    if ($key === 'HomeRh' && 0) { //&& stripos($var,'div class')>-1||stripos($var,'sur notre page recrutement')||stripos($var,'"')
                        fpco(R1 . '1.php', gettype($value2) . $value2 . print_r($value2, 1));
                        $_ENV['break'] = 1;
                        var_debug($value2);
                        $_ENV['break'] = 0;
                    }
                    $s2 .= var_debug($value2, compact('objects') + ['i' => $i + 1, 'parentKeys' => $currentKey]);
                    $count++;
                }
                if (CLEANEMPTYARRAYS && !$s2) {
                    return null;
                    $string = '';
                    break;
                }
                $string .= "<a id='o$j' href='#c$j' class='open'>array($len)</a>\n" . $spaces . "{" . $s2; #<b class='".($i*2)."'>
                $string .= "\n" . $spaces . "<a id='c$closin' href='#o$closin' title='$closin' class='close'>}--</a></b>";
            }
            break;
#Salomon\DatabaseBundle\Entity\Subsidiary:advancedProductPages] => Doctrine\ORM\PersistentCollection
        case 'object':#n'est plus utilisé si déjà passé dans l'anti-recursion primaire
            timelimit(1);
            $id = $objid = null;
            $classname = get_class($var);
            $cn = shortClass($var);
            if (strposa($classname, $skip)) {
                $_ENV['skipped'][] = $classname;
                return $classname . '#skip';
            }

            if (method_exists($var, 'getId')) {
                $objid = $var->getId();
            } elseif (isset($var->id) && is_integer($var->id)) {
                $objid = $var->id;
            }

            if (isset($objid) && isset($objids[$cn]) && array_key_exists($objid, $objids[$cn])) {
                return '<a class=obj href="#' . $cn . ($objid) . '">' . $cn . '#listed as ' . ($objid) . "</a>";
            }

            $hash = spl_object_hash($var);
            $id = array_search($hash, $objects, true);
            if ($id !== false) {
                return '<a class=obj href="#' . $cn . ($id + 1) . '">' . $cn . '#listed as ' . ($id + 1) . "</a>";
            } else {
                if ($i == $depth) {
                    $string .= $cn . ' #!depth';
                } else {
                    if ($objid && $cn != 'stdclass') {
                        $id = $objid;
                        $objids[$cn][$objid] = 1;
                    } else {
                        $id = array_push($objects, $hash);
                    }

                    $spaces = str_repeat(' ', $i * 2);
                    $j++;
                    $closin = $j;

                    $cast = castAction($var);
                    if ($cast === 'unset') {
                        continue;
                    }
                    if ($cast !== false && in_array(gettype($cast), ['array', 'object'])) {
                        $string .= var_debug($cast, compact('objects') + ['i' => $i + 1]);
                        #$string .= var_debug($cast, compact('objects')+['i'=>$i+1,'parentKeys'=>$currentKey]);
                        continue;
                    } elseif ($cast !== false) {
                        $string .= $cast;
                    } //todo:other than to array castings
                    elseif (PERSISTENTCOLL && $cn === 'persistentcollection') { # && !in_array($var->get('backRefFieldName'),['subsidiary']) && $var->getValues() && count($var->getValues())>0
                        $next = $var->getValues();
                        $count = count($next);
                        if ($count === 0 && CLEANULL) {
                            return;
                        }
                        if ($count === 0) {
                            $string .= " pc => 0";
                        } elseif ($count > 1000) {
                            $string .= " pc + 1000";
                        } else {
                            $string .= 'pc';
                            $type = shortClass($next[0]);
                            if (0 && method_exists($next[0], 'getId')) { #is_integer($next[0]->id)){
                                $tids = [];
                                foreach ($next as $t) {
                                    $tids[] = $t->getId();
                                }
                                $string .= " pc:ids => " . implode(',', $tids);
                            } else {
                                if ($x = timelimit()) {
                                    $string .= $x;
                                    break;
                                }
                                $string .= var_debug($next, compact('objects') + ['i' => $i + 1]);
                                #$string .= var_debug($next,compact('objects')+['i'=>$i+1,'parentKeys'=>$currentKey]);
                            }
                        }
                        unset($next);
                        #die('<pre>'.print_r($var->getValues(),1));die(var_debug($var->getValues()));
                    } else {
                        $methods = get_class_methods($var);
                        foreach ($methods as &$method) {
                            if (substr($method, 0, 2) == '__') {
                                $method = null;
                            }
                        }
                        unset($method);
                        $methods = array_filter($methods);
                        $methods = implode(', ', $methods);
                        $string .= "<a title=\"$classname\" class=i id='" . $cn . $id . "' href='#c$j'>#" . $cn . "#$id</a>";
                        $string .= "\n" . $spaces . "{" . $spaces;
                        if ($methods) {
                            $string .= "\n" . $spaces . "  [*] => " . $methods;
                        }
                        $array = (array)$var;
                        #$properties = array_keys($array);
                        foreach ($array as $property => $value2) {
                            if (isset($_ENV['a']['MAXRAM'])) {
                                break;
                            }
                            if (is_string($value2) && substr($value2, 0, 2) == '#!') {
                                continue;
                            }
                            if (substr($property, 0, 2) === '__') {
                                continue;
                            }#initialize,cloner
                            if (CLEANULL && !$value2) {
                                continue;
                            }

                            $currentKey = $parentKeys . "[$property]";
                            $aKeys[] = $currentKey;

                            $name = str_ireplace([STRIPNAMESPACE, $classname, $cn, "\0", '*'], ['', '', '', '', ''], trim($property));

                            $cast = castAction($value2, $name);
                            if ($cast === 'unset') {
                                continue;
                            }

                            if ($cast !== false && in_array(gettype($cast), ['array', 'object'])) {
                                $value2 = $cast;
                            } elseif (!isset($_ENV['a']['nr']) && $cast !== false && $cn != 'stdclass') {
                                $string .= "\n" . $spaces . "  [$name] => " . $cast;
                                #$e=error_get_last();if(strpos($e['message'],'ndefined index'));else{$_ENV['a']['nr']=1;dbe(['nr'=>1,$e]);die;}
                                continue;
                            }

                            if (strposa($name, $skip)) {
                                $_ENV['skipped'][] = $name;
                                $string .= '#s';
                            } else {
                                $x = var_debug($value2, compact('objects') + ['i' => $i + 1, 'parentKeys' => $currentKey]);
                                if ($x) {
                                    $string .= "\n" . $spaces . "  [$name] => " . $x;
                                }
                            }
                        }
                        $string .= "\n" . $spaces . "<a id='c$closin' href='#" . $cn . $id . "' title='$closin'>}--</a></b>";
                    }
                }
            }
            break;
        default:
            $string .= '???#unknow type';
            break;
    }
    #if ($i > 0)return $string;#do not returns first index
    return $string;
}

##
function gt1()
{
}

function fpco()
{
}

function ccn(&$x)
{
    if (is_array($x)) {
        foreach ($x as &$v) {
            ccn($v);
        }
        return $x;
    }
    $x = strtolower(str_replace(['*', "\0"], '', $x));
    return $x;
}

/* conversion d'un objet en une certaine valeur */
function castAction($object, $className = null)
{
    if (!isset($_ENV['castings']) || (isset($GLOBALS['noRecursivityForCastAction']) && $GLOBALS['noRecursivityForCastAction'])) {
        return false;
    }
    $castings = $_ENV['castings'];
    $ocn = shortClass($object);

    if (in_array($ocn, ['success', 'values'])) {
        return false;
    }

    if (is_array($className)) {
        $classNames = array_merge($className, [$ocn]);
    } else {
        $classNames = ($className) ? [$className, $ocn] : [$ocn];
    }
    ccn($classNames);

    if ($ocn == 'zend_db_profiler_query') {
        return round($object->getElapsedSecs() * 1000, 1) . '#' . $object->getQuery();
    }

    #if($className)die($className);
    #pr1([$className,$classNames, $castings]);die;

    foreach ($classNames as $className) {
        #if(!in_Array($className, ['datetime','persistentcollection']))die($className);
        if (array_key_exists($className, $castings)) {
            #pr1([$className,$classNames, $castings]);die;
            #dbe([$className,$castings,$castings[$className],$object]);
            $temp = $object;
            $parcours = $castings[$className];

            if ($ocn === 'persistentcollection') {
                if ($parcours === 'count') {
                    return count($object->getValues()) . ' values';
                }
                if ($parcours === 'unset') {
                    return 'unset';
                }
                return count($object->getValues()) . ' values';
                $vals = $object->getValues();
                $ret = [];
                foreach ($vals as $val) {
                    $ret[] = $val->$parcours();
                }
                return implode(',', $ret);
            }

            if (!$parcours || $parcours === 'unset') {
                return 'unset';
            }#==exclusino
            if (is_string($parcours)) {
                $parcours = [$parcours];
            }

            foreach ($parcours as $key => $value) {
                if (!is_int($key) && method_exists($temp, $key)) { #avec des paramétres
                    $temp = $temp->$key($value);
                    continue;
                } elseif (method_exists($temp, $value)) {
                    $temp = $temp->$value();
                } elseif (method_exists($temp, 'get' . $value)) {#getter
                    $temp = $temp->{'get' . $value}();
                } elseif (isset($temp->$value)) {
                    $temp = $temp->$value;
                }
            }
            if (0 && in_array($ocn, ['subsegment']) && gettype($temp) == 'string') {
                $GLOBALS['noRecursivityForCastAction'] = 1;
                dbe([$temp, $parcours, $object]);
            }

            #die($className.' '.$temp);
            return $temp;
        }
    }
    return false;
}

$skip = [];
function init()
{
    static $t;
    global $skip;
    if ($t) {
        return;
    }
    $t = 1;#register_shutdown_function('\bf\die3');

    if (!session_id()) {
        session_start();
    }#caution unserializing non loaded objects errors !!!
    $_SESSION['filter'] = FILTERS;
    if (!isset($_SESSION['filter'])) {
        $_SESSION['filter'] = FILTERS;
    }
    $skip = explode(',', $_SESSION['filter']);
}

function die3($x = '')
{
    if (!BFD) {
        return;
    }
    static $t;
    if ($t) {
        die;
    }
    echo $x;
    $t = 1;
    if ($_SERVER['HTTP_X_REQUESTED_WITH']) {
        return;
    }
    die('--shutdown');
}

function shortClass($obj)
{
    if (gettype($obj) === 'object') {
        $className = get_class($obj);
    } else {
        $className = 'type:' . gettype($obj);
    }
    if (strpos($className, '\\') > -1) {
        $className = explode("\\", $className);
        $className = end($className);
    }
    if (!isset($className)) {
        $className = '#undefined';
    }
    return strtolower($className);
}

function strposa($haystack, $needles = [], $offset = 0)
{
    $chr = [];
    foreach ($needles as $needle) {
        $res = stripos($haystack, $needle, $offset);
        if ($res !== false) {
            $chr[$needle] = $res;

            return $needle;
        }
    }
    if (empty($chr)) {
        return false;
    }

    return 1; #min($chr);
}

function cleanOut(&$x, $clean = null)
{
    #$x = str_replace(['\' . "\0" . \'', '\\\\'], '\\', $x);
    $x = str_replace(["\0", "\\0*\\0"], '', $x);
    $x = str_ireplace('[*', '[', $x);
    #file_put_contents('/desk/1.ser',$x);
    $x = preg_replace("~ string\([0-9]+\):~", '', $x);
    $x = str_replace(['\u0000*\u0000', '\u0000'], '', $x);
    if ($clean) {
        $y = ['_methods', '__initializer__', '__cloner__', '__isInitialized__'];
        foreach ($y as $v) {
            $x = preg_replace("~\"$v\":\"[^\"]+\",?~", '', $x);
        }
        $x = str_replace(['"__initializer__":null,', '"__isInitialized__":true,'], '', $x);
    }
    return $x;
}

function strlenOrArray($x)
{
    $type = gettype($x);
    if (in_array($type, ['array', 'object', 'unknown type'])) {
        return 1;
    }
    try {
        if (strlen($x)) {
            return 1;
        }
    } catch (Exception $e) {
        return 1;
        die($type);
    }
    return 0;
}

/** array map filter recurisve */
function amf(&$a)
{
    $type = gettype($a);
    if (in_array($type, ['function', 'object', 'unknown type'])) {
        return 1;
    } elseif ($type == 'array') {
        foreach ($a as $k => &$v) {
            amf($v);
        }
        unset($v);
        if (is_string($a)) {
            die('string:' . $a);
        }
        $a = array_filter($a, 'strlenOrArray');
        $a = count($a) ? $a : null;
    } else {
        $a = strlenOrArray($a) ? $a : null;
    }
    return $a;
}


function cleanNullOrMaxDepthArrays(&$x)
{
    $type = gettype($x);
    if ($type == 'array') {
        foreach ($x as $k => &$v) {
            cleanNullOrMaxDepthArrays($v);
        }
        $x = array_filter($x);
        unset($v);
        if (array_sum(array_map('is_numeric', $x)) == count($x)) {
            $x = implode(',', $x);
        }
    }
    if ($type == 'string' && substr($x, 0, 2) == '#!') {
        $x = null;
    }#remove the max depth
    return $x;
    #else x remains what it is
}

$_ENV['hashes'] = [];
$oi = 0;
function htmle($x)
{#surtout pas en référence ( beware of objects )
    global $oi;
    $type = gettype($x);
    if ($type == 'string' && $x[0] == '#') {
        return $x;
    }#is allready processed =) !!!
    if ($type == 'string' && (strpos($x, '>') or strpos($x, '<') !== false)) {#htmle
        if (strlen($x) > 120) {
            $x = substr($x, 0, 120);
        }
        $x = '#' . htmlentities($x);
    }
#spl_array_access
    if ($type == 'object') {
        $hash = spl_object_hash($x);
        if ($p = array_search($hash, $_ENV['hashes'])) {
            return "#<a class=recursion href='#o$p'>recursion#$p</a>";
        }
        $_ENV['hashes'][] = $hash;#die($hash);
        $class = get_class($x);
        if ($_ENV['callbacks'][$class]) {
            return $_ENV['callbacks'][$class]($x);
        }
        $_class = "#<a class=original id='o$oi'>" . $class . "#$oi</a>";
        $oi++;
        $_methods = implode(',', get_class_methods($x));
        #Cannot use object of type Fusion_View_Manager_Parameter as array
        $x = (array)$x;#ahah !!!
        $x = array_merge(compact('_class', '_methods'), $x);
        #array_unshift($x,['class'=>$_class],['methods'=>$_methods]);
        #cannot use object as array
        #$x=clone($x);
        #$x=(array)clone($x);
    }
    if (in_array($type, ['array', 'object'])) {
        /**devient no recursion !! */
        foreach ($x as $k => &$v) {
            $v = htmle($v);
        }
        unset($v);#echo",$k";
        if (is_array($x)) {
            $x = array_filter($x);
            if (count($x) == 0) {
                $x = null;
            }
        }#filtration recursive de array
        return $x;
    }
    return $x;
}
