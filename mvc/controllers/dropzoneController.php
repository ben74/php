<?php
class dropzoneController{static function main(){}}
require_once 'app/common.php';
nocache();
$postTo = basename(__FILE__);
$cb = date('YmdHis');
$a = $_SERVER;
$p = $_POST;
$g = $_GET;
#header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
#if($a['HTTP_HOST']!='2.x24.fr' or strpos($a['REQUEST_URI'],'dropzone')===FALSE){header('location: //2.x24.fr/dropzone.php#'.$cb,1,302);die;}
$folders = explode(';', 'z/img;z/uploads;z/pasted');
foreach ($folders as $d) {
    if (!is_dir($d)) {
        mkdir($d, 0777, 1);#recursive mkdir
    }
}
#default upload folder is
if (!isset($p['folder'])) {
    $p['folder'] = 'z/uploads';
}

if (isset($_FILES['paste'])) {#fichier issu d'un copier / coller ( as png )
    $x = $_FILES['paste'];
    if ($x['type'] != 'image/png') {
        die('/*nah*/');
    }
    $fi = 'z/pasted/' . date('YmdHis') . '.png';
    #$x['tmp_name']
    $_a = move_uploaded_file($x['tmp_name'], $fi);
    die($fi);
}

$cb = '';

if (isset($_FILES) and count($_FILES)) {
    foreach ($_FILES as $k => $v) {
        #if($v['error'])die('err:'.$v['error']);
        $ext = explode('.', $v['name']);
        $ext = end($ext);
        if (strpos($v['name'], '.php') or strpos($v['name'], '.html')) {
            continue;
        }#die('php');
        #$sFileType = $v['type'];
        #$sFileSize = bytesToSize1024($v['size'], 1);
        $t = $t2 = $p['folder'] . '/' . $v['name'];
        $i = 0;
        while (is_file($t) && $i < 900) {
            $t = str_replace('.' . $ext, '_' . $i . '.' . $ext, $t2);
            $i++;
        }
        if ($i > 899) {
            die('#e#' . $v['name']);
        }
        move_uploaded_file($v['tmp_name'], $t);
        echo $t . ' ';
    }
    die;
    print_r($_FILES);
    die;
}
if ($_POST) {
    die('POSTDATA');
}
if (!empty($a['HTTP_X_REQUESTED_WITH']) && strtolower($a['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    die('ajax');
}
if ($a['QUERY_STRING']) {
    die('qs');
}
/* > 8Mo > dies('qs'); */
$opt = '';
foreach ($folders as $v) {
    $opt .= '<option value="' . $v . '">' . $v . '</option>';
}

$k = 'r';
$self = '//' . $a['HTTP_HOST'] . $a['REQUEST_URI'] . '?' . $cb;#//1.x24.fr/upload.php?$cb#http://www.script-tutorials.com/demos/257/upload.php
$title='dropzone';
require_once 'z/header.php';
?>

<script>var postto='<?=$postTo?>';</script>
<script type='text/javascript' src='/z/upload.js?k=<?= $k ?>&<?= $cb ?>'></script>
<link rel=stylesheet href='/z/upload.css?<?= $cb ?>'/>
<?#}{?>
<fieldset class="container"><legend>Dropzone</legend>
    <canvas width="500px" height="20px"></canvas>
    <table class="t1">
        <tr>
            <td><div id="dropArea">Drag and drop your files to this dropzone - or copy paste them ( either pasting screenshot or img file contents selection )</div></td>
            <td class="info">
                    <li>Max fs : <?= ini_get('upload_max_filesize') ?> max post size :<?= ini_get('post_max_size') ?>
                    <li>Remaining files : <span id="count">0</span>
                    <li>Destination Url : <input name id="url" value="<?= $self ?>"/>
                    <li>Target folder : <select id=folder><?= $opt ?></select>
                    <hr>Results :
                    <div id="result">

                    </div>
            </td>
        </tr>
    </table>
</fieldset>
</body>
</html><?
return;
die;
function bytesToSize1024($bytes, $precision = 2)
{
    $unit = array('B', 'KB', 'MB');
    return @round($bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision) . ' ' . $unit[$i];
}

?>
// fixe le niveau de rapport d'erreur
if (version_compare(phpversion(), '5.3.0', '>=') == 1)error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);else error_reporting(E_ALL & ~E_NOTICE);
#file_put_contents(__file__.'.p.log',"\n\n".print_r($_FILES,1),8);
return;die;
#[type] => image/png
$file_contents = file_get_contents( $_FILES['paste']['tmp_name'] );
header("Content-Type: " . $_FILES['paste']['type'] );
die($file_contents);
