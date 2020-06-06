<?
#implémente lecture séquentielle du fichier loremIpsum_5_chunks, permettant à chaque host de ne télécharger que les données qu'il n'aurait pas vu sur des fichiers de logs, typiquement, non processées yet
require_once '../app/fun.php';
$readEachNBytes = 447;#incluant le saut de ligne

$oks = 0;
$ok = curl($mainHost . 'fastbus/', [], ['folder' => 'benqueue'], ['user: ben', 'secret: pass', 'Cookie: XDEBUG_SESSION=1']);

if (strlen($ok['contents']) > 1 and $ok['info']['http_code'] == 200) {
    $availableMessages = explode(',', $ok['contents']);
    $oks++;

    foreach ($availableMessages as $__nb => $_msg) {
        $endIsReached = $starts = $transmitted = $nbchunks = 0;
        $response = '';
        while (!$endIsReached) {
            $nbchunks++;
            $ok = curl($mainHost . 'fastbus/', [], ['folder' => 'benqueue', 'msgid' => $_msg, 'starts' => $starts, 'len' => $readEachNBytes], ['user: ben', 'secret: pass', 'Cookie: XDEBUG_SESSION=1']);
            if ($ok['info']['http_code'] != 200) {
                die('!200');
            }
            $_c = $ok['contents'];
            #not $http_response_header
            $responseHeaders = get_headers_from_curl_response($ok['header']);
            $totalFilesize = (int)$responseHeaders['filesize'];
            $_txSize = strlen($ok['contents']);
            if ($_txSize > $readEachNBytes) {
                die('!txsizeerror');
            }
            $transmitted += $_txSize;
            if ($transmitted >= $totalFilesize) {
                $endIsReached = 1;
                $a = 1;
                $oks++;
            } else {
                $starts += $readEachNBytes;
            }
            $response .= $ok['contents'];#appens anyways
        }
        echo "\n$nbchunks for $transmitted :: " . $response;
        $a = 1;
    }
}
if ($oks == 3) {
    echo "\n\nall test successful";
} else {
    echo "\n\n!Error:some error occured: $oks";
}
die('#');

