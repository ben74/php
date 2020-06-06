<?php
/*
or guzzle promise ?? perform real async FPC on message received
function accept(): Promise{}
while ($client = yield $server->accept()) {
*/
class asyncUpdServerController extends base{}

if(1){
    #https://www.php.net/manual/fr/function.stream-socket-server.php
    $socket = stream_socket_server('udp://0.0.0.0:1983', $errno, $errstr, STREAM_SERVER_BIND);
    if (!$socket) {echo "ERROR: $errno - $errstr<br />\n";} else {
        while ($conn = stream_socket_accept($socket)) {
            echo"z";
            fwrite($conn, date("D M j H:i:s Y\r\n"));
            fclose($conn);
        }
        fclose($socket);
    }
    _die('end');
}

$address = '127.0.0.1';
$port = 1983;
$timeout = 1;
$usecTimeout = 0;
$dl = 2048;
$killed = 0;
#echo "I am here";
set_time_limit(0);


if (false == ($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
    echo "could not create socket";
}
#Setting the socket timeout microseconds ('usec') does not work under Windows, at least under PHP/5.2.9:
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => $timeout, 'usec' => $usecTimeout]);
socket_bind($socket, $address, $port) or die ("could not bind socket");
#
socket_listen($socket);

#accepts postdata, put files for the workers to process
while (!$killed) {
    // Setup clients listen socket for reading
    $read[0] = $sock;
    for ($i = 0; $i < $max_clients; $i++)
    {
        if (isset($client[$i]))
            {if ($client[$i]['sock']  != null)
                {$read[$i + 1] = $client[$i]['sock'] ;}}
    }
    // Set up a blocking call to socket_select()
    $ready = socket_select($read, $write = null, $except = null, $tv_sec = null);
    $tot='';
    /* if a new connection is being made add it to the client array */
    if (in_array($sock, $read)) {
        for ($i = 0; $i < $max_clients; $i++)
        {
            if (!isset($client[$i])) {
                $client[$i] = array();
                $client[$i]['sock'] = socket_accept($sock);
                echo("Accepting incoming connection...\n");
                break;
            }
            elseif ($i == $max_clients - 1) {
                print ("too many clients");
            }
        }
        if (--$ready <= 0) {continue;}
    } // end if in_array

    // If a client is trying to write - handle it now
    for ($i = 0; $i < $max_clients; $i++){ // for each client gets 2048 bytes of body
        if (isset($client[$i])){
            if (in_array($client[$i]['sock'] , $read)) {
                #$tot .= $buf = socket_read($msgsock, 2048, PHP_BINARY_READ);#not line per line : block per block ..
                $tot.=$input = socket_read($client[$i]['sock'] , 2048, PHP_BINARY_READ);
                if ($input == null) {
                    // Zero length string meaning disconnected
                    echo("Client disconnected\n");
                    socket_close($client[$i]['sock']);
                    unset($client[$i]);
                }
                $n = trim($input);
                if ($n == 'exit') {
                    echo("Client requested disconnect\n");
                    // requested disconnect
                    socket_close($client[$i]['sock']);
                }
                if(substr($n,0,3) == 'say') {
                    //broadcast
                    echo("Broadcast received\n");
                    for ($j = 0; $j < $max_clients; $j++) // for each client
                    {
                        if (isset($client[$j]))
                            {if ($client[$j]['sock']) {
                                socket_write($client[$j]['sock'], substr($n, 4, strlen($n)-4).chr(0));
                            }}
                    }
                } elseif ($input) {
                    echo("Returning stripped input\n");
                    // strip white spaces and write back to user
                    $output = ereg_replace("[ \t\n\r]","",$input).chr(0);
                    socket_write($client[$i]['sock'],$output);
                }
            } else {
                // Close the socket
                if (isset($client[$i])) {
                    echo("Client disconnected\n");
                }
                if ($client[$i]['sock'] != null){
                    socket_close($client[$i]['sock']);
                    unset($client[$i]);
                }
            }
        }
    }
    echo $tot;
    continue;#old ways..
}
_die('old');
while (!$killed) {
while (!$killed) {
#while ($client = yield $server->accept()) {
        if (($msgsock = socket_accept($socket)) === false) {
            echo "socket_accept() a échoué : raison : " . socket_strerror(socket_last_error($socket)) . "\n";
            break;
        }
        #Response
        if (0) {
            $msg = "\Bienvenue sur le serveur de test PHP.\nPour quitter, tapez 'quit'. Pour éteindre le serveur, tapez 'shutdown'.\n";
            socket_write($msgsock, $msg, strlen($msg));
        }
        echo "\nServing new connection :\n";
        $receptionStarts = time();
        $tot = '';
        $bytes = 0;

        if ((time() - $receptionStarts) > $timeout) {
            echo "\nTimed out with transmission : $bytes";
            break;
        }

        if (0 and 'clien waiting for response') {
            #MSG_PEEK loops, MSG_OOB:nothing, MSG_DONTWAIT && nothing:instantanely killed
            #$ret = socket_recv($msgsock, $buf, $dl);
            if ($ret === false) {
                echo "\nempty ended with $bytes";
                break;
            }
        }

        $tot .= $buf = socket_read($msgsock, 2048, PHP_BINARY_READ);#not line per line : block per block ..
        #$r = socket_recv($msgsock, $buf, 2048);
        if (false === ($buf)) {
            if (socket_last_error($msgsock) == 10060 and 'timedout') {
                $a = 'timeout';
            } else {
                echo "\nsocket_read() a échoué : raison : " . socket_strerror(socket_last_error($msgsock)) . "\n";
            }
            break;#Fin des données transmises = Réponse logique
        }

        #$buf = socket_read($msgsock, 2048, PHP_BINARY_READ);#
        if (0 and socket_last_error($socket) > 0) {
            echo 'Unable to read from socket: ' . socket_strerror(socket_last_error($socket));
            @socket_clear_error($socket);
            break;
        }

        if (!$buf) {
            $empty++;
            if ($empty > 1) {
                echo "\nempty ended with " . $bytes;
                break;
            }
        }

        if ($buf === '') {
            echo "\nended " . $bytes;
            break;
        }

        if (strlen($buf)) {
            if (strlen($buf) > $dl) {
                echo "\nabove limits";
            }
            $empty = 0;
            $bytes += strlen($buf);
        }
        elseif ($buf == "\n") {
            echo "\n£";
        } elseif (!$buf = trim($buf)) {#empty line ?
            echo "\nµ";
            continue;#loops
        } elseif (!trim($buf)) {
            echo "\n...";
        } elseif ($buf == 'quit') {#\nquit at end of transmission
            break;
        } elseif ($buf == 'shutdown') {#plain body command to server :: shutdown
            $killed = 1;
            #socket_close($msgsock);
            break 2;#ends everythin
        } else {
            echo strlen($buf).',';
            #echo $buf;#regular line of text
        }
    }
    #yield $socket->end("HTTP/1.1 200 OK\r\nConnection: close\r\nContent-Length: {$bodyLength}\r\n\r\n{$body}");
    if($tot){
        $rep='ok:'.strlen($tot);
        socket_write($msgsock,$rep,strlen($rep));
        #socket_write($tot);
        echo "\nTotlen:".strlen($tot);
        echo "\n".$tot;
        #do something with the transmission
    }
    socket_close($msgsock);
}
die('#' . __line__);
return;?>


while ($notTerminated) {
    if ($client = socket_accept($socket)) {
        $msg = "\Bienvenue sur le serveur de test PHP.\n" .
            "Pour quitter, tapez 'quit'. Pour éteindre le serveur, tapez 'shutdown'.\n";
        socket_write($client, $msg, strlen($msg));


        $read = socket_read($socket);
        $msg = date('Ymdhis');
        socket_send($socket, $msg, strlen($msg));
        print_r(compact('client', 'read'));
        #print_r($_POST);
    }
}

socket_close($socket);
