<?php //  php -ddisplay_errors=1 bases.php

set_error_handler(function($errno,$errstr,$errfile,$errLine,$errContext=null){echo 'Error:'.json_encode([$errno,$errstr,$errfile,$errLine,$errContext]);return true;}, E_ALL);
set_exception_handler(function (\Throwable $e) {echo "\n#exception: " . $e->getMessage();return true;});

try {
    $f = 'vendor/autoload.php';
    if (is_file($f)) require_once $f;
    require_once 'triggerError.php';
}catch(\Throwable $e){
    echo "\nException Catched :" . $e->getFile() . '@' . $e->getLine().' :: '.$e->getMessage();
}


return ['a'=>'bob'];// $result=require_once'bases.php';
die;
?>
