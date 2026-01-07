<?php
/*
|--------------------------------------------------------------------------
| OWSA-INV V2 - Railway Compatible
|--------------------------------------------------------------------------
| Database configuration using environment variables for Railway deployment.
*/

// Use environment variables with fallbacks for local development
$db_host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS') ?: '';
$db_name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'oswa_inv';

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);

?>