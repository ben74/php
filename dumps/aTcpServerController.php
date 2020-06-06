<?php
#phpx cli.php aTcp;#copied from web
class aTcpServerController extends base{}

$master = array();
$socket = stream_socket_server("tcp://0.0.0.0:1983", $errno, $errstr);
if (!$socket) {
    echo "$errstr ($errno)<br />\n";
} else {
    $master[] = $socket;
    $read = $master;
    while (1) {
        $read = $master;
        $mod_fd = stream_select($read, $_w = NULL, $_e = NULL, 5);
        if ($mod_fd === FALSE) {
            break;
        }
        for ($i = 0; $i < $mod_fd; ++$i) {
            if ($read[$i] === $socket) {
                $conn = stream_socket_accept($socket);
                #fwrite($conn, "Connected. Local time is ".date("Y-m-d H:i:s a")."\n");
                $master[] = $conn;
            } else {
                $sock_data = unserialize(fread($read[$i], 1024));
                if (empty($sock_data)) { // connection closed
                    $key_to_del = array_search($read[$i], $master, TRUE);
                    fclose($read[$i]);
                    unset($master[$key_to_del]);
                } else if ($sock_data === FALSE) {
                    echo "Something bad happened";
                    $key_to_del = array_search($read[$i], $master, TRUE);
                    unset($master[$key_to_del]);
                } else {
                    echo "The client has sent :"; print_r($sock_data);
                    #fwrite($read[$i], "You have sent :[".print_r($sock_data)."]\n");
                    fclose($read[$i]);
                    unset($master[array_search($read[$i], $master)]);
                }
            }
        }
    }
}
