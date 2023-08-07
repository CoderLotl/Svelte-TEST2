<?php
namespace App\Model\Utilities;

use App\Model\Classes\DataAccess;
use App\Model\Classes\CryptoManager;
use Exception;

$dataAccess = new DataAccess();
$cryptoManager = new CryptoManager();

$data = json_decode(file_get_contents('php://input'));

if($data['isForm'] === true)
{           
    $data[1] = $cryptoManager->Encode($data[1]);
}
else
{
    $data[0] = $cryptoManager->Decode($data[0]);
}

$columns = ['username', 'password'];
$values = [$data['user'], $data['password']];

try
{
    $path = APP_ROOT . '/app/database/Database.db';
    $userExists = $dataAccess->Find($table, $columns, $values, $path);

    if ($userExists)
    {   
        $content = [$cryptoManager->Encode($data[0]), $data[1]];
        return $response->withStatus(200)->withJson($content);
    }
    else
    {        
        return $response->withStatus(401);
    }
}
catch (Exception $e)
{    
    return $response->withStatus(500)->withJson(['error' => $e->getMessage()]);
}

?>