<?php
/*
1: input file onchange ==> js split file per chunk & calcul sha1 total & calcul sha1 sur chaque chunk
2: si un chunck ne renvoie pas le même sha1 on le renvoie ( jamais observé personnellement )
3: un cookie vuuid:$checkSumFichier = $UID se crée, permettant le resume de l'upload si interompu, failed, closed etc .. tu peux revenir dessus, si le cookie est toujours là, il va le trouver et donc de facto resumer ce dernier ;)
4: concat file sur fin upload, renvoie le sha1 final, si match, passe sur l'upload openstack
5: si le hash(md5) de l'upload openstack n'est pas le bon on ré-essaye
6: bingo
 */

$algo = 'SHA-1';
$algo2 = 'sha1';
$chunkSize = 1000000;#1mo
$uploadDir = 'uploads';

if (0) {#openstack swift configuration and examples
    $osChunckSize = 104857600;# if file > 100mo , split it into 100Mo chuncks
    $u = 'user';
    $p = 'passs';
    $pn = 'projectname';
    $osConf[$osc] = ['url' => 'url', 'user' => ['domain' => ['name' => 'Default'], 'name' => $u, 'password' => $p],
        'authUrl' => 'url', 'projectName' => $pn];# optional: , 'region' => 'RegionOne',
    $osc = 'default';
    $containerName = 'container';
    $objId = 'swiftObjId';
    $localfile = 'yop.mp4';
    #functions :
    $isSwiftUploadValid = 0;
    up($osc, $containerName, $objId, $localfile);#up
    while (!$isSwiftUploadValid) {
        exists($osc, $containerName, $objId);#checks if exists return object for stats
        $md5 = md5_file($localfile);
        $c = exists($osc, $containerName, $objId, 1);
        $c->retrieve();
        if ($c->hash == $md5) {
            $isSwiftUploadValid = 'uploaded File is Valid !!!';# <============
        } else {
            up($osc, $containerName, $objId, $localfile);#up
        }
    }
    osget($osc, $containerName, $objId, $localfile);#retrieve
    osdel($osc, $containerName, $objId);#delete
    up($osc, $containerName, $objId, $localfile);#up
    oslist($osc, $containerName);#list container contents
}

header('X-Content-Type-Options: nosniff');
header('Cache-control: no-cache');

if (isset($_POST['list'])) {#for resuming upload ?
    $x = glob('uploads/' . $_POST['list'] . '*.part');
    $t2 = [];
    foreach ($x as $t) {
        $t = explode('-', $t);
        $t2[] = intval(str_replace('.part', '', end($t)));
    }
    die(implode(',', $t2));
    #60851de5723a7
}

if (isset($_POST['sum'])) {
    $x = glob($uploadDir . '/' . $_POST['sum'] . '*.part');
    $fileToAppendTo = 'uploads/' . str_replace(['.php'], '', $_POST['dest']);
    @unlink($fileToAppendTo);
    foreach ($x as $v) {
        file_put_contents($fileToAppendTo, file_get_contents($v), 8);
        unlink($v);
    }
    $hash = hash_file($algo2, $fileToAppendTo);
    if ($_POST['expectedTotalFinalCheckum'] == $hash) {
        if (0 and 'upload final file on swift if valid') {
            $isSwiftUploadValid = 0;
            up($osc, $containerName, $objId, $localfile);#up
            while (!$isSwiftUploadValid) {
                exists($osc, $containerName, $objId);#checks if exists return object for stats
                $md5 = md5_file($localfile);
                $c = exists($osc, $containerName, $objId, 1);
                $c->retrieve();#nécessaire à l'obtention des infos :)
                if ($c->hash == $md5) {
                    $isSwiftUploadValid = 'uploaded File is Valid !!!';# <============
                } else {
                    up($osc, $containerName, $objId, $localfile);#up
                }
            }
        }
    }
    die(json_encode(['ok' => ($_POST['expectedTotalFinalCheckum'] == $hash), 'crc32' => $hash, 'finalFile' => $fileToAppendTo]));#FINAL FILE CHECKSUM
}

if ($_FILES) {
    if (isset($_POST['uuid'])) {
        $uuid = $_POST['uuid'];
    } else {
        $uuid = uniqid();
    }
    $nb = str_pad($_POST['chunknb'], 8, 0, STR_PAD_LEFT);
    $hash = hash_file($algo2, $_FILES['file']['tmp_name']);
    $fp = $uploadDir . '/' . $uuid . "-" . $nb . ".part";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir);
    }
    if (isset($_POST['expectedCrc32']) and $_POST['expectedCrc32'] != $hash) {
        $a = 'mismatch';
    } else {
        move_uploaded_file($_FILES['file']['tmp_name'], $fp);
    }
    die('{"crc32":"' . $hash . '","uuid":"' . $uuid . '"}');
}
?><html lang="en">
<head><title>Upload file per chuncks with SHA-1 Validation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
</head>
<body>
<fieldset>
    <legend>Upload a video here:</legend>
    <input type="text" id="uuid" title="resuming" placeholder="uuid for resuming upload">
    <input type="file" id="FileUploadHere" title="file" placeholder="file">
    <hr>
    <div id="video-information"></div>
    <div id="chunk-information"></div>
</fieldset>
<hr>
<script>var j, finalMsg, finalChecksum, y, dejaUploades = [], x, errorBreak = 0, failed = 0, success = 0,
        maxRetries = 10,
        algo = '<?php echo $algo?>', hashHex, reader = new FileReader(), tt, expectedCrc32, numberofChunks, chunkForm,
        chunk, cl = console.debug, file, filename, chunkCounter = 0, videoId = 0, playerUrl = "", url = "?"
    const input = document.querySelector('#FileUploadHere'), chunkSize = <?php echo $chunkSize?>;

    input.addEventListener('change', function () {
        file = input.files[0];
        reader = new FileReader();
        reader.onloadend = readerResFichierComplet;
        reader.readAsArrayBuffer(input.files[0]);
        filename = file.name;
        numberofChunks = Math.ceil(file.size / chunkSize);
        startup();
        cl('inputchanged');
    });

    function startup() {
        if (!finalChecksum) {
            cl('waiting for finalChecksum');
            return setTimeout(function () {
                startup();
            }, 1000);
        }
        var oldUuidForResumingUpload = videoId;//document.getElementById("uuid").value;//manually set
        if (oldUuidForResumingUpload) {
            videoId = oldUuidForResumingUpload;
            var form = new FormData();
            form.append('list', oldUuidForResumingUpload);
            x = new XMLHttpRequest();
            x.open('POST', '?list=1', true);
            x.onload = function (e) {
                y = x.response.split(',');
                for (var i in y) {
                    dejaUploades.push(parseInt(y[i]));
                }
                //cl('dejaUploades', dejaUploades);
            };
            x.send(form);
        }
        document.getElementById("video-information").innerHTML = "There will be " + numberofChunks + " chunks uploaded."
        var start = 0, chunkEnd = start + chunkSize;
        //upload the first chunk to get the videoId
        createChunk(start);
    }

    async function readerResFichierComplet() {
        if (reader.readyState && reader.result) {
            var arrayBuffer = reader.result;
            var hashBuffer = await crypto.subtle.digest(algo, arrayBuffer);
            var hashArray = Array.from(new Uint8Array(hashBuffer)); // convert buffer to byte array
            finalChecksum = hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            if (!finalChecksum) {
                errorBreak = 1;
                document.querySelector('#chunk-information').innerHTML = '<hr><div style="border:2px dashed #D00;padding:3rem;font-size:30px;color:#D00;">Use https in order to get crypto.subtle.digest function working</div>';
                console.error('Use https in order to get crypto.subtle.digest function working');
                return;
            }
            if (getCookie('vuuid:' + finalChecksum)) {//resume file upload
                document.querySelector('#uuid').value = videoId = getCookie('vuuid:' + finalChecksum);
            }
        }
    }

    async function readerRes() {
        if (reader.readyState && reader.result) {
            c = Date.now();
            var arrayBuffer = reader.result;//
            var hashBuffer = await crypto.subtle.digest(algo, arrayBuffer); // hash the message
            errorBreak = 0;
            var hashArray = Array.from(new Uint8Array(hashBuffer)); // convert buffer to byte array
            expectedCrc32 = hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            if (!hashBuffer || !expectedCrc32) {
                errorBreak = 1;
                document.querySelector('#chunk-information').innerHTML = '<hr><div style="border:2px dashed #D00;padding:3rem;font-size:30px;color:#D00;">Use https in order to get crypto.subtle.digest function working</div>';
                console.error('Use https');
                return;
            }
            //cl('expectedCrc32:', expectedCrc32);
            chunkForm.append('expectedCrc32', expectedCrc32);
            chunkForm.append('chunknb', chunkCounter);
            e = Date.now();
            tt = e - c;
        }
    }

    function createChunk(start, end) {
        chunkCounter++;
        if (dejaUploades.indexOf(chunkCounter) > -1) {
            //cl('resumin upload :: skip');
            start += chunkSize;
            return createChunk(start);//nex ont
        }
        chunkEnd = Math.min(start + chunkSize, file.size);
        chunk = file.slice(start, chunkEnd);
        reader.readAsArrayBuffer(chunk);
        reader.onloadend = readerRes;
        chunkForm = new FormData();
        chunkForm.append('file', chunk, filename);
        chunkForm.append('chunknb', chunkCounter);
        if (videoId) chunkForm.append('uuid', videoId);
        uploadChunk(chunkForm, start, chunkEnd);
    }

    function uploadChunk(chunkForm, start, chunkEnd, retries) {
        if (errorBreak || !expectedCrc32) {
            return setTimeout(function () {
                return uploadChunk(chunkForm, start, chunkEnd, retries);
            }, 1000);
            cl('delay expectedCrc32 not set', expectedCrc32);
        }

        var retries = retries || 0, oReq = new XMLHttpRequest();
        oReq.upload.addEventListener("progress", updateProgress);
        oReq.open('POST', url, true);
        var blobEnd = chunkEnd - 1;
        var contentRange = "bytes " + start + "-" + blobEnd + "/" + file.size;
        oReq.setRequestHeader("Content-Range", contentRange);
        oReq.onload = function (oEvent) {
            var resp = JSON.parse(oReq.response), res = 'ok';
            setCookie('lastChunkOk', chunkCounter);
            if (resp.uuid && !videoId) {//premier upload, setter cette valeur
                videoId = resp.uuid;
                setCookie('vuuid:' + finalChecksum, videoId);
            }
            if (resp.crc32 != expectedCrc32) res = '<>';
            cl(resp, resp.crc32, res, expectedCrc32, tt);
            if (resp.crc32 != expectedCrc32 && retries > maxRetries) {
                failed++;
                return;
            } else if (resp.crc32 != expectedCrc32) {
                cl('retrying ...', resp.crc32, expectedCrc32);
                uploadChunk(chunkForm, start, chunkEnd, retries + 1);
                return;
            }
            expectedCrc32 = 0;//neutralisé
            success++;
            start += chunkSize;
            if (start < file.size) {//Still left
                createChunk(start);
            } else if ('complete') {
                var form = new FormData();
                form.append('sum', videoId);
                form.append('expectedTotalFinalCheckum', finalChecksum);
                form.append('dest', input.files[0].name);
                x = new XMLHttpRequest();
                x.open('POST', '?sum=1', true);
                x.onload = function (e) {
                    document.querySelector('#uuid').value = '';
                    delCookie('vuuid:' + finalChecksum);
                    delCookie('lastChunkOk');

                    j = JSON.parse(x.response);
                    finalMsg = 'File Upload Complete : <a target=1 href="' + j.finalFile + '">' + j.finalFile + '</a>';

                    cl('>> Final Checksum Matching ? ', finalChecksum == j.crc32);
                    cl('expected final file checksum:', finalChecksum, '<> Response:', j.crc32);
                    cl('final joined filename', j.finalFile);

                    if (finalChecksum == j.crc32) {
                        finalMsg += '<br>Checksum ok';
                    } else {
                        finalMsg += '<br>Checksum ko mismatch : expecting: ' + finalChecksum + ', response was:' + j.crc32;
                    }
                    document.querySelector('#chunk-information').innerHTML = finalMsg;
                    //nettoyage de données
                    videoId = finalChecksum = 0;
                };
                x.send(form);
            }
        };
        oReq.send(chunkForm);
    }

    function updateProgress(oEvent) {
        if (oEvent.lengthComputable) {
            var percentComplete = Math.round(oEvent.loaded / oEvent.total * 100);
            var totalPercentComplete = Math.round((chunkCounter - 1) / numberofChunks * 100 + percentComplete / numberofChunks);
            document.getElementById("chunk-information").innerHTML = "Chunk # " + chunkCounter + " is " + percentComplete + "% uploaded. Total uploaded: " + totalPercentComplete + "%";
        } else {
            cl("not computable");
        }
    }

    function getCookie(e) {
        //if(localStorage.getItem(e))return localStorage.getItem(e);
        return document.cookie.length > 0 && (begin = document.cookie.indexOf(e + "="), -1 != begin) ? (begin += e.length + 1, end = document.cookie.indexOf(";", begin), -1 == end && (end = document.cookie.length), unescape(document.cookie.substring(begin, end))) : null
    }

    function delCookie(e) {
        document.cookie = e + "= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
        return;
        path = ";path=/", domain = ";domain=." + document.location.hostname;
        var o = "Thu, 01-Jan-1970 00:00:01 GMT";
        document.cookie = e + "=" + path + domain + ";expires=" + o
    }

    function setCookie(name, value, expires, path, domain, secure) {
        //localStorage.setItem(name,value);
        path = path || '/';
        var today = new Date();
        today.setTime(today.getTime());
        if (expires) {
            expires = expires * 1000/*ms*/ * 3600/*hours*/ * 24;
        }//given in days
        else {
            expires = 1000 * 60 * 60 * 24 * 365;
        }//365 days
        var expires_date = new Date(today.getTime() + (expires));
        document.cookie = name + "=" + escape(value) + ((expires) ? ";expires=" + expires_date.toGMTString() : "") + ((path) ? ";path=" + path : "") + ((domain) ? ";domain=" + domain : "") + ((secure) ? ";secure" : "");
    }

</script>
</body>
</html>
<?php die;

function os($storage, $containerName, $createIfMissing = 1, $retsrv = 0)
{
    global $os, $cn, $osConf;
    if (isset($os[$storage]) && $retsrv) {
        return $os[$storage]->objectStoreV1();
    }
    if (isset($cn[$storage . '-' . $containerName])) {
        return $cn[$storage . '-' . $containerName];
    }
    if (!isset($os[$storage])) {
        $os[$storage] = new \OpenStack\OpenStack($osConf);
    }#php OppenStack objectStoreV1 createcontainer
    $service = $os[$storage]->objectStoreV1(/*['region' => 'BDB1']*/);
    if ($createIfMissing and !$service->containerExists($containerName)) {
        $service->createContainer(['name' => $containerName]);
    }
    if ($retsrv) return $service;
    $cn[$storage . '-' . $containerName] = $service->getContainer($containerName);
    return $cn[$storage . '-' . $containerName];
}

function up($storage, $containerName, $objUid, $objCtn)
{#todo: async url or pass stream in parameter in order to speed up things ?
    static $t;
    global $__conf, $_perChannel, $osChunckSize;
    if (isset($t[$containerName . $objUid])) {
        _e("\n tentative twice upload, why ?: $objUid");
        return;
    }
    if (isset($t['upped:' . $containerName . $objUid])) {
        _e("\n tentative twice upload, why ?: $objUid");
        return;
    }
    $b = time();
    $c = os($storage, $containerName);
    $a = $objCtn;
    if (strlen($objCtn) < 5) {#$objCtn===null -- thats a problem anyways
        $pb = 1;
        _e("\nProblem:os:up:" . __line__, 1);
        _confirm();
        return;
    }
    if (is_file($objCtn)) {
        $fs = filesize($objCtn);
        $stream = new Stream(fopen($objCtn, 'r'));
        $options = ['name' => $objUid, 'stream' => $stream,];
        if ($fs > $osChunckSize) $options['segmentSize'] = $osChunckSize;#Splitted into 100mo chunks
        if (0) {
            $objCtn = file_get_contents($objCtn);
            $options = ['name' => $objUid, 'content' => $objCtn,];
        }
    } elseif ('raw file contents passed in parameter') {
        if (strlen($objCtn) < 10000) {
            $pb = 1;
            _e("\nProblem:os:up:missing:$objCtn" . __line__, 1);
            _confirm();
            return;
        }
        $options = ['name' => $objUid, 'content' => $objCtn,];
    }
    _e("\nup:$objUid:len:" . strlen($objCtn));
    $t[$containerName . $objUid] = 1;
    $_perChannel[$__conf['byChannelID']]['fup'][$objUid] = $containerName;
    $object = $c->createObject($options);
    echo "up in " . (time() - $b) . 'sec';
    unset($objCtn);
    return $object;
}

function osdel($storage, $containerName, $objUid)
{
    $c = os($storage, $containerName);
    if (!$c->objectExists($objUid)) return 0;
    $o = $c->getObject($objUid);
    if ($o) {
        $o->retrieve();
        $size = $o->contentLength;
        $o->delete();
        return $size;
    }
}

function osget($storage, $containerName, $objUid, $to)
{
    $c = os($storage, $containerName);
    if (!$c->objectExists($objUid)) return 0;
    $stream = $c->getObject($objUid)->download();
    while (!$stream->eof()) {
        file_put_contents($to, $stream->read(4096), 8);
    }
    return $to;
}

function oslist($storage, $containerName, $nameOnly = 0)
{
    $c = os($storage, $containerName);
    $a = [];
    foreach ($c->listObjects() as $t) {
        if ($nameOnly) $a[] = $t->name;
        else $a[] = $t;
    }
    return $a;
}

function exists($storage, $containerName, $objUid, $fullObject = 0)
{
    $c = os($storage, $containerName);
    if (!$fullObject) return $c->objectExists($objUid);# ->download
    $object = $c->getObject($objUid);
#OpenStack\objectStoreV1 stat file  => objectExists => getObject
    return $object;
}

function osd($storage, $containerName, $objUid)
{
    $c = os($storage, $containerName);
    if (!$c->objectExists($objUid)) return;
    return $c->getObject($objUid)->getMetadata();
}

function lc($storage, $containerName, $create = 0)
{
    $c = os($storage, $containerName, $create, 1);
    return $c->listContainers();
}

function b36($x)
{
    return base_convert($x, 10, 36);
}

function _e($x)
{
    print_r($x);
}

function confirm($x)
{
    print_r($x);
}

?>

