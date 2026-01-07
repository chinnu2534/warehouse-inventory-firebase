<?php
/*
|--------------------------------------------------------------------------
| Database Configuration - Render Compatible
|--------------------------------------------------------------------------
*/

// Render uses standard environment variable names
$db_host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: getenv('MYSQLPASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'oswa_inv';

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);

?>