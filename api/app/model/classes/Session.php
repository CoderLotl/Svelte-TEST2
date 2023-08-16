<?php
namespace App\Model\Classes;

use PDO;
use App\Model\Utilities\Log;
use Exception;

class Session
{
    private $sessionId;
    private $userId;
    private $password;
    
    public function __construct($sessionId = null)
    {
        $this->sessionId = $sessionId;
    }        

    /////////////////////////////////////////////////////////////
    #region - - - COOKIES - - -
    /**
     * COOKIES
     * Returns the session from the cookies if it exists.
     * @return int|null
     */
    public static function getSessionFromCookie()
    {
      if (isset($_COOKIE[_SESSION_COOKIE_NAME]) && !empty($_COOKIE[_SESSION_COOKIE_NAME])) {
        return intval($_COOKIE[_SESSION_COOKIE_NAME]);
      }
      return null;
    }

    /**
     * COOKIES
     * Updates or creates the session cookie on the client.
     * @param mixed $session_id
     * 
     * @return void
     */
    public static function updateSessionCookie($session_id)
    {      
      $expirationTime = time() + SESSION_EXPIRATION_SECONDS;
      setcookie(_SESSION_COOKIE_NAME, $session_id, $expirationTime, "/", ".localhost", null, true);      
    }

    /**
     * COOKIES
     * Deletes the session cookie on the client.
     * @return [type]
     */
    public static function deleteSessionCookie()
    {
      setcookie(_SESSION_COOKIE_NAME, 0, time() - SESSION_EXPIRATION_SECONDS * 60);
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
    public static function createSession($sessionId, $userId, $path)
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
    public static function updateSessionInDatabase($sessionId, $path)
    {
        $time = date('Y-m-d H:i:s');
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("UPDATE session SET lasttime = :lasttime WHERE id = :sessionId");
        $stm->bindParam(':lasttime', $time);
        $stm->bindParam(':sessionId', $sessionId);
        $stm->execute();
    }

    /**
     * SESSION
     * Provided a session ID, looks for the session in the DB.
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return bool
     */
    public static function findSessionInDatabase($sessionId, $path)
    {
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE id = :id");
        $stm->bindParam(":id", $sessionId);
        $stm->execute();
        return $stm->fetchColumn() > 0;
    }

    /**
     * SESSION
     * Returns the session's player ID, provided the session exists.
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return int|false
     */
    public static function findSessionPlayer($sessionId, $path)
    {
        $pdo = new PDO('sqlite:'.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("SELECT user_id FROM sessions where id = :id");
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
    public static function deleteSessionFromDatabase($sessionId, $path)
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
    public static function deleteExpiredSessions($expirationTimestamp, $path)
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
    public static function generateID($path)
    {
      try
      {
        while (true) {
          //if we manipulate this - we need remember that url crypt system are related
          //with session key length.
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
        Log::WriteLog('SessionErrors.txt', $e->getMessage() . " " . date('Y-m-d'));
      }
    }
}
?>