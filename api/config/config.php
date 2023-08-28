<?php
//site name
define('SITE_NAME', 'Simple Chat');

//App Root
define('APP_ROOT', dirname(dirname(__FILE__)));

//DB Params
define('DB_HOST', 'your-host');
define('DB_USER', 'your-username');
define('DB_PASS', 'your-password');
define('DB_NAME', 'your-db-name');
define('DB_SQLITE_PATH', APP_ROOT . '/app/database/Database.db');

//Cookie Params
define('SESSION_COOKIE_NAME', '40d0228e409c8b711909680cba94881c');
define('SESSION_EXPIRATION_SECONDS', 60 * 60 * 24);

//Game Params
define('MAX_CHARACTERS', 5);


?>