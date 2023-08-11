<?php
namespace App\Model\Utilities\Login;

use App\Model\Classes\DataAccess;
use App\Model\Classes\CryptoManager;
use App\Model\Utilities\Log;
use Exception;

function Login()
{
    $dataAccess = new DataAccess();
    $cryptoManager = new CryptoManager();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if($data['isForm'] !== true)
    {    
        $password = $cryptoManager::Decode($data['password']);
        $data['password'] = $password;
        
        $user = $cryptoManager::Decode($data['user']);
        $data['user'] = $user;    
    }
    
    $columns = ['name'];
    $values = [$data['user']];
    
    try
    {
        $path = APP_ROOT . '/app/database/Database.db';
        $userExists = $dataAccess->Find('users', $columns, $values, $path);
        
        if ($userExists)
        {           
            $userEncryptedPass = $dataAccess->GetSingleColumn('users', 'password', ['name'], [$data['user']], $path);
            
            $userPass = $cryptoManager::Decode($userEncryptedPass);
            
            if($data['password'] === $userPass)
            {                
                $content = [$cryptoManager->Encode($data['user']), $cryptoManager->Encode($data['password'])];                
                return $content;
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
    }
}

?>