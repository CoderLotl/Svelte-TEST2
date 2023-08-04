<?php
namespace App\Model\Utilities;

use App\Model\Classes\DataAccess;
use Exception;

$dataAccess = new DataAccess();

$username = $request->getParam('username');
$password = $request->getParam('password');

$columns = ['username', 'password'];
$values = [$username, $password];

try
{
    $path = APP_ROOT . '/app/database/Database.db';
    $userExists = $dataAccess->Find($table, $columns, $values, $path);

    if ($userExists)
    {    
        return $response->withStatus(200);
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