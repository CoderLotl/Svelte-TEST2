<?php
use App\Model\Classes\Session;
use App\Model\Utilities\Log;

$session = new Session();
$sessionID = $session->getSessionFromCookie();

if($sessionID && $session->findSessionInDatabase($sessionID, DB_SQLITE_PATH))
{    
    $session->updateSessionCookie($sessionID);
    $session->updateSessionInDatabase($sessionID, DB_SQLITE_PATH);    
}

?>