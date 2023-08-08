<?php
namespace App\Model\Utilities;

use App\Model\Classes\DataAccess;

$db = new DataAccess();

if($db->Find('users', ['name'], ['admin'], APP_ROOT . '/app/database/Database.db'))
{
    $data = 'Base de datos funcionando.';
}
else
{
    $data = 'La base de datos no está funcionando.';
}

?>