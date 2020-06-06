<?php
class reflectorController extends base{}
$postBody = trim(file_get_contents('php://input'));
echo"\nPostBody:".$postBody."\n\n";
$_POST=json_decode($postBody,1);
print_r(['p'=>$_POST,'c'=>$_COOKIE]);
