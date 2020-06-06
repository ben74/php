<?die;#notes using syntax color & so ..
phpgit;log phpgit_php74;
cd /home/mvc/controllers;php -S 127.0.0.1:1983 phpServerController.php;#easier 20Mo postdata => took : 45ms
php /home/cli.php streamServer;
php /home/cli.php streamClient 0;#20m =>Took:388ms

fastbus/messages/20201018165410-127.0.0.146154.msg
len:9308160

x0=str.trim().replace(/\s+/gi,' ').replace(/> </gi,'><');
x1=b64EncodeUnicode(x0);
sc('clickAndCollect',x1);

#python comment is weird in php8 ..
>> php #[ interprêté       as     #[ExampleAttribute]
