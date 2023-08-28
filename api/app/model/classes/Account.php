<?php
namespace App\Model\Classes;

use PDO;
use Exception;
use App\Model\Classes\DataAccess;
use App\Model\Classes\Session;
use App\Model\Utilities\Log;

class Account
{
    public function CreateAccount($path)
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

    public function Login($path)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $dataAccess = new DataAccess();
        $session = new Session();
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
                    $sessionCookie = $session->getSessionFromCookie();
                    if(!$sessionCookie) //if the session cookie doesn't exist ...
                    { // we get the user's ID, create a new session, and cookie.
                        $userId = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$data['user']], $path);                                                
                        $sessionID = $session->generateID($path);                        
                        $session->createSession($sessionID, $userId, $path);                        
                        $session->updateSessionCookie($sessionID);                        
                    }
                    else
                    {                        
                        if($session->findSessionInDatabase($sessionCookie, $path))
                        {   // if the session cookie exists and matches an existing session ...
                            $session->updateSessionCookie($sessionCookie);
                            $session->updateSessionInDatabase($sessionCookie, $path);
                            // we update both the cookie and the session.                            
                        }
                        else
                        {   // but if the session in the cookie doesn't match ...
                            $session->deleteSessionCookie();
                            // we delete the cookie and create both a new session and cookie for it.
                            $userId = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$data['user']], $path);
                            $sessionID = $session->generateID($path);
                            $session->createSession($sessionID, $userId, $path);
                            $session->updateSessionCookie($sessionID);                            
                        }
                    }
                    
                    $this->UpdateUserData($data['user'], $path);
                    $this->RegisterNewLogin($data['user'], $path);
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
            Log::WriteLog('AccountErrors.txt', $e->getMessage() . " " . date('Y-m-d'));            
        }
    }

    public function Logout($path)
    {
        $session = new Session();
        $sessionID = $session->getSessionFromCookie($path);
        if(!$sessionID || !$session->findSessionInDatabase($sessionID, $path))
        {
            return false;            
        }
        $session->deleteSessionFromDatabase($sessionID, $path);        
        $session->deleteSessionCookie();
        return true;
    }

    public function ValidateSession($path)
    {
        $session = new Session();
        $sessionID = $session->getSessionFromCookie();
        if(!$sessionID || !$session->findSessionInDatabase($sessionID, $path))
        {            
            return false;
        }        
        $session->updateSessionCookie($sessionID);        
        $session->updateSessionInDatabase($sessionID, $path);        
        return true;
    }

    private function RegisterNewLogin($userName, $path)
    {
        try
        {
            $dataAccess = new DataAccess();            

            $userID = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$userName], $path);
            $userIP = $_SERVER['REMOTE_ADDR'];
            $userHost = gethostbyaddr($userIP);
            $time = date('Y-m-d H:i:s');            

            $pdo = new PDO('sqlite:' . $path);
    
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stm = $pdo->prepare("INSERT INTO users_logins (id, name, ip, hostname, login_date) VALUES ( ?, ?, ?, ?, ?)");
            $stm->execute([$userID, $userName, $userIP, $userHost, $time]);
        }
        catch(Exception $e)
        {
            Log::WriteLog('AccountErrors.txt', $e->getMessage() . " " . date('Y-m-d H:i:s'));            
        }
    }

    private function UpdateUserData($userName, $path)
    {
        try
        {
            $dataAccess = new DataAccess();            

            $userID = $dataAccess->GetSingleColumn('users', 'id', ['name'], [$userName], $path);
            $userIP = $_SERVER['REMOTE_ADDR'];
            $userHost = gethostbyaddr($userIP);
            $time = date('Y-m-d H:i:s');
            
            $pdo = new PDO('sqlite:' . $path);
    
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stm = $pdo->prepare("UPDATE users SET ip = ?, hostname = ?, lasttime = ? WHERE id = ?");
            $stm->execute([$userIP, $userHost, $time, $userID]);            
        }
        catch(Exception $e)
        {
            Log::WriteLog('AccountErrors.txt', $e->getMessage() . " " . date('Y-m-d H:i:s'));            
        }
    }
}
?>