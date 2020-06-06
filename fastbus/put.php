<?
#phpx put.php
require_once '../app/fun.php';
$oks = 0;
$nbTests = 6;#callback and mirror
$mirror = $mainHost . 'fastbus/mirror.php';
$p = ['folder' => 'benqueue', 'msgid' => date('YmdHis'), 'content' => "\ncontent:" . date('YmdHis'), 'mirror' => json_encode([$mirror]), 'callback' => 'hyphenWrapper'];
$h = ['user: ben', 'secret: pass', 'CustomHeader: PrivateValue', 'Cookie: XDEBUG_SESSION=1'];
$mirrorTimeout = 20;

$mirrored=curl($mirror, [], $p, $h, $mirrorTimeout);
if ($mirrored['contents'] == 'mirrored:175' and $mirrored['info']['http_code'] == 200) {
    $oks++;
}else{$errors[]='mirrored:'.$mirrored['contents'];}

$expectedResponse = 'ok:27,mirrorsok:1';
$callbackEtMirroirs = curl($mainHost . 'fastbus/', [], $p, $h);
if ($callbackEtMirroirs['contents'] == $expectedResponse and $callbackEtMirroirs['info']['http_code'] == 200) {
    $oks++;
}else{
    $errors[]='callbackEtMirroirs:'.$callbackEtMirroirs['contents'];
}

#no mirror ok
$brokenMirror = curl($mainHost . 'fastbus/', [], ['folder' => 'benqueue', 'msgid' => date('YmdHis'), 'content' => "\ncontent:" . date('YmdHis'), 'mirror' => json_encode(['borkenMirror'])], ['user: ben', 'secret: pass', 'Cookie: XDEBUG_SESSION=1']);
if ($brokenMirror['contents'] == 'ok:23' and $brokenMirror['info']['http_code'] == 200) {
    $oks++;#http_code:0, error:Operation timed out after 10000 milliseconds with 0 bytes received
}else{$errors[]='brokenmirror:'.$brokenMirror['contents'];}

#

$ko = curl($mainHost . 'fastbus/', [], ['folder' => 'benqueue', 'msgid' => date('YmdHis'), 'content' => "\ncontent:" . date('YmdHis'), 'mirror' => json_encode(['saispas']), 'callback' => 'nonexist'], ['user: ben', 'secret: bad', 'CustomHeader: PrivateValue', 'Cookie: XDEBUG_SESSION=1']);
if ($ko['contents'] == '!pass' and $ko['info']['http_code'] == 404) {
    $oks++;
}else{$errors[]='ko';}

$ko2 = curl($mainHost . 'fastbus/', [], ['folder' => 'benqueue', 'msgid' => date('YmdHis'), 'content' => "\ncontent:" . date('YmdHis'), 'callback' => 'dead'], ['user: nf', 'secret: bad', 'CustomHeader: PrivateValue', 'Cookie: XDEBUG_SESSION=1']);#t
if ($ko2['contents'] == '!user' and $ko2['info']['http_code'] == 404) {
    $oks++;
}else{$errors[]='ko2';}

$ko3 = curl($mainHost . 'fastbus/', [], ['folder' => 'benqueue', 'msgid' => date('YmdHis'), 'content' => "\ncontent:" . date('YmdHis'), 'callback' => 'dead'], ['user: ben', 'secret: pass']);#t
if ($ko3['contents'] == '!callback:dead' and $ko3['info']['http_code'] == 404) {
    $oks++;
}else{$errors[]='ko3:'.$ko3['contents'];}

if ($oks == $nbTests) {
    echo "\n\nall test successful";
} else {
    echo "\n\n!Error:some error occured:".count($errors);
    print_r($errors);
}
die('#');
