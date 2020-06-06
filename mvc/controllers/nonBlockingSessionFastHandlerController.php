<?php
class nonBlockingSessionFastHandlerController extends base{}
$title='non blocking session fast handler';
require_once 'z/header.php';
?><hr>
/**
 * Main Goal : avoid blocking session_start(); #on badly written websites where every requests loads a frontcontroller using session_start foreach request, especially where images, thumbnails, js and css stuff are served via this php frontcontroller, once having cascade locked loading ressources times which is catastrophic
 *
 * session_set_save_handler() => could be sqlite, could be redis hashmap, could be json serialized files ..
 * writes at shutdown ( reading if file has been modified in between, locks only while writing file, removing lock pretty fast then .. )
 * Ideally each $_SESSION['key'] is a distinct file : aka "SESSID_keya.session", writen in "/dev/shm/sessions" or disk or whatever so each "Session" parameter could be read / written appart ..
 * using isset($k,$v); and sget($k);
 */
