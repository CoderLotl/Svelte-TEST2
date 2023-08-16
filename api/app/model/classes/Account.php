<?php
namespace App\Model\Classes;

use PDO;
use Exception;
use App\Model\Classes\DataAccess;
use App\Model\Classes\Session;
use App\Model\Utilities\Log;

class Account
{
    public static function CreateAccount($path)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);        
        $userIP = $_SERVER['REMOTE_ADDR'];
        $userHost = gethostbyaddr($userIP);
        $time = date('Y-m-d H:i:s');

        $pdo = new PDO('sqlit:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("INSERT INTO users
                        (name, password, lasttime, ip, hostname, registerd)
                        VALUES (:name, $password, $time, $userIP, $userHost, $time)"
        );
        $stm->bindParam(':name', $data['user']);

        return $stm->execute();
    }

    public static function Login($path)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $dataAccess = new DataAccess();
        $columns = ['name'];
        $values = [$data['user']];       

        try
        {            
            $userExists = $dataAccess->Find('users', $columns, $values, $path);
            
            if ($userExists)
            {                
                $userEncryptedPass = $dataAccess->GetSingleColumn('users', 'password', ['name'], [$data['user']], $path);
                
                if(password_verify($data['password'], $userEncryptedPass))
                {                    
                    $session = Session::getSessionFromCookie();
                    if(!$session) //if the session cookie doesn't exist ...
                    { // we get the user's ID, create a new session, and cookie.
                        $userId = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$data['user']], $path);                                                
                        $sessionID = Session::generateID($path);                        
                        Session::createSession($sessionID, $userId, $path);                        
                        Session::updateSessionCookie($sessionID);                        
                    }
                    else
                    {
                        if(Session::findSessionInDatabase($session, $path))
                        {   // if the session cookie exists and matches an existing session ...
                            Session::updateSessionCookie($session);
                            Session::updateSessionInDatabase($session, $path);
                            // we update both the cookie and the session.                            
                        }
                        else
                        {   // but if the session in the cookie doesn't match ...
                            Session::deleteSessionCookie();
                            // we delete the cookie and create both a new session and cookie for it.
                            $userId = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$data['user']], $path);
                            $sessionID = Session::generateID($path);
                            Session::createSession($sessionID, $userId, $path);
                            Session::updateSessionCookie($sessionID);                            
                        }
                    }
                    
                    Account::UpdateUserData($data['user'], $path);
                    return true; //either way we return true since the user has logged in correctly.                    
                }
                else
                {
                    return false;                    
                }
            }
            else
            {        
                return false;                
            }
        }
        catch (Exception $e)
        {    
            die($e);
            Log::WriteLog('AccountErrors.txt', $e->getMessage() . " " . date('Y-m-d'));
        }
    }

    public static function Logout($path)
    {
        $session = Session::getSessionFromCookie($path);
        if(!$session || !Session::findSessionInDatabase($session, $path))
        {
            return false;
        }

        Session::deleteSessionCookie();
        Session::deleteSessionFromDatabase($session, $path);
        return true;
    }

    public static function ValidateSession($path)
    {
        $session = Session::getSessionFromCookie();
        if(!$session || !Session::findSessionInDatabase($session, $path))
        {
            return false;
        }

        Session::updateSessionCookie($session);
        Session::updateSessionInDatabase($session, $path);
        return true;
    }

    private static function UpdateUserData($userName, $path)
    {
        try
        {
            $dataAccess = new DataAccess();            

            $userID = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$userName], $path);
            $userIP = $_SERVER['REMOTE_ADDR'];
            $userHost = gethostbyaddr($userIP);
            $time = date('Y-m-d H:i:s');

            $userIPsJSON = $dataAccess->GetSingleColumn('users', 'ip', ['id'], [$userID], $path);
            $userHostsJSON = $dataAccess->GetSingleColumn('users', 'hostname', ['id'], [$userID], $path);
    
            $userIPs = json_decode($userIPsJSON);
            $userHosts = json_decode($userHostsJSON);
    
            if (!in_array($userIP, $userIPs)) {
                $userIPs[] = $userIP;
            }
            if (!in_array($userHost, $userHosts)) {
                $userHosts[] = $userHost;
            }
    
            $userIPsJSON = json_encode($userIPs);
            $userHostsJSON = json_encode($userHosts);
    
            $pdo = new PDO('sqlite:' . $path);
    
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stm = $pdo->prepare("UPDATE users SET ip = ?, hostname = ?, lasttime = ? WHERE id = ?");
            $stm->execute([$userIPsJSON, $userHostsJSON, $time, $userID]);            
        }
        catch(Exception $e)
        {
            Log::WriteLog('AccountErrors.txt', $e->getMessage() . " " . date('Y-m-d'));
        }
    }
}
?>