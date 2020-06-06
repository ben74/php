<?
require_once '../app/fun.php';
#simple reflexions
if ($_POST) {
    $j = json_encode($_POST);
    file_put_contents('logs/mirrored.log', "\n" . $j, 8);
    ok('mirrored:' . strlen($j));
}
ko('!wtf');

function ok($x){die($x);}
function ko($x){header('HTTP/1.0 404 Not Found', 1, 404);die($x);}
