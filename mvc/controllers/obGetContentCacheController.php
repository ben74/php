<?php
class obGetContentCacheController extends base{}
require_once 'app/common.php';
#nocache();
$cachepath='z/cache/cachepath.html';
$validity=3600;

$title='ob get contents cache';
require_once 'z/header.php';


if(is_file($cachepath) && filemtime($cachepath)>(time()-$validity)){
    echo'<!-- cached @ '.date('Y/m/d H:i:s').'-->';
    readfile($cachepath);
}else{?>

<fieldset><legend><?="cached at ".date('Y/m/d H:i:s');?></legend>
HTML output of the page using tons of mysql and entities taking such a long time you'll want this block to be cached at least for one hour so your website might serve content instead of enabling such a ddos gate, crashing mysql because lots of connexions, dead process pending sql response, so much apache processes causing crashing at each request, having a load_avg above 99 and opening the road to main system OOM ;)
</fieldset>
<?

file_put_contents($cachepath,ob_get_contents());
}
return;
