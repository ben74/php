<?php 
/*
runkit-rewrites.php
prequisites : enable php_runkit extension on development environment

helps in putting breakpoints at lowest scope functions in order to catch / intercept specific behaviours
*/
if(function_exists('runkit_function_rename')) {
    if ('functions') {
        #todo : microtime,time
        #todo : rewrite {{ eval, base64_decode, shell_exec }} for logging potential backdoor infected websites
        function mysqli_error2($a)
        {
            $_a=$_ENV['raw'];
            $_e=mysqli_error1($a);
            if($_e){
                $a=1;
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
            $_u=$a['url'];
            if (preg_match('~image-charts\.com~', $a['url'])) {
                $b='nothing';
            } else {
                $b = 1;
            }
            return curl_exec1($ch);
        }

        function file_get_contents2($f, $b=null, $c = null, $d = null)
        {
            $a = file_get_contents1($f, $b, $c, $d);
            if (preg_match('~\.xml~', $f)) {#magento
                $e = 1;
            }
            return $a;
        }

        function file_put_contents2($f, $b, $c = null, $d = null)
        {
            if (preg_match('~thalgo\.log~', $f)) {return;}
            elseif (preg_match('~filepath.xml|thalgo\.log~', $f)) {
                $a = 1;
            }
            return file_put_contents1($f, $b, $c, $d);
        }

        function session_start2($o = [])
        {
            return session_start1($o);
        }

        function header2($a = '', $b = true, $c = 0)
        {
            if (!$isMedia and !preg_match('~\.jpg~',$a) and !preg_match('~\?image=~',$_ENV['u']) and Preg_Match('~Location: ~i', $a)) {
                $d = 1;
            }
            header1($a, $b, $c);#les 404 not found de PR également suivent ce chemin
        }
        function session_destroy2()
        {
            return session_destroy1();
        }
    }
    $funcs=explode(',','ini_set,curl_exec,file_get_contents,file_put_contents,mysqli_error,session_destroy,header,mysqli_query,session_start,mail,error_reporting,spl_autoload_register,simplexml_load_string'); 
    foreach ($funcs as $x) {
        runkit_function_rename($x, $x . '1');
        runkit_function_rename($x . '2', $x);
    }
}
