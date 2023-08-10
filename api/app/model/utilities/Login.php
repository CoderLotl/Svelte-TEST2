<?php
namespace App\Model\Utilities;

use App\Model\Classes\DataAccess;
use App\Model\Classes\CryptoManager;
use App\Model\Utilities\Log;
use Exception;

$dataAccess = new DataAccess();
$cryptoManager = new CryptoManager();
$file = APP_ROOT . '/log.txt';
file_put_contents($file, '');

$data = json_decode(file_get_contents('php://input'), true);

Log::WriteLog($file, 'login data: ' . file_get_contents('php://input') );

Log::WriteLog($file, 'isForm: ' . $data['isForm']);
if($data['isForm'] !== true)
{
    Log::WriteLog($file, 'password: ' . $data['password']);    
    $password = $cryptoManager::Decode($data['password']);
    $data['password'] = $password;
    Log::WriteLog($file, 'password des-encryptado: ' . $data['password']);
    Log::WriteLog($file, 'user: ' . $data['user']);
    $user = $cryptoManager::Decode($data['user']);
    $data['user'] = $user;
    Log::WriteLog($file, 'user des-encryptado: ' . $data['user']);
}

Log::WriteLog($file, 'crypto pasado');

$columns = ['name'];
$values = [$data['user']];

try
{
    $path = APP_ROOT . '/app/database/Database.db';
    $userExists = $dataAccess->Find('users', $columns, $values, $path);
    Log::WriteLog($file, 'busqueda realizada. Resultado: ' . ($userExists ? 'si' : 'no' ));
    if ($userExists)
    {   
        Log::WriteLog($file, 'user existe');
        $userEncryptedPass = $dataAccess->GetSingleColumn('users', 'password', ['name'], [$data['user']], $path);
        Log::WriteLog($file, 'encrypted pass: ' . $userEncryptedPass);
        $userPass = $cryptoManager::Decode($userEncryptedPass);
        Log::WriteLog($file, 'decrypted pass: ' . $userPass);

        if($data['password'] === $userPass)
        {
            Log::WriteLog($file, 'exito');
            $content = [$cryptoManager->Encode($data[0]), $data[1]];
            return $response->withStatus(200)->withJson($content);
        }
        else
        {
            return $response->withStatus(400)->withJson(['error' => $e->getMessage()]);
        }
    }
    else
    {        
        return $response->withStatus(400)->withJson(['error' => $e->getMessage()]);
    }
}
catch (Exception $e)
{    
    return $response->withStatus(500)->withJson(['error' => $e->getMessage()]);
}

?>