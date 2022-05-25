<?php
try {
    if ("perUserConfiguration <== you'll only have to fill in your credentials here") {
        $_ENV['swiftProject'] = 'sb_project_SBI-xxx';
        $_ENV['swiftUser'] = 'SBI-xxx';
        $_ENV['swiftPass'] = 'xxxx';
        $cacheTime = 3600;//cache d'une heure sur le listing des containers, car ce dernier peux prendre plus de temps
    }

    $_ENV['swiftUrl'] = 'https://swift02-api.cloud.infomaniak.ch';
    $timeout = 99999;
    $containerName = $_ENV['swiftDomain'] = 'default';
    $_ENV['RESOLVE'] = ['swift02-prx.cloud.infomaniak.ch:443:83.166.143.59'];//if the resolver goes down

    error_reporting(0);
    ini_set('display_errors', 0);
    date_default_timezone_set('UTC');

    $ctn = $dl = $act = null;
    if (isset($argv) && $argv) {
        array_shift($argv);
        $a = [];
        foreach ($argv as $arg) {
            if (preg_match('/^--([^=]+)=(.*)/', $arg, $m)) {
                $a[$m[1]] = $m[2];
            } elseif (preg_match('/^([^=]+)=([^=]+)/', $arg, $m)) {
                $a[$m[1]] = $m[2];
            }
        }
        extract($a);
    } elseif (isset($_GET) && $_GET) {
        extract($_GET);
    }

    chdir(__DIR__);//lock on current dir ( if cli calls )

    if (!is_dir('../cache/')) {//outside of document root or non listed or protected
        mkdir('../cache/');
    }

    if ('KonneKt') {
        $f = '../cache/.token.default.cache';
        if (is_file($f) and filemtime($f) > time()) {
            $x = fgcj($f);
            extract($x);
        } else {
            [$token, $endpoint, $expires] = swiftAutoToken($_ENV['swiftUser'], $_ENV['swiftPass'], $_ENV['swiftUrl'] . '/identity/v3', $_ENV['swiftDomain']);
            FPCJ($f, compact('token', 'expires', 'endpoint'));
            touch($f, time() + $expires - 1);
        }
    }

// phpx $bf/sb.php act=dl > 1.mp4
    if ($dl && $ctn) {// and what about the range Request pour bouger le pointeur dans le temps ???
        $f = \str_replace('+', '%20', \urlencode($dl));//\urldecode($dl);

        $starts = $ends = $fs = $ct = 0;
        $check = hash('crc32c', $ctn . $f . $_SERVER['HTTP_RANGE']);
        r304($check, time() - 3600 * 24);// Last modified: yesterday
        //header('Etag: ' . $check); // hash du file
        $sc = hash('crc32c', '0' . $ctn . $f);
        $ffs = '../cache/z-' . $sc . '.fileattr.cache';

        if (isset($_COOKIE[$sc . '404'])) {
            r404();
        }
        if (isset($_COOKIE[$sc . 'fs'])) {
            $fs = $_COOKIE[$sc . 'fs'];
            $ct = $_COOKIE[$sc . 'ct'];
        } elseif (file_exists($ffs)) {//failsafe
            $finfo = fgcj($ffs);
            $fs = $finfo['download_content_length'];
            $ct = $finfo['content_type'];
        } else {// once :)
            $fi = $endpoint . '/' . $ctn . '/' . $f;
            //$fi = $endpoint . '/default?format=json&marker=' . \urlencode($f);
            $_a = cs($fi, [CURLOPT_CUSTOMREQUEST => 'HEAD', CURLOPT_NOBODY => true]);//CURLOPT_RETURNTRANSFER => 0
            if (in_array($_a['http_code'], [0, 404])) {
                setCookie($sc . '404', $fs, time() + 3600 * 24 * 365, '/');
                r404();
            }
            $ct = $_a['content_type'];
            $fs = $_a['download_content_length'];// Interception mimetype here !!!!
            FPCJ($ffs, $_a);
            //header('Last-Modified: 1'); // hash du file
        }
        if (!isset($_COOKIE[$sc . 'fs'])) {
            setCookie($sc . 'fs', $fs, time() + 3600 * 24 * 365, '/');
            setCookie($sc . 'ct', $ct, time() + 3600 * 24 * 365, '/');
        }
        $h = [];
        if (isset($_SERVER['HTTP_RANGE'])) {// Devient très rapidement hyper greedy selon la vitesse des temps de transfert ou pas : nb avec plusieurs workers, cela pourrait aller mieux je pense ..
            [$starts, $ends] = explode('-', str_replace('bytes=', '', $_SERVER['HTTP_RANGE']));
            //if (!$ends) $ends = $fs;
            $h = ['Range: bytes=' . $starts . '-' . $ends];
        }

        header('A: ' . $check);
        header('Content-type: ' . $ct);
        if (in_array($ct, ['video/mp4', 'audio/mp3'])) {
            if (!$ends) $ends = intval($fs);
            header('Content-Range: bytes ' . $starts . '-' . ($ends - 1) . '/' . $fs);
            header('Content-Length: ' . $fs); // hash du file
            header('HTTP/1.1 206 Partial Content', 1, 206); // hash du file
            header('Accept-ranges: bytes');
        }

        $fi = $endpoint . '/' . $ctn . '/' . $f;//Passthrough
        cs($fi, [CURLOPT_RETURNTRANSFER => 0, CURLOPT_WRITEFUNCTION => function ($c, $data) {
            echo $data;
            return strlen($data);
        }], $h);
        die;
    }

    if ("wrapper tout cela au sein d'un datatable avec interface de listing et proposer de télécharger les contenus") {
        if ('list All Containers') {
            $ctn = cs($endpoint . '/?format=json');// max updated date
        }

        if ('listContainer files') {
            foreach ($ctn as $t) {
                $f = '../cache/z-' . $t['name'] . '.container.listfiles.cache';
                // last_modified -> non reliable for listing files
                //$d = time() - strtotime($t['last_modified']);// date de création, ou renommage du container !! :)
                // time()-strtotime('2022-05-25T11:31:24');
                //if (is_file($f) and ($diff = filemtime($f) - strtotime($t['last_modified'])) and $diff > 0) {
                if (is_file($f) and ($diff = time() - filemtime($f)) and $diff < $cacheTime) {
                    $res[$t['name']] = fgcj($f);
                } else {
                    $x = cs($endpoint . '/' . $t['name'] . '?format=json');
                    foreach ($x as &$t2) {
                        if ($t2['content_type'] == 'application/directory') {
                            $t2 = null;
                        }
                    }
                    unset($t2);
                    $x = array_values(array_filter($x));
                    FPCJ($f, $x);
                    $res[$t['name']] = $x;
                }
            }
        }
    }
} catch (\throwable $___e) {
    r404('Erreur :' . $___e->getLine() . ';;' . $___e->getMessage(), false);
}
?>
    <html id="h">
    <head>
        <script id="sc1">var get = <?=json_encode($_GET)?>, post = <?=json_encode($_POST)?>;</script>
        <title>SB</title>
        <link rel="icon" href="/z/bird.png" sizes="any" type="image/png"/>
        <link rel="stylesheet" href="/z/fonts.css"/>
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css"/>
        <style id="unicornsOnRails">
            body, table {
                margin: auto;
            }

            #h #data_filter input, #h .paginate_button {
                background: #000;
                color: #FFF !important;
            }

            html {
                font-size: 10px;
            }

            table, option {
                color: #000;
            }

            body {
                margin: auto;
                max-width: 95vw;
                overflow-x: hidden;
                background: url('./z/b1.jpg') #000;
                color: #FFF;
            }

            body, select, option, input, textarea {
                font: 1.2rem "Avenir Next", "Source Code Pro", "Roboto Mono";
                font-weight: 200;
            }

            table {
                font-size: 1.2rem;
                border-collapse: collapse;
                background: rgba(255, 255, 255, 0.7);
            }

            .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate {
                color: #FFF;
            }

            table.dataTable thead th, table.dataTable thead td,
            table.dataTable tbody th, table.dataTable tbody td {
                padding: 0.2rem 0 0.2rem 0.2rem;
            }

            #h table.dataTable tbody tr {
                background: transparent;
            }

            #h .dataTables_wrapper .dataTables_length select, #h .dataTables_wrapper .dataTables_filter input {
                w
                color: #FFF
            }

            #h .sep {
                width: 5px;
                background: #000;
                padding: 0px;
            }

            td .svg {
                height: 20px
            }

            a img {
                transition: all .5s ease-in-out;;
                transform: rotate(-6deg) scale(1);
            }

            a:hover img {
                opacity: 0.5;
                transform: rotate(6deg) scale(1.5);
            }

            a:hover img.php {

                transform: rotate(12deg) scale(3);
                filter: hue-rotate(180deg);
            }

            #h a {
                display: inline-block;
                background: transparent;
            }

            #h #b .hidden {
                display: none
            }

            a[title], label {
                cursor: pointer
            }

            textarea {
                font-size: 1.4rem;
                background: #000;
                color: #0F0;
            }

            #processCount {
                width: 70vw;
                height: 50px;
            }

            #log {
                width: 100%;
                height: 25vh;
            }

            .locked .launch, #h .v2 .launch {
                display: none;
            }

            #h .v2 .dry {
                display: none;
            }


            a, a:visited {
                color: #D00;
                text-decoration: none;
            }

            #data_filter, #data_length {
                display: inline-block;
            }

            #h #b tr.migr1 {
                background: rgba(230, 208, 255, 0.74);
            }

            #h #b tr.migr2 {
                background: rgba(211, 208, 255, 0.74);
            }

            #h #b tr.migr3 {
                background: #75bfff;
            }

            form {
                display: inline
            }

            .f1 input {
                width: 7rem;
                background: #000;
                color: #0F0;
            }

            .submod:hover {
                cursor: pointer;
                background: #F00;
            }

            .user {
                text-align: right
            }

            /*é55,255?255,07*/
            .toV1.v1, .toV2.v2 {
                display: none;
            }

            pre {
                max-width: 90vw;
                word-break: break-all;
                white-space: break-spaces;
            }

            button {
                cursor: pointer
            }
        </style>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"
                integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    </head>
    <body>
    <table border="1" id="data">
        <thead>
        <tr>
            <th>Con</th>
            <th>Chemin</th>
            <th>Size</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($res as $cn => $files) {
            foreach ($files as $file) {
                echo "<tr><td>" . $cn . "</td><td><a target=craazy href='?ctn=" . $cn . "&dl=" . \urlencode($file['name']) . "'>" . $file['name'] . "</a></td><td>" . $file['bytes'] . "</td><td>" . $file['last_modified'] . "</td></tr>";
            }
        }
        ?>
        </tbody>
    </table>
    <script>
        var r = [], x, desiredVersion = 0, t, refresh = 1, rien, maxProcess = 2, si = {log: {}}, datat, el, cl = console.debug,
            voice = new SpeechSynthesisUtterance(),
            v, tr = 0, locks = {},
            p = {}, p2 = {
                "oLanguage": {"sSearch": "dynamic search:"},
                "searching": true,
                "lengthChange": true,
                "paging": true,
                "info": false,
                "pageLength": 100,
                "order": [[3, "desc"]]//, [11, "asc"]
//,"columnDefs": [{"searchable": false, "targets": [0,1,2,11,12,13,14,15] },{"orderable": false,   'targets': [0,1,2,11,12,13,14,15] },{ "type": 'natural-nohtml', targets: [3,4,5,6,7,8,9,10] }]
                , initComplete: function () {
                    if (0) {
                        this.api().columns([5/*, 10, 12*/]).every(function () {
                            var column = this;
//$(column.footer()).empty();
//cl('column initcomplete',this);
                            var select = $('<select><option value="">All</option></select>');
                            column.data().unique().sort().each(function (d, j) {
                                if (!d) return;
                                select.append('<option value="' + d + '">' + d + '</option>')
                            });
                            select
                                .appendTo($(column.header()))
                                .on('change', function () {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                });
                        });
                    }

                    //$('.dataTables_length').append(' &nbsp; <b class=but><a href="?v=1">v1</a> <a href="?v=2">v2</a> <a href="?v=0">all</a> </b> <form target=a2>R1:<input name="rl1"/><button>Go</button></form> <form target=a2>R2:<input name="rl2"/><button>Go</button></form>');
                }
            }

        window.onload = function () {
            datat = $('#data').DataTable(p2);
        }

    </script>
    </body>
    </html>
<?
return;

function fgcj($f)
{
    if (!is_file($f)) return [];
    return json_decode(file_get_contents($f), true);
}

function FPCJ($f, $x)
{
    return file_put_contents($f, json_encode($x));
}

function cs($url, $opts = [], $headers = [], $timeout = 99999)
{
    global $token;
    if ($headers) {
        $a = 1;
    }
    $headers[] = 'X-Auth-Token: ' . $token;
    $c = \curl_init();
    $o = [CURLOPT_DNS_USE_GLOBAL_CACHE => true, CURLOPT_DNS_CACHE_TIMEOUT => 99999, CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4, CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => $timeout, CURLOPT_PROTOCOLS => 3, CURLOPT_HTTP_VERSION => 2, CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_URL => $url, CURLOPT_HTTPHEADER => $headers];
    if (isset($_ENV['RESOLVE'])) {
        $o[CURLOPT_RESOLVE] = $_ENV['RESOLVE'];
    }
    foreach ($opts as $k => $v) $o[$k] = $v;
    \curl_setopt_array($c, $o);
    $res = \curl_exec($c);
    $err = \curl_error($c);
    $i = \curl_getinfo($c);
    if ($err) {
        throw new \Exception($err);// Could not resolve host:
    }
    if (!$res) return $i;
    return json_decode($res, true);
}

function r304($etag, $mtime = 1, $expiration = 900000)
{
    $date = gmdate('D, j M Y H:i:s', $mtime) . ' GMT'; #die;
    if (
        (isset($_SERVER['HTTP_IF_NONE_MATCH']) and $_SERVER['HTTP_IF_NONE_MATCH'] == $etag)
        or (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) and $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $date)) {
        header('HTTP/1.1 304 Not Modified', 1, 304);
        die;
    }
    header('Etag: ' . $etag, 1);
    header('Cache-Control: public, max-age=' . $expiration, 1);
    header('Last-Modified: ' . $date, 1);
    $date2 = gmdate('D, j M Y H:i:s', time() + $expiration) . ' GMT';
    header('Expires: ' . $date2, 1);
}

function r404($x = '', $redir = true)
{
    header('HTTP/1.0 404 Not Found', 1, 404);
    echo '/*' . $x;
    if (!$redir) die('*/');
    die('<a href="/">Nicht gefunden : ' . trim($_SERVER['REQUEST_URI'], ' */') . ' </a><script>location.href="/#' . str_replace('"', '', $_SERVER['REQUEST_URI']) . '";</script>*/');
}

function swiftAutoToken($user, $pass, $auth = 'https://swift02-api.cloud.infomaniak.ch/identity/v3', $domain = 'default')
{
    $post = ['auth' => ['identity' => ['password' => ['user' => ['name' => $user, 'password' => $pass, 'domain' => ['name' => $domain]]], 'methods' => [0 => 'password']]]];
    return getToken($auth, $post);
}

function getToken($baseURI, $post, $timeout = 120)
{//CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_SSL_VERIFYHOST => 0,
    $o = [CURLOPT_USERAGENT => 'x24.fr', CURLOPT_FOLLOWLOCATION => 1, CURLOPT_HEADER => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => $timeout, CURLOPT_CONNECTTIMEOUT => $timeout, CURLOPT_POST => 1, CURLOPT_URL => $baseURI . '/auth/tokens', CURLOPT_POSTFIELDS => json_encode($post), CURLOPT_HTTPHEADER => ['Expect:', 'Accept-Encoding:', 'User-Agent: x24.fr', 'Content-Type: application/json', 'Accept:']];
    $c = curl_init();
    curl_setopt_array($c, $o);
    $__a = curl_exec($c);
    $__i = curl_getinfo($c);
    if ($__i['http_code'] != 201) {
        throw new Exception('getToken:' . $__i['http_code']);
    }
    $e = curl_error($c);
    if ($e) {
        throw new Exception('getToken:' . $e);
    }

    $endPointUrl = '';
    $header_size = $__i['header_size'];
    $headers = getHeaders(substr($__a, 0, $header_size));
    $__body = json_decode(substr($__a, $header_size), true);
    //$projectId = "AUTH_" . $__body["token"]["project"]["id"];
    $catalogs = $__body['token']['catalog'];
    foreach ($catalogs as $catalog) {
        if ($catalog['type'] == 'object-store') {
            $endPointUrl = $catalog['endpoints'][0]['url'];
            break;
        }
    }
    $expiresIn = strtotime($__body['token']['expires_at']) - time();
    $token = $headers['X-Subject-Token'];
    return [$token, $endPointUrl, $expiresIn];
}

function getHeaders($response)
{
    if (!preg_match_all('/([A-Za-z\-]{1,})\:(.*)\\r/', $response, $matches)
        || !isset($matches[1], $matches[2])) {
        return false;
    }

    $headers = [];

    foreach ($matches[1] as $index => $key) {
        $headers[$key] = trim($matches[2][$index], ' "');
    }

    return $headers;
}

die;
return; ?>