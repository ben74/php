<?php
require_once 'app/common.php';
class funWithOopController extends base{}
#nocache();

class child extends base
{
    static $nbinstances = 0, $instances = [];#if you don't want to share with parent, brother and sister instances ;)
    #dull class ;)
}

/*do not print_r recursivity into 'instances' !!*/
$_ENV['objData'] = 1;
#make sure each class herits of base
if ('singleton check') {
    $b[] = $b1 = base::i(['a' => 1]);
    $b[] = $b2 = base::i(['b' => 2]);
    $b1->setVar1(1);
    $b2->setVar2(2);
    if (!spl_object_hash($b1) == spl_object_hash($b2)) {#same objects : singleton
        die('#' . __line__);
    }
}

$b3 = base::me(['c' => 3]);#b4 = b1 & b2
$b4 = base::add(['d' => 4]);#is Different as second instance :)
$b5 = new base(['e' => 5]);#different too
$b6 = child::i(['f' => 6]);#appears in instances
if (spl_object_hash($b1) == spl_object_hash($b5)) {#objects are different
    die('#' . __line__);
}



if (isset($argv)) {#CLI
    print_r($b1);
} else {
    $title='oop fun';
    require_once 'z/header.php';
    #echo'<pre>';print_r([$b1, $b2, $b3, $b4, $b5, $b6]);
    echo \debugger\debugger::d([$b1, $b2, $b3, $b4, $b5, $b6]);
}
die(':ok');
