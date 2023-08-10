<?php
namespace App\Model\Utilities;

use App\Model\Classes\DataAccess;

$db = new DataAccess();

if($db->Find('users', ['name', 'password'], ['admin', 'QF0cQpKj2l91N7yPEBz7rGcyeU9BN2hIck1BOS9IY1g1cXVLaVE9PQ=='], APP_ROOT . '/app/database/Database.db'))
{
    $data = 'Base de datos funcionando.';
}
else
{
    $data = 'La base de datos no está funcionando.';
}

?>