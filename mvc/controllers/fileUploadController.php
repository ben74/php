<?php
class fileUploadController{static function main(){}}

require_once 'app/common.php';
if(isset($_POST['ok']))_die($_POST['ok']);
if(isset($_FILES) and $_FILES){
    $json=json_encode($_FILES);
    if(preg_match('~"name":"[^\"]+\.php[^\"]*"~i',$json,$m)){#way much more simpler but wont work for more complex ..
        _die("catched:".$m[0]);
    }
    if(preg_match('~":"[^\"]+\.php[^\"]*"~i',$json,$m)){
        _die("catched 4 complex uploads:".$m[0]);
    }
    #$foundUploads=searchInArrayDepths($_FILES);
    $foundUploads=searchInArrayDepths($_FILES,['name'],'~\.php~i');#keys with values
    if(!$foundUploads){echo'ok:';_die($json);}
    print_r(['p'=>$_POST,'foundPhpUploadedFileInRecursivity'=>$foundUploads,'uploadedFiles'=>$_FILES]);
    die;
}
#echo phpversion();
#dummy-checks the inner contents of potential sent file
if(isset($_FILES) and $_FILES){
    foreach($_FILES as $k=>$t){
        echo"<li>$k :: $t[name]<br>".htmlentities(file_get_contents($t['tmp_name']));
    }
    echo"<hr>Then catched by security measures => ";
}

#$files=['nocheckzyx9'=>1,'file' =>curl_file_create($file,'.jpg',$name)];
$file='shell.php';
$ras=cup('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=nada',[],['ok'=>'pass'])['contents'];
$jpg=curlFile('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=jpg','shell.php','babar.jpg')['contents'];


$simple=curlFile('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=Simple','shell.php','babar.php',['nocheckzyx9'=>1])['contents'];
 /*[upload][name][0]=>badm*/
$complex2=cup('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=Complex',[],['nocheckzyx9'=>1,'upload[0]'=>curl_file_create($file,'.jpg','arbook.jpg')])['contents'];
$complex=cup('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=Complex',[],[
    'nocheckzyx9'=>1    
    ,'inocent'=>curl_file_create('.gitignore','.jpg','babar.jpg')
    ,'lv0[lv1][bobar]'=>curl_file_create($file,'.jpg','bobar.php')#lv0>name>lv1
    ,'lv0[lv2][babar]'=>curl_file_create('.gitignore','.jpg','babar.jpg')#lv0>name>lv2
    ,'lv0[bobar]'=>curl_file_create($file,'.jpg','bobar.php')#lv0>name
    ,'lv0[babar]'=>curl_file_create('.gitignore','.jpg','babar.jpg')#lv0>name>
/*
    ,'deeper' =>['dummy'=>1
        ,'inocent'=>curl_file_create('.gitignore','.jpg','babar.jpg')
        ,'nestedFileUpload'=>curl_file_create($file,'.jpg','bobar.jpg')]*/
],['content-type: multipart/form-data'])['contents'];
$simplePhpFileUploadCatched=curlFile('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=Simple','shell.php','babar.php')['contents'];
$complexPhpFileUploadCatched=cup('https://'.$_SERVER['HTTP_HOST'].'/file-upload.php?fupload=Complex',[],[
    'inocent'=>curl_file_create('.gitignore','.jpg','babar.jpg')
    ,'lv0[lv1][bobar]'=>curl_file_create($file,'.jpg','bobar.php')#lv0>name>lv1
    ,'lv0[lv2][babar]'=>curl_file_create('.gitignore','.jpg','babar.jpg')#lv0>name>lv2
    ,'lv0[bobar]'=>curl_file_create($file,'.jpg','bobar.php')#lv0>name
    ,'lv0[babar]'=>curl_file_create('.gitignore','.jpg','babar.jpg')#lv0>name>
/*
    ,'deeper' =>['dummy'=>1
        ,'inocent'=>curl_file_create('.gitignore','.jpg','babar.jpg')
        ,'nestedFileUpload'=>curl_file_create($file,'.jpg','bobar.jpg')]*/
],['content-type: multipart/form-data'])['contents'];
#
#htmlentities(
#$x['contents']=substr($x['contents'],0,50);
echo'<pre>';print_r(compact('ras','jpg','complex2','simplePhpFileUploadCatched','complexPhpFileUploadCatched','simple','complex'));

$title='check file upload contents';
require_once 'z/header.php';
?>Try it on your own<form enctype="multipart/form-data" method="post"><input name="1" type="file" /><input type=submit></form><?return;?>
