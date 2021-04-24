<?/*
php -r 'echo md5_file($argv[1]);' /d/aPicSync/heloiseLuge.mp4;#ef72
php -r 'echo md5_file($argv[1]);' /c/Users/ben/home/phpgit/uploads/final.mp4;#38AD

https://api.video/blog/tutorials/uploading-large-files-with-javascript

effectue chaque chunk dans l'ordre //

md5 45 2452612f1d8a1b5bf5dad3bfd283a2cc 57 checksum 533838682 4
ChunkUploaded 45 0.0027570724487305 2955097679

php -r 'echo md5_file($argv[1]);' /d/aPicSync/heloiseLuge.mp4;#ef72
cux https://php.home/chunkedUpload.php?sum;echo '--';php -r 'echo md5_file($argv[1]);' /c/Users/ben/home/phpgit/uploads/final.mp4;
*/
$rb=4096;$cs='1mb';$fileToAppendTo='uploads/final.mp4';
if(isset($_GET['sum'])){
    $new=0;
    $x=glob('uploads/*.part');
    @unlink($fileToAppendTo);#touch($fileToAppendTo);
    if(!$new)$dest = fopen($fileToAppendTo,"w+"); if (FALSE === $dest) die("Failed to open destination");
    foreach($x as $v){
        if($new)file_put_contents($fileToAppendTo,file_get_contents($v),8);
        else {
            $handle = fopen($v, "rb");if (false === $handle) {fclose($dest);die("Failed to open source");}while (!feof($handle))fwrite($dest, fread($handle, $rb));
            fclose($handle);
        }
    }

    fclose($dest);
    #foreach($x as $v)unlink($v);
    die($fileToAppendTo);
}



ini_set('display_errors',1);

function checksum($s) {
    return crc32($s);
    $chk=0x12345678;$len=strlen($s);$s=str_split($s);
    for ($i=0;$i<$len;$i++){$chk.=$s[$i]*($i+1);}
    $x=$chk & 0xffffffff;
    return $x;#.toString(16);
}
function verbose($ok=1,$info=""){
  // THROW A 400 ERROR ON FAILURE
  if ($ok==0) { http_response_code(400); }
  die(json_encode(["ok"=>$ok, "info"=>$info]));
}

if($_FILES){
// (A) FUNCTION TO FORMULATE SERVER RESPONSE
// (B) INVALID UPLOAD
if (empty($_FILES) || $_FILES['file']['error']) {
  verbose(0, "Failed to move uploaded file.");
}

// (C) UPLOAD DESTINATION
// ! CHANGE FOLDER IF REQUIRED !
$filePath = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
if (!file_exists($filePath)) { 
  if (!mkdir($filePath, 0777, true)) {
    verbose(0, "Failed to create $filePath");
  }
}
$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
$filePath = $filePath . DIRECTORY_SEPARATOR . $fileName;
// (D) DEAL WITH CHUNKS
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
#$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
$chunkFN=str_pad($chunk,8,0,STR_PAD_LEFT);
$fp="{$filePath}-{$chunkFN}.part";
move_uploaded_file($_FILES['file']['tmp_name'],$fp);
if(0){
$out=fopen($fp,'wb');
if ($out) {
  $in=@fopen($_FILES['file']['tmp_name'], 'rb');
  if ($in) {while ($buff = fread($in, $rb))fwrite($out, $buff);} else {
    die('{"OK":0,"info":"Failed to open input stream"}');
  }
  @fclose($in);@fclose($out);
  @unlink($_FILES['file']['tmp_name']);
  #die(md5_file($fp));
} else {
  verbose(0, "Failed to open output stream");
}
}
$a=microtime(1);$c=checksum(file_get_contents($fp));$b=microtime(1);
#if($c!=$expectedChecksum){unlink($out);die('{"OK": 0, "info": "checksum<>"}');}
die('{"OK":1,"chunk":'.$chunk.',"time":"'.round(($b-$a)*1000).'","md5":"'.$c.'"}');
die('{"chunk":'.$chunk.',"md5":"'.md5_file($fp).'"}');

// (E) CHECK IF FILE HAS BEEN UPLOADED
if (!$chunks || $chunk == $chunks - 1) {
  rename("{$filePath}.part", $filePath);
}
verbose(1, "Upload OK");
die;
}
session_start();
?><title>Chunked uploads</title><script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/2.3.7/plupload.full.min.js"></script>
<script src="/vendor/alptech/wip/js/md5.js?a=1"></script>
<script>/*
xhr.onload = function() {// check if upload made itself through
    cl(xhr.response)
    if (xhr.status >= 400) {
        //handleError();
        return;
    } else {return;
        for (var i in settings.errors_responses) {
            if (xhr.responseText == settings.errors_responses[i]) {
                handleError();
                return;
            }
        }
    }
}*/


function checksum(s) {
    return crc32(s);
    var chk = 0x12345678;var len = s.length;
    for (var i = 0; i < len; i++) {chk += (s.charCodeAt(i) * (i + 1));}
    return (chk & 0xffffffff);//s.toString(36);
}
function sleep(ms) {
    return new Promise(resolve=>setTimeout(resolve,ms));
}

var lastOffset=0,cl=console.debug,ready=0,reader,responses={},checksums={},readers={};
window.addEventListener("load", function () {
  var uploader = new plupload.Uploader({
    on_error:function(x,r,u,f){cl(x,r,u,f);
        f.loaded=lastOffset;//retry
        return true;},
    runtimes: 'html5,html4', browse_button: 'pickfiles',max_retries: 3, url: '<?=$_SERVER['REQUEST_URI']?>?send', chunk_size: '<?=$cs?>',
    ///* OPTIONAL
    filters: {
      max_file_size: '100Go',
      mime_types: [{title: "Videos files", extensions: "mp4,mp3,mkv,avi,mov"}]
    },
    init: {
BeforeUpload:function(up, file) {return;cl('BeforeUpload',up, file);},//AR : works once
BeforeChunkUpload:function(up,file,args,chunkBlob,Offset){
//javascript read blob content
    lastOffset=Offset;
    reader=readers[args.chunk] = new FileReader();
    readers[args.chunk].readAsText(chunkBlob.getSource());
    readers[args.chunk].onloadend=function(){
        //var reader=readers[chunkNb];
        //rcl('rol',chunkNb,reader.readyState);
        //a=Date.now();b=md5(reader.result);c=Date.now();
        chunkNb=args.chunk;
        if(reader.readyState && reader.result){
          c=Date.now();d=checksum(reader.result);checksums[chunkNb]=d;e=Date.now();//md5 99 checksum 5
//document.cookie = "checksum:"+chunkNb+"="+d+"; expires=Thu, 18 Dec 2090 12:00:00 UTC; path=/";
          //up.settings.multipart_params={"checksum":d};
          //up.settings.url="?checksum="+d;
          cl('md5',chunkNb,'crc32',d,e-c,up);
        }
    //cl('md5',chunkNb,b,c-a,'checksum',d,e-c);
     };//
  //ready=0;cl('.');while(!ready){sleep(2000);cl('s');}cl('=');
  //sleep(500).then(() => {});
},
ChunkUploaded:function(up,file,res) {
    if(res.status!=200){
        return;file.chunk.resend();
    }
    var j = JSON.parse(res.response);
    responses[j.chunk] = j.md5;
    if(checksums[j.chunk] != j.md5) {
        cl('ChunkUploaded', checksums[j.chunk],'<>', j.md5);
        return;
    }
    cl('ChunkUploaded',j.chunk,j.md5,file.loaded);
    return;
    if(checksums[j.chunk] != j.md5){
        cl('not same checksum > retry !',checksums[j.chunk] , j.md5);
        file.loaded=lastOffset;
    }

    //j.time,
},FileUploaded:function(up, file,res){
    return;
    cl('FileUploaded',up, file,res);/*res contains last checksum*/},
UploadComplete:function(up) {cl('UploadComplete',up);},
//https://github.com/moxiecode/plupload/wiki/Uploader#events
//https://www.plupload.com/docs/v2/Uploader#BeforeChunkUpload-event*/

      PostInit: function () {
        document.getElementById('filelist').innerHTML = '';
      },
      FilesAdded: function (up, files) {
        plupload.each(files, function (file) {
          document.getElementById('filelist').innerHTML += `<div id="${file.id}">${file.name} (${plupload.formatSize(file.size)}) <b></b></div>`;
        });
        uploader.start();
      },
      UploadProgress: function (up, file) {
        //cl(file);
        document.querySelector(`#${file.id} b`).innerHTML = `<span>${file.percent}%</span>`;
      },
      Error: function (up, err) {console.log(err);}
    }
  });
  //uploader.bind('FileUploaded', function(up, file, ret) {console.log('FileUploaded',up,file,ret);//obj.jsonrpc});//same as above
  uploader.init();
});


function Utf8Encode(string) {
    string = string.replace(/\r\n/g,"\n");
    var utftext = "";

    for (var n = 0; n < string.length; n++) {
        var c = string.charCodeAt(n);
        if (c < 128) {
            utftext += String.fromCharCode(c);
        } else if((c > 127) && (c < 2048)) {
            utftext += String.fromCharCode((c >> 6) | 192);
            utftext += String.fromCharCode((c & 63) | 128);
        } else {
            utftext += String.fromCharCode((c >> 12) | 224);
            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
            utftext += String.fromCharCode((c & 63) | 128);
        }
    }
    return utftext;
};

function crc32 (str) {
    //str = Utf8Encode(str);
    var table = "00000000 77073096 EE0E612C 990951BA 076DC419 706AF48F E963A535 9E6495A3 0EDB8832 79DCB8A4 E0D5E91E 97D2D988 09B64C2B 7EB17CBD E7B82D07 90BF1D91 1DB71064 6AB020F2 F3B97148 84BE41DE 1ADAD47D 6DDDE4EB F4D4B551 83D385C7 136C9856 646BA8C0 FD62F97A 8A65C9EC 14015C4F 63066CD9 FA0F3D63 8D080DF5 3B6E20C8 4C69105E D56041E4 A2677172 3C03E4D1 4B04D447 D20D85FD A50AB56B 35B5A8FA 42B2986C DBBBC9D6 ACBCF940 32D86CE3 45DF5C75 DCD60DCF ABD13D59 26D930AC 51DE003A C8D75180 BFD06116 21B4F4B5 56B3C423 CFBA9599 B8BDA50F 2802B89E 5F058808 C60CD9B2 B10BE924 2F6F7C87 58684C11 C1611DAB B6662D3D 76DC4190 01DB7106 98D220BC EFD5102A 71B18589 06B6B51F 9FBFE4A5 E8B8D433 7807C9A2 0F00F934 9609A88E E10E9818 7F6A0DBB 086D3D2D 91646C97 E6635C01 6B6B51F4 1C6C6162 856530D8 F262004E 6C0695ED 1B01A57B 8208F4C1 F50FC457 65B0D9C6 12B7E950 8BBEB8EA FCB9887C 62DD1DDF 15DA2D49 8CD37CF3 FBD44C65 4DB26158 3AB551CE A3BC0074 D4BB30E2 4ADFA541 3DD895D7 A4D1C46D D3D6F4FB 4369E96A 346ED9FC AD678846 DA60B8D0 44042D73 33031DE5 AA0A4C5F DD0D7CC9 5005713C 270241AA BE0B1010 C90C2086 5768B525 206F85B3 B966D409 CE61E49F 5EDEF90E 29D9C998 B0D09822 C7D7A8B4 59B33D17 2EB40D81 B7BD5C3B C0BA6CAD EDB88320 9ABFB3B6 03B6E20C 74B1D29A EAD54739 9DD277AF 04DB2615 73DC1683 E3630B12 94643B84 0D6D6A3E 7A6A5AA8 E40ECF0B 9309FF9D 0A00AE27 7D079EB1 F00F9344 8708A3D2 1E01F268 6906C2FE F762575D 806567CB 196C3671 6E6B06E7 FED41B76 89D32BE0 10DA7A5A 67DD4ACC F9B9DF6F 8EBEEFF9 17B7BE43 60B08ED5 D6D6A3E8 A1D1937E 38D8C2C4 4FDFF252 D1BB67F1 A6BC5767 3FB506DD 48B2364B D80D2BDA AF0A1B4C 36034AF6 41047A60 DF60EFC3 A867DF55 316E8EEF 4669BE79 CB61B38C BC66831A 256FD2A0 5268E236 CC0C7795 BB0B4703 220216B9 5505262F C5BA3BBE B2BD0B28 2BB45A92 5CB36A04 C2D7FFA7 B5D0CF31 2CD99E8B 5BDEAE1D 9B64C2B0 EC63F226 756AA39C 026D930A 9C0906A9 EB0E363F 72076785 05005713 95BF4A82 E2B87A14 7BB12BAE 0CB61B38 92D28E9B E5D5BE0D 7CDCEFB7 0BDBDF21 86D3D2D4 F1D4E242 68DDB3F8 1FDA836E 81BE16CD F6B9265B 6FB077E1 18B74777 88085AE6 FF0F6A70 66063BCA 11010B5C 8F659EFF F862AE69 616BFFD3 166CCF45 A00AE278 D70DD2EE 4E048354 3903B3C2 A7672661 D06016F7 4969474D 3E6E77DB AED16A4A D9D65ADC 40DF0B66 37D83BF0 A9BCAE53 DEBB9EC5 47B2CF7F 30B5FFE9 BDBDF21C CABAC28A 53B39330 24B4A3A6 BAD03605 CDD70693 54DE5729 23D967BF B3667A2E C4614AB8 5D681B02 2A6F2B94 B40BBE37 C30C8EA1 5A05DF1B 2D02EF8D";
    var crc = 0;
    var x = 0;
    var y = 0;

    crc = crc ^ (-1);
    for( var i = 0, iTop = str.length; i < iTop; i++ ) {
        y = ( crc ^ str.charCodeAt( i ) ) & 0xFF;
        x = "0x" + table.substr( y * 9, 8 );
        crc = ( crc >>> 8 ) ^ x;
    }

    return (crc ^ (-1)) >>> 0;
};
</script>
 
<div id="container">
  <span id="pickfiles">[Upload files]</span>
</div>

<!-- UPLOAD FILE LIST -->
<div id="filelist">Your browser doesn't support HTML5 upload.</div>
<style>
#container{border:2px dashed #D00}
</style>
