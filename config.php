<?php

//Note: This file should be included first in every php page.
error_reporting(E_ALL);
ini_set('display_errors', 'On');
define('BASE_PATH', dirname(__FILE__));
define('ADMIN_FLDR', '/');
define('BASE_PATH_ADMIN', $_SERVER['DOCUMENT_ROOT'].ADMIN_FLDR);
define('APP_FOLDER', dirname(dirname(__FILE__)));
define('CURRENT_PAGE', basename($_SERVER['REQUEST_URI']));

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $url = "https://";   
} else {
	$url = "http://";   
}
define('MAIN_DOMAIN', $url.$_SERVER['HTTP_HOST']);
define('BASE_URI', $url.$_SERVER['HTTP_HOST']);

require_once 'lib/MysqliDb.php';
require_once 'helpers/helpers.php';

/*
|--------------------------------------------------------------------------
| DATABASE CONFIGURATION
|--------------------------------------------------------------------------
 */

define('DB_HOST', "localhost");
define('DB_USER', "rudebetorka");
define('DB_PASSWORD', "yE6tX3sF1l");
define('DB_NAME', "rudebetorka");

/**
 * Get instance of DB object
 */
function getDbInstance() {
	return new MysqliDb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
}