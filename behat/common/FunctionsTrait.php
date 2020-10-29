<?php

/** anything which is not behat relative */
trait FunctionsTrait
{
    /** translate mangeto locale for using the right  code, especially for  !! */
    public function Magento_Locales($_site, $code)
    {
        return[];
    }

    /** Generic functions below, could be moved in another file, such as Functions */

    /**
     * @param $start
     * returns ms since first timestamp
     *
     * @return float
     */
    public function gt($reset = 0)
    {
        static $start = 0;
        if ($reset or !$start) {
            $start = microtime(1);
            return $start;
        }
        return round((microtime(1) - $start) * 1000);
    }

    /***
     * returns an array of any passed option in the format --key=value
     *
     * @return array
     */
    public function parseOptions()
    {
        $res = [];
        foreach ($_SERVER['argv'] as $x) {
            if (substr($x, 0, 2) == '--' and strpos($x, '=')) {
                list($k, $v) = explode('=', $x);
                $res[trim(substr($k, 2))] = trim($v);
            }
        }
        return $res;
    }


    /**
     * handles relative urls
     * @param $url
     */
    public function relativeUrl($url)
    {
        if (strpos($url, 'ttp') === false) {
            if (substr($url, 0, 1) == '/') {
                $url = $_SESSION['basehost'] . $url;
                return $url;
            }
            #where /$lang/ is allready included within
            $url = $_SESSION['baseurl'] . $url;
            return $url;
        }
        return $url;
    }

    /**
     * @param $url
     * returns hostname
     */
    public function basehost($url)
    {
        $p = parse_url($url);
        if (!$_SESSION['lang']) {
            $_SESSION['lang'] = strtolower(trim($p['path'], '/'));
        }
        return $p['scheme'] . '://' . $p['host'];

    }
}
