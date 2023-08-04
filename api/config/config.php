<?php
//site name
define('SITE_NAME', 'Simple Chat');

//App Root
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', '/');
define('URL_SUBFOLDER', '');

//DB Params
define('DB_HOST', 'your-host');
define('DB_USER', 'your-username');
define('DB_PASS', 'your-password');
define('DB_NAME', 'your-db-name');

// DISABLED DUE TO BEING RESTful
//ini_set('session.gc_maxlifetime', 3600); //sessions are cleaned every 1 hour
//session_start();

?>