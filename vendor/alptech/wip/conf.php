<?php
namespace Alptech\Wip;
return [
    'defaultHost'=>'https://d9.home/',
    'logCollectorUrl' => 'https://d9.home/alptech.php?a=logCollector',#exposed path loading alptech

    'routes'=>['/route1'=>['router','test']],
    'a'=>['b'=>1],
    'devIps'=>['127.0.0.1'],'localhostIps'=>['127.0.0.1'],
    'ip2hostname'=>['127.0.0.1'=>'local'],
#multidimensional ignored for bash variables
    'host'=>'transmittedhostname4monitoring',
    'silverb'=>['h' => '127.0.0.1', 'u' => 'a', 'p' => 'b', 'db' => 'silverpricing_db','names'=>'utf8'],
    'silver'=>['h' => '127.0.0.1', 'u' => 'a', 'p' => 'b', 'db' => 'silverpricing_data_db','names'=>'utf8'],
    'mysql'=>['h' => '127.0.0.1', 'u' => 'a', 'p' => 'b', 'db' => 'alptech','names'=>'utf8'],
    'mysql_host' => '127.0.0.1','mysql_user' => 'a', 'mysql_pass' => 'b', 'mysql_db' => 'alptech',#bash
    /** activate the logs here */
    'log'=>1,
    'sendLogs' => 1,
    'logdir' => 'logs',
    'errorLog'=>'logs/errorLog.log',
    'exceptionsLog'=>'logs/exceptions.log',
    'logCollectorSecret'=>'hophophop',
    'logCollectorSeed'=>'%y%m%d',#one valid per day, avoid Hours if datetime resolution is not the same sync or timezone, will cause mismatches
    'authorizedIps'=>['local'=>'127.0.0.1','l6'=>'::1','pom2'=>'2a01:e0a:2d7:fe0:cda9:527f:604a:10e9'],

    'pathSeparator' => '-_',
    'thumbAuthorizedWidths' => [100],
    'thumbAuthorizedHeights' => [100],
    'thumbnailsDir' => 'y/thumbs/',
    'defaultImage' => 'y/default.png',
    'mediaTypes' => 'jpeg,jpg,png,webp,ico,gif,woff,ttf,eot,woff2,css,js,map',#404 is /**/
];?>

