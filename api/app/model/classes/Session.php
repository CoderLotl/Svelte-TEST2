<?php
namespace App\Model\Classes;

use PDO;
use App\Model\Utilities\Log;
use Exception;

class Session
{
    /////////////////////////////////////////////////////////////
    #region - - - COOKIES - - -
    /**
     * COOKIES
     * Returns the session from the cookies if it exists.
     * @return int|null
     */
    public function getSessionFromCookie()
    {
      if (isset($_COOKIE[SESSION_COOKIE_NAME]) && !empty($_COOKIE[SESSION_COOKIE_NAME])) {
        return intval($_COOKIE[SESSION_COOKIE_NAME]);
      }
      return null;
    }

    /**
     * COOKIES
     * Updates or creates the session cookie on the client.
     * @param mixed $session_id
     * 
     * @return bool
     */
    public function updateSessionCookie($sessionId)
    {      
      $expirationTime = time() + SESSION_EXPIRATION_SECONDS;
      return setcookie(SESSION_COOKIE_NAME, $sessionId, $expirationTime, "/", ".localhost", false, true);      
    }

    /**
     * COOKIES
     * Deletes the session cookie on the client.
     * @return bool
     */
    public function deleteSessionCookie()
    {
      return setcookie(SESSION_COOKIE_NAME, '', time() - 3600, '/');
    }
    #endregion

    /////////////////////////////////////////////////////////////
    #region - - - SESSION - - -
    /**
     * SESSION
     * Creates a session in the DB.
     * @param string $sessionId
     * @param string $userId
     * @param string $path
     * 
     * @return void
     */
    public function createSession($sessionId, $userId, $path)
    {
        $userIP = $_SERVER['REMOTE_ADDR'];
        $userHost = gethostbyaddr($userIP);
        $time = date('Y-m-d H:i:s');

        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("INSERT INTO 
                                sessions 
                                (id, user_id, lasttime, user_ip, user_host) 
                                VALUES 
                                (:id, :user_id, :lasttime, :user_ip, :user_host)"
        );
        $stm->bindParam(':id', $sessionId);
        $stm->bindParam(':user_id', $userId);
        $stm->bindParam(':lasttime', $time);
        $stm->bindParam(':user_ip', $userIP);
        $stm->bindParam(':user_host', $userHost);
        $stm->execute();
    }

    /**
     * SESSION
     * Updates an existing session in the DB. Only affects at the 'lasttime' column in order to extend the session
     * life time.
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return void
     */
    public function updateSessionInDatabase($sessionId, $path)
    {
        $time = date('Y-m-d H:i:s');
        try
        {
          $pdo = new PDO('sqlite:'.$path);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);          
          $stm = $pdo->prepare("UPDATE sessions SET lasttime = :lasttime WHERE id = :sessionId");
          $stm->bindParam(':lasttime', $time);
          $stm->bindParam(':sessionId', $sessionId);
          $stm->execute();          
        }
        catch(Exception $e)
        {
          Log::WriteLog('SessionsError.txt', $e->getMessage() . date('Y-m-d H:i:s'));
        }
    }

    /**
     * SESSION
     * Provided a session ID, looks for the session in the DB.
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return bool
     */
    public function findSessionInDatabase($sessionId, $path)
    {
      try
      {
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE id = :id");
        $stm->bindParam(":id", $sessionId);
        $stm->execute();        
        return $stm->fetchColumn() > 0;
      }
      catch(Exception $e)
      {
        Log::WriteLog('findSessionInDatabase.txt', $e->getMessage());
      }
    }

    /**
     * SESSION
     * Returns the session's player ID, provided the session exists.
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return int|false
     */
    public function findSessionPlayer($sessionId, $path)
    {
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("SELECT user_id FROM sessions where id = :id");
        $stm->bindParam(":id", $sessionId);
        $stm->execute();
        return $stm->fetchColumn();
    }

    public function findSessionPlayerName($sessionId, $path)
    {
      $pdo = new PDO('sqlite:'.$path);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stm = $pdo->prepare("SELECT name FROM users where id = :id");
      $stm->bindParam(":id", $sessionId);
      $stm->execute();
      return $stm->fetchColumn();
    }
  
    /**
     * SESSION
     * Deletes a session from the DB.
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return bool
     */
    public function deleteSessionFromDatabase($sessionId, $path)
    {
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("DELETE FROM sessions WHERE id = :id");
        $stm->bindParam(":id", $sessionId);
        return $stm->execute();
    }
  
    /**
     * Deletes sessions from the DB which expiration date is older than the given date.
     * Example: $expirationTimestamp = date('Y-m-d H:i:s');
     * @param string $expirationTimestamp date('Y-m-d').
     * @param string $path
     * 
     * @return void
     */
    public function deleteExpiredSessions($expirationTimestamp, $path)
    {
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("DELETE FROM sessions WHERE lasttime < :expirationTime");
        $stm->bindParam(":expirationTime", $expirationTimestamp);
        $stm->execute();
    }

    /**
     * SESSION
     * Generates a random session ID.
     * @param mixed $path
     * 
     * @return int
     */
    public function generateID($path)
    {
      try
      {
        while (true) {
          $remaddr = $_SERVER['REMOTE_ADDR'];        
    
          // 4 byte suffix of session id is based on player's IP to eliminate session conflicts
          $sessionIdSuffix = intval(sprintf("%u", ip2long($remaddr)));
    
          $new_id = (1 << 32) * hexdec(sprintf("%X%X%X%X", mt_rand(0, 127), mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255))) + $sessionIdSuffix;
          $new_id = intval(sprintf("%u", $new_id));          
  
          $pdo = new PDO('sqlite:' . $path);
          $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          
          $stm = $pdo->prepare("SELECT id FROM sessions WHERE id = :sessionID LIMIT 1");
          $stm->bindParam(':sessionID', $new_id);
          $stm->execute();
          $idAlreadyExists = $stm->fetchColumn();
          
          if (!$idAlreadyExists) {            
            return $new_id;
          }
        }
      }
      catch(Exception $e)
      {
        Log::WriteLog('SessionErrors.txt', $e->getMessage() . " " . date('Y-m-d H:i:s'));
      }
    }
}
?>