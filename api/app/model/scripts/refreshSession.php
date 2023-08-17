<?php
use App\Model\Classes\Session;
use App\Model\Utilities\Log;

$session = Session::getSessionFromCookie();

if($session && Session::findSessionInDatabase($session, DB_SQLITE_PATH))
{    
    Session::updateSessionCookie($session);
    Session::updateSessionInDatabase($session, DB_SQLITE_PATH);    
}

?>