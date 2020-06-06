<?
#phpx $td/duplicatePhotos.php | tee duplicates.log
chdir('d:/aPicSync/Pictures/2007-Sardegna');
mkdir('duplicates');
$x=glob('*.jpg');
foreach($x as $f){$s[$f]=filesize($f);}
$potentialDuplicates=array_count_values($s);#of the sizes
arsort($potentialDuplicates);
foreach($potentialDuplicates as $size=>$nb){
    if($nb<2)die("\n no more duplicates found");
    $filesBySize=array_keys($s,$size);
    if(!$filesBySize or count($filesBySize)<2){continue;}
    #echo"\n}$nb{\n";print_r($filesBySize);die;
    $md5v=[];
    foreach($filesBySize as $f){
        $md=md5_file($f);
        $md5v[$f]=$md;
    }
    
    $md5s=array_count_values($md5v);
    arsort($md5s);
    foreach($md5s as $md5=>$nbmd5){
        if($nbmd5<2)continue;
        $filesByMd5=array_keys($md5v,$md5);
        if(!$filesByMd5 or count($filesByMd5)<2){continue;}
        #echo"\n$md5::\n";print_r($filesByMd5); 
        array_shift($filesByMd5);
        foreach($filesByMd5 as $f){
            echo"\n$f";
            rename($f,'duplicates/'.$f);
        }
    }
    #array_shift($filesBySize);     
}
die("\n no more duplicates found...");
print_r($potentialDuplicates);
die;
#get keys for duplicate value, keep first aside, tag others
print_r($x);
