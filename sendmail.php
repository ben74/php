<?
session_start();
$_SESSION['a']=1;
phpinfo();
print_r(sendMail('bmxa74+test@gmail.com','tox24','tox24body',null));
function sendMail($to,$sub,$body,$head=null,$from=null,$mid=''){
    $s="\r\n";
    $sub='=?UTF-8?B?' . base64_encode($sub) . '?=';
    if (preg_match("~Message-ID: ([^\r\n]+)~i",$head,$m) and $m[1]){$mid=$m[1];}
    else{$mid=preg_replace('~[^a-z0-9]+~i','',md5(time().$to.$sub.$body));$head .="Message-ID: ".$mid.$s;}#generates messageId if absent
    if (strpos($head, 'text/html') === false){$head .= "MIME-Version: 1.0{$s}Content-type: text/html; charset=utf-8{$s}";}#            iso-8859-1   #make html as default :)
    if (!$from and preg_match("~From:[^\r\n]+<([^>]+)>~i",$head,$m) and $m[1]) {#from.$to
        $from=trim($m[1],'>< ');
    } elseif (!$from and preg_match("~From: ([^\r\n]+)~i",$head,$m) and $m[1]) {#from.$to
        $from=trim($m[1],'>< ');
    } elseif (strpos($head, 'From:') === false) {
        if (!$from) {
            $from = 'www-data@91-171-92-63.subs.proxad.net';#default@x24.fr
        }
        $head .= "From: $from{$s}Reply-To: $from{$s}";        
    }

    $sp='/var/log/'.$_SERVER['HOSTNAME'].'/mailsent/-';#__DIR__.'/';
    $sent=mail($to,$sub,$body,$head);
    if($sp){#todo:query postfix for messageId
        $f=$_SERVER['DOCUMENT_ROOT'].$sp.substr(preg_replace('~_+~','_',preg_replace('~[^a-z0-9@\.\-]~is','_',$mid.'-_-'.$to.'-_-'.time().'-_-'.$sub)),0,250).'.json';#
        $_written=file_put_contents($f,json_encode(compact('sent','to','sub','body','head')));
    }
    return $sent;
}