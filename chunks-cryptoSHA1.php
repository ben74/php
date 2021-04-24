<?php
#todo:baser le resume sur filename/filesize.info que l'on recup sur l'input
#cookie auto uuid tant que sum non finie
#http://1.x24.fr/a/uploads/200708-drn.tinariCriqueSauvage.MP4?file=1&result
$algo = 'SHA-1';
$algo2 = 'sha1';
$chunkSize = 1000000;#1mo

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
    $x = glob('uploads/' . $_POST['sum'] . '*.part');
    $fileToAppendTo = 'uploads/' . str_replace(['.php'],'',$_POST['dest']);
    @unlink($fileToAppendTo);
    foreach ($x as $v) {
        file_put_contents($fileToAppendTo, file_get_contents($v), 8);
        unlink($v);
    }
    die($fileToAppendTo);
}

if ($_FILES) {
    if (isset($_POST['uuid'])) {
        $uuid = $_POST['uuid'];
    } else {
        $uuid = uniqid();
    }
    $nb = str_pad($_POST['chunknb'], 8, 0, STR_PAD_LEFT);
    $hash = hash_file($algo2, $_FILES['file']['tmp_name']);
    $fp = 'uploads/' . $uuid . "-" . $nb . ".part";
    if (isset($_POST['expectedCrc32']) and $_POST['expectedCrc32'] != $hash) {
        $a = 'mismatch';
    } else {
        move_uploaded_file($_FILES['file']['tmp_name'], $fp);
    }
    die('{"crc32":"' . $hash . '","uuid":"' . $uuid . '"}');
}
?>
<html lang="en">
<head><title>Chunck Uploads</title>
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
<script>var y, dejaUploades = [], x, failed = 0, success = 0, maxRetries = 10, algo = '<?php echo $algo?>', hashHex, reader = new FileReader(), tt, expectedCrc32, numberofChunks, chunkForm, chunk, cl = console.debug, file, filename, chunkCounter = 0, videoId = 0, playerUrl = "", url = "?"
    const input = document.querySelector('#FileUploadHere'), chunkSize = <?=$chunkSize?>;

    if(getCookie('vuuid')) {
        document.querySelector('#uuid').value=getCookie('vuuid');
    }

    input.addEventListener('change', function () {
        file = input.files[0];
        filename = file.name;
        numberofChunks = Math.ceil(file.size / chunkSize);
        var oldUuidForResumingUpload = document.getElementById("uuid").value;
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
    });

    async function readerRes() {
        if (reader.readyState && reader.result) {
            c = Date.now();
            var arrayBuffer = reader.result;
            var hashBuffer = await crypto.subtle.digest(algo, arrayBuffer); // hash the message
            var hashArray = Array.from(new Uint8Array(hashBuffer)); // convert buffer to byte array
            expectedCrc32 = hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
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
        var retries = retries || 0, oReq = new XMLHttpRequest();
        oReq.upload.addEventListener("progress", updateProgress);
        oReq.open('POST', url, true);
        var blobEnd = chunkEnd - 1;
        var contentRange = "bytes " + start + "-" + blobEnd + "/" + file.size;
        oReq.setRequestHeader("Content-Range", contentRange);
        oReq.onload = function (oEvent) {
            var resp = JSON.parse(oReq.response), res = 'ok';
            setCookie('lastChunkOk', chunkCounter);
            if (resp.uuid && !videoId) {
                videoId = resp.uuid;
                setCookie('vuuid', videoId);
            }
            if (resp.crc32 != expectedCrc32) res = '<>';
            cl(resp, resp.crc32, res, expectedCrc32, tt);
            if (resp.crc32 != expectedCrc32 && retries > maxRetries) {
                failed++;
                return;
            } else if (resp.crc32 != expectedCrc32) {
                cl('retrying ...');
                uploadChunk(chunkForm, start, chunkEnd, retries + 1);
                return;
            }
            success++;
            start += chunkSize;
            if (start < file.size) {//Still left
                createChunk(start);
            } else if ('complete') {
                var form = new FormData();
                form.append('sum', videoId);
                form.append('dest', input.files[0].name);
                x = new XMLHttpRequest();
                x.open('POST', '?sum=1', true);
                x.onload = function (e) {
                    delCookie('vuuid');
                    delCookie('lastChunkOk');
                    videoId=0;//new upload then
                    cl('final joined filename', x.response);
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

    function getCookie(e){
        //if(localStorage.getItem(e))return localStorage.getItem(e);
        return document.cookie.length>0&&(begin=document.cookie.indexOf(e+"="),-1!=begin)?(begin+=e.length+1,end=document.cookie.indexOf(";",begin),-1==end&&(end=document.cookie.length),unescape(document.cookie.substring(begin,end))):null}
    function delCookie(e){path=";path=/",domain=";domain=."+document.location.hostname;var o="Thu, 01-Jan-1970 00:00:01 GMT";document.cookie=e+"="+path+domain+";expires="+o}
    function setCookie(name,value,expires,path,domain,secure){
        //localStorage.setItem(name,value);
        path=path || '/';
        var today = new Date();today.setTime(today.getTime());
        if(expires){expires = expires * 1000/*ms*/ * 3600/*hours*/ * 24;}//given in days
        else{expires=1000*60*60*24*365;}//365 days
        var expires_date = new Date( today.getTime() + (expires) );
        document.cookie=name+"="+escape(value)+ ((expires)?";expires="+expires_date.toGMTString():"")+ ((path)?";path="+path:"")+ ((domain)?";domain="+domain:"")+ ( ( secure ) ? ";secure" : "" );
    }

</script>
</body>
</html>
