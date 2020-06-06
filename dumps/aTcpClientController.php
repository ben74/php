<?#phpx cli.php aTcpClient;#copied from web
class aTcpClientController extends base{}

$fp = stream_socket_client('tcp://127.0.0.1:1983', $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)\n";
} else {
    fwrite($fp,'coucou');
    while (!feof($fp)) {
        echo fgets($fp, 1024);#blocked, why ???
    }
    fclose($fp);
}
_die();
