<?php

class baseObject
{
#redeclare in child if you want these data to be shared
    static $nbinstances = 0, $instances = [], $dataS = [];

    public $data = [], $quick = 1;#not sharing static scope
#public $uuid=null,$defined = __file__;#$who = 'base', #could be overrided as default
    /*private function __construct(){} self is shared for every instances*/

    function __construct()
    {
        $class = static::gc();
        $_ENV['_nbi'][$class]++;
        $p=func_get_args();
        if($p){$a=1;if(is_array($p) and count($p)==1 and is_numeric(array_keys($p)[0]))$p=reset($p);$this->set($p);}
        $_ENV['_obj'][$class][] = $this;
    }
    /*
    function __construct($parameters = [])
    {
        static::$nbinstances++;#increments on each construction
        if (!isset(static::$instances[static::gc()])) {#instance of who ????
            static::$instances[static::gc()] = [];
        }
        static::$instances[static::gc()][] = $this;

        if ($parameters) {
            if ($_ENV['objData'] && $parameters != $this->data) {
                $this->data = $parameters;
            } elseif (!$_ENV['objData']) {
                foreach ($parameters as $k => $v) {
                    $this->{$k} = $v;
                }
            }
        }
        if ($this->quick) {
            return;
        }
        $this->uuid = static::gc() . '/' . uniqid();

    }
*/
    /*appels functions not set=> NO STATIC HERE IN MAGIC METHODS BORDEL
    get static context out of this
    self::i([$reste=>$reset]);
    */
    function __call($name, $args = [])
    {
        $base = $name;
        $reset = reset($args);
        if (!$reset) {
            $reset = null;
        }
        if (!$args) {
            $args = null;
        }
        if (1) {#pass to possible getter && setters defined later ..
            $name = strtolower(str_replace('_', '', $base));
            $matches = [$name, 'get' . $name, 'set' . $name];
            foreach ($matches as $method) {
                if (method_exists($this, $method)) {
                    return $this->$method($args);
                }
            }
        }
#die("\n__CALL:".$name.":\n".print_r(compact('args'),1));
        if (!$args && 'possible getter') {
            $name = strtolower($base);
            if (array_key_exists($name, $this->data) && 'private') {
                return $this->data[$name];
            }
            if (!$this->quick && array_key_exists($name, self::$dataS) && 'shared') {
                return self::$dataS[$name];
            } #obj->name();
        }
#aliases
        $aliases = ['gettitre' => 'getnom'];
        foreach ($aliases as $souhait => $exists) {
            if ($name == $souhait && method_exists($this, $exists)) {
                return $this->$exists($args);
            }
        }

        if ($name == 'get' && $reset) {
            if (array_key_exists($reset, $this->data) && 'private') {
                return $this->data[$reset];
            }
            if (!$this->quick && array_key_exists($reset, self::$dataS) && 'shared') {
                return self::$dataS[$reset];
            }
        }
        if ($name == 'set' && $args && $args[0] && $args[1]) {
            $this->data[$args[0]] = $args[1];
            if (!$this->quick) {
                self::$dataS[$args[0]] = $args[1];
            }
            return $this;
        }
#getters, setters not found
        $reste = substr($name, 3);
        if (substr($name, 0, 3) == 'get') {
            if (isset($this->$reste)) {
                return $this->$reste;
            }
            if (array_key_exists($reste, $this->data) && 'private') {
                return $this->data[$reste];
            }
            if (!$this->quick && array_key_exists($reste, self::$dataS) && 'shared') {
                return self::$dataS[$reste];
            }
        }
        if (substr($name, 0, 3) == 'set') {
            $this->data[$reste] = $reset;
            if (!$this->quick) {
                self::$dataS[$reste] = $reset;
            }
            return $this;
#get static context out of this
#self::i([$reste=>$reset]);
#static::dataS[$reste] = $reset;
#$this->$reste = reset($args);
        }
#global namespaces functions
        if (in_array($name, ['ajoutsessionpubaffiliation', 'create_centre_fiches_offre_fiche'])) {
            return;
        }
#callto function if exists or return object
        if (function_exists($name)) {
            return call_user_func('\\' . $name, $args[0]);
        }
#so thats a magic setter
        if ($args) {
            $this->data[$name] = $reset;
            if (!$this->quick) {
                self::$dataS[$name] = $reset;
            }
        }
        return null;#Ne jamais retourner l'objet si échec
    }

    /** return static::me();returns itself from static context*/
    static function me()
    {
        if (isset(static::$instances[static::gc()])) {#verschiedene Instanzen ..
            return reset(static::$instances[static::gc()]);#singleton : renvoie la première instance crée ;)
        }
        return null;
    }

    /** get instance
     * $a=self;#as undefined constant self=='self'???? self::i()
     */
    static function i($p = null)
    {
        $class = static::gc();
        if (!isset($_ENV['_obj'][$class])) {# creates one
            $o = new static;
            #$reflector = new ReflectionClass($class);$o = $reflector->newInstanceArgs($p);
        } else {
            $o = reset($_ENV['_obj'][$class]);
        }
        if($p)$o->setOrGetKv($p);
        return $o;
    }
    /*
        static function i($parameters = null)
        {
            $c = static::gc();
            $me = static::me();
            if ($me && 'returns first instance as singleton') {
                return $me::registerParameters($parameters);
            }
            $i = new static($parameters);
            #static::$instances[static::gc()][] = $i;#nouvelle instance crée
            return $i;#otherwise build the first instance !
        }
    */
    /** new instance */
    static function add($parameters = null)
    {
        $c = static::gc();
        $i = new static($parameters);
        #static::$instances[static::gc()][] = $i;
        return $i;
    }

    /** self::i() */
    static function registerParameters($parameters)
    {
        $a = 1;#this->scope ???
        $me = static::me();
        if (is_array($parameters) && $parameters != $me->data && 'if parameters are the same, please do nothing => becomes private once different') {
            if (!$me->quick) {
                static::$dataS = array_merge(static::$dataS, $parameters);
            }#commondata}
            $me->data = array_merge($me->data, $parameters);#"private"
#foreach ($z as $k => $v) {$this->$k = $v;}
        } #register those parameters if passed
        return $me;#returns itself, defined in construct
#$class=static::gc();if(!isset(static::$instances[$class]))static::$instances[$class]=$this;else static::$additional[$class][]=$this;
    }

    static function id()
    {
        return;#useless : use uuid instead ;)
        $a = 1;
        return;
        return static::uuid;#$this->
        $ash = spl_object_hash(static::gc());#cant get this as static
        return $ash;
    }

#instead of static::class for php<5.6 compactibility
    static function gc()
    {
        return get_called_class();
        /*if(PHP_VERSION_ID < 56000)return get_called_class();syntax error, unexpected 'class' (T_CLASS), expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$'
        else{return static::class;}*/
    }

    function __invoke()
    {

    }

#appel statique vers une fonction publique ?
    static function __callStatic($a, $b)
    {
        $instance = static::i();
        return $instance->{$a}($b[0]);
        #set singleton value
    }

    function __wakeup()
    {#unserialize
        $class = static::gc();
        if ($class == 'plBasket') {
            $a = 1;
        }
        #$_ENV['_nbi'][static::class]++;
        $_ENV['_obj'][$class][] = $this;
    }

#on non-existing : get from collective self::$data ?
#avoid the error [type] => 1,[message] => Cannot access empty property, if  $k=''; and $object->$k is used somewhere ..
    function __get($k)
    {
        return null;#todo: decorate not set or in data ???
        $k = strtolower($k);
        return $this->get($k);
    }

    /* xdebug interceptions */
    function get($k=null)
    {#get on static ???
        if(!isset($this)){$el=static::i();} else{$el=$this;}
        if(!$k){#todo :: list all
            return get_object_vars($el);#use reflector for private properties
        }
        if($k){
            if (!isset($el->$k)) {
                return null;
            }
            return $el->$k;
        }
    }

    function set($k, $v = null, $hydrate = 0, $_newer = 0, $virtual = 0)
    {
        if(!isset($this)){$el=static::i();} else{$el=$this;}

        if(is_array($k)){
            foreach($k as $k2=>$v2){
                $el->set($k2,$v2);
            }
            return $el;
        }
        $el->$k = $v;
        return $el;
    }

#on non-existing, loss of proper backtrace
    function __set($k, $v)
    {
        #$this->set($k,$v);#loops
        $this->$k = $v;
        return $this;
    }

    function push($array, $k = null, $v = null)
    {
        $a = 1;
        if (is_null($v) and $k) {
            $v = $k;
            $k = null;
        }#simple push
        if (!$k and $k !== 0) {
            $err = 'no keys';
        }
        if (!isset($this->{$array})) {
            $this->{$array} = [];
        }
        if (is_null($k) or (!$k and $k !== 0)) {
            $this->{$array}[] = $v;
        } else {
            $this->{$array}[$k] = $v;
        }
    }

    function setOrGetKv($p = null)
    {
        if (!$p) {
            return $this;
        }

        if (is_array($p)) {
            foreach ($p as $k => $v) {
                $this->{$k} = $v;
            }
            return $this;
        } elseif (isset($this->{$p})) {
            return $this->{$p};
        } else {
            return null;
        }
    }

}#end base :: generic object
