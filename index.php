<?php

/*
 * PerunioCMS
 *
 */

// script name name
define('APP_NAME','PerunioUMS');

// script version
define('APP_VER','1.0');

// enable or disable debug mode
define('IS_DEBUG',false);

define('IN_APP',true);

define('ROOT_DIR',dirname(__FILE__));

define('INC_DIR',ROOT_DIR . DIRECTORY_SEPARATOR . 'app');

define('PAGES_DIR',ROOT_DIR . DIRECTORY_SEPARATOR . 'pages');

// prepare the environment and require all needed files
require INC_DIR . DIRECTORY_SEPARATOR . 'init.php';

App::run();

ob_end_flush();