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
if($data['isForm'] === true)
{
    Log::WriteLog($file, 'password: ' . $data['password']);    
    $password = $cryptoManager::Encode($data['password']);
    $data['password'] = $password;
    Log::WriteLog($file, 'password encryptado: ' . $data['password']);
}
else
{
    Log::WriteLog($file, 'user: ' . $data['user']);
    $user = $cryptoManager::Decode($data['user']);
    $data['user'] = $user;
    Log::WriteLog($file, 'user des-encryptado: ' . $data['user']);
}

Log::WriteLog($file, 'crypto pasado');

$columns = ['name', 'password'];
$values = [$data['user'], $data['password']];

try
{
    $path = APP_ROOT . '/app/database/Database.db';
    $userExists = $dataAccess->Find('users', $columns, $values, $path);
    Log::WriteLog($file, 'busqueda realizada. Resultado: ' . ($userExists ? 'si' : 'no' ));
    if ($userExists)
    {   
        $content = [$cryptoManager->Encode($data[0]), $data[1]];
        return $response->withStatus(200)->withJson($content);
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