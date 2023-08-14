<?php
namespace App\Model\Classes;

use PDO;

class Session
{
    private $sessionID;
    private $userIP;
    private $userHost;
    private $userID;
    private $password;
    
    public function __construct($sessionID = null)
    {
        $this->sessionID = $sessionID;
        $this->userIP = $_SERVER['REMOTE_ADDR'];
        $this->userHost = gethostbyaddr($this->userIP);
    }

    /////////////////////////////////////////////////////////////
    #region - - - COOKIES - - -
    public static function getSessionFromCookie()
    {
      if (isset($_COOKIE[_SESSION_COOKIE_NAME]) && !empty($_COOKIE[_SESSION_COOKIE_NAME])) {
        return intval($_COOKIE[_SESSION_COOKIE_NAME]);
      }
      return null;
    }

    public static function updateSessionCookie($session_id)
    {
      $expirationTime = time() + SESSION_EXPIRATION_SECONDS;
      setcookie(_SESSION_COOKIE_NAME, $session_id, $expirationTime, "/", null, null, true);
    }

    public static function deleteSessionCookie()
    {
      setcookie(_SESSION_COOKIE_NAME, 0, time() - SESSION_EXPIRATION_SECONDS * 60);
    }
    #endregion

    /////////////////////////////////////////////////////////////
    #region - - - SESSION - - -
    public static function sessionExistsInDatabase($sessionId, $path)
    {
        $pdo = new PDO('sqlit='.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("SELECT COUNT(*) FROM sessions WHERE id = :id");
        $stm->bindParam("id", $sessionId);
        return $stm->execute() > 0;
    }
  
    /**
     * @param mixed $sessionId
     * @param mixed $path
     * 
     * @return [type]
     */
    public static function deleteSessionFromDatabase($sessionId, $path)
    {
        $pdo = new PDO('sqlit='.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("DELETE FROM sessions WHERE id = :id");
        $stm->bindParam("id", $sessionId);
        $stm->execute();
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
        $pdo = new PDO('sqlite='.$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare("DELETE FROM sessions WHERE lasttime < :expirationTime");
        $stm->bindParam(":expirationTime", $expirationTimestamp);
        $stm->execute();
    }

    private function generateID($path)
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
        $stm->bindParam(':sessionID', $new_id, PDO::PARAM_INT);
        $idAlreadyExists = $stm->execute();
        if (!$idAlreadyExists) {
          return $new_id;
        }
      }
    }
}
?>