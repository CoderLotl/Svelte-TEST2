<?php

namespace App\Model\Utilities;

use App\Model\Classes\DataAccess;

$db = new DataAccess();

if($db->Find('users', ['name'], ['admin'], APP_ROOT . '/app/database/Database.db'))
{
    $data = 'Usuario existe';
}
else
{
    $data = 'No existe';
}

?>