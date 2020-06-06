<?php

class baseObject
{
    static $nbinstances = 0, $instances = [];
    public $data = [], $quick = 1;#not sharing static scope

    /*private function __construct(){} self is shared for every instances*/
    function __construct($parameters = [])
    {
        static::$nbinstances++;#increments on each construction
        if (!isset(static::$instances[static::class])) {#instance of who ????
            static::$instances[static::class] = [];
        }
        static::$instances[static::class][] = $this;
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
        $this->uuid = static::class . '/' . uniqid();

    }

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
    /*
    public function __get($k)
    {
    if (isset($this->data[$k]) && 'private') {
    return $this->data[$k];
    }
    if (!$this->quick && isset(static::$dataS[$k])) {
    return static::$dataS[$k];
    }
    return null;
    }

    public function __set($k, $v)
    {
    $this->data[$k] = $v;
    if(!$this->quick){static::$dataS[$k] = $v;}
    return static::me();
    }
    */
#static part then
#public $uuid=null,$defined = __file__;#$who = 'base', #could be overrided as default
    static $dataS = [];#is shared

    /** return static::me();returns itself from static context*/
    static function me()
    {
        if (isset(static::$instances[static::class])) {#verschiedene Instanzen ..
            return reset(static::$instances[static::class]);#singleton : renvoie la première instance crée ;)
        }
        return null;
    }

    /** get instance
     * $a=self;#as undefined constant self=='self'???? self::i()
     */
    static function i($parameters = null)
    {
        $c = static::class;
        $me = static::me();
        if ($me && 'returns first instance as singleton') {
            return $me::registerParameters($parameters);
        }
        $i = new static($parameters);
        #static::$instances[static::class][] = $i;#nouvelle instance crée
        return $i;#otherwise build the first instance !
    }

    /** new instance */
    static function add($parameters = null)
    {
        $c = static::class;
        $i = new static($parameters);
        #static::$instances[static::class][] = $i;
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
#$class=static::class;if(!isset(static::$instances[$class]))static::$instances[$class]=$this;else static::$additional[$class][]=$this;
    }

    static function id()
    {
        return;#useless : use uuid instead ;)
        $a = 1;
        return;
        return static::uuid;#$this->
        $ash = spl_object_hash(static::class);#cant get this as static
        return $ash;
    }
}#end base :: generic object
