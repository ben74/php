<?php
class funWithOopController extends base{}
require_once 'app/common.php';
#nocache();

class child extends baseObject
{
    static $nbinstances = 0, $instances = [];#if you don't want to share with parent, brother and sister instances ;)
    #dull class ;)
}

/*do not print_r recursivity into 'instances' !!*/
$_ENV['objData'] = 1;
#make sure each class herits of baseObject
if ('singleton check') {
    $b[] = $b1 = baseObject::i(['a' => 1]);
    $b[] = $b2 = baseObject::i(['b' => 2]);
    $b1->setVar1(1);
    $b2->setVar2(2);
    if (!spl_object_hash($b1) == spl_object_hash($b2)) {#same objects : singleton
        die('#' . __line__);
    }
}

$b3 = baseObject::me(['c' => 3]);#b4 = b1 & b2
$b4 = baseObject::add(['d' => 4]);#is Different as second instance :)
$b5 = new baseObject(['e' => 5]);#different too
$b6 = child::i(['f' => 6]);#appears in instances
if (spl_object_hash($b1) == spl_object_hash($b5)) {#objects are different
    die('#' . __line__);
}



if (isset($argv)) {#Http served
    print_r($b1);
} else {
    $title='oop fun';
    require_once 'z/header.php';
    echo \debugger\debugger::d([$b1, $b2, $b3, $b4, $b5, $b6]);
}
die(':ok');
