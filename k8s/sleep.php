<?php
/*
php -dextension=pcntl.so sleep.php &
*/
class sleep
{
    public $pid;
    function sighup()
    {
        $c = curl_init($_ENV['NOTIFY_URL'].'?say=sighup');curl_exec($c);
        sleep(99999);#otherwise dies ...
    }
    function sigterm()
    {
        $c = curl_init($_ENV['NOTIFY_URL'].'?say=sigterm');curl_exec($c);
    }
    function dies()
    {
        $c = curl_init($_ENV['NOTIFY_URL'].'?say=dies');curl_exec($c);
    }
    function main()
    {
        $c = curl_init($_ENV['NOTIFY_URL'].'?say=on');curl_exec($c);
        register_shutdown_function([$this, 'dies']);
        pcntl_async_signals(true);
        pcntl_signal_dispatch();
        $this->pid = $pid = getmypid();
        \pcntl_signal(SIGTERM, [&$this, 'sigterm']);
        #\pcntl_signal(SIGINT, [&$this, 'sigint']);
        \pcntl_signal(SIGHUP, [&$this, 'sighup']);
        #\pcntl_signal(SIGUSR1, [&$this, 'sigusr1']);
        sleep(999999);
    }

}

$sleep = new sleep;
$sleep->main();