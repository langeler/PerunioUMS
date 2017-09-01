<?php

ob_start();

if(!defined('IN_APP')){
	define('IN_APP', true);
}

if(!defined('IS_DEBUG')){
	define('IS_DEBUG', false);
}

if(!defined('APP_NAME')){
	define('APP_NAME', 'PerunioCMS');
}

if(!defined('APP_VER')){
	define('APP_VER', '1.0');
}

error_reporting(E_ALL);
ini_set('display_errors', (IS_DEBUG ? "1" : "0") );

if(!defined('SESSION_NAME')){
	define('SESSION_NAME', APP_NAME . '_session');
}

ini_set('session.name', SESSION_NAME );
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly',	'1');
ini_set('session.hash_function',	'1');

if(!function_exists('deepStripslashes')) {

	function deepStripslashes($value) {
		return is_array($value) ? array_map('deepStripslashes' , $value) : stripslashes($value);
	}
}

if(get_magic_quotes_gpc()){

	$_GET = deepStripslashes($_GET);
	$_POST = deepStripslashes($_POST);
	$_COOKIE = deepStripslashes($_COOKIE);
	$_REQUEST = deepStripslashes($_REQUEST);
}

if(!defined('ROOT_DIR')){
	define('ROOT_DIR',dirname(dirname(__FILE__)));
}

if(!defined('INC_DIR')){
	define('INC_DIR',ROOT_DIR . DIRECTORY_SEPARATOR . 'app');
}

if(!defined('CLASS_DIR')){
	define('CLASS_DIR',INC_DIR . DIRECTORY_SEPARATOR . 'classes');
}

if(!defined('LANGUAGE_DIR')){
	define('LANGUAGE_DIR',INC_DIR . DIRECTORY_SEPARATOR . 'languages');
}

if(!defined('PAGES_DIR')){
	define('PAGES_DIR',ROOT_DIR . DIRECTORY_SEPARATOR . 'pages');
}

if(!defined('TPL_DIR')){
	define('TPL_DIR',ROOT_DIR.DIRECTORY_SEPARATOR.'templates');
}

if(!defined('LIBS_DIR')){
	define('LIBS_DIR',INC_DIR . DIRECTORY_SEPARATOR . 'libs');
}

if(!defined('CONF_DIR')){
	define('CONF_DIR',INC_DIR . DIRECTORY_SEPARATOR . 'config');
}

if(!file_exists(CONF_DIR . DIRECTORY_SEPARATOR . 'database.php')){

	echo '<center><h1>Configuration file `<code>config.php</code>` not found</h1></center>';

	exit;
}

require CONF_DIR . DIRECTORY_SEPARATOR . 'database.php';
require INC_DIR . DIRECTORY_SEPARATOR . 'functions.php';

if(!defined('TIME_ZONE')){
	define('TIME_ZONE', 'GMT');
}

date_default_timezone_set(TIME_ZONE);

//start session
if(session_id() == ''){
	session_start();
}

//classes autoloader
if(!function_exists('PCMSAutoload')) {

	// Create the autoload function
	function PCMSAutoload($classname) {

		// set directories to load classes from
		$classDirectories = array(
			'core',
			'exceptions',
			'system',
			''
		);

		// loop through all the specefied directories
		foreach($classDirectories as $classDirectory){

			// Set file name variable
			$fullClassPath = CLASS_DIR . DIRECTORY_SEPARATOR . $classDirectory . DIRECTORY_SEPARATOR . $classname . '.php';

			// If the file is readable
			if (is_readable($fullClassPath)) {
				require $fullClassPath; // Require it
			}
		}
	}
}

// register autoloader
if (version_compare(PHP_VERSION, '5.1.2', '>=')) {

	//SPL autoloading was introduced in PHP 5.1.2
	if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		spl_autoload_register('PCMSAutoload', true, true);
	}

	else {
		spl_autoload_register('PCMSAutoload');
	}
}

else {

	/**
	 * Fall back to traditional autoload for old PHP versions
	 * @param string $classname The name of the class to load
	 */
	function __autoload($classname) {
		PCMSAutoload($classname);
	}
}

//PHPMailer
require LIBS_DIR . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'PHPMailerAutoload.php';

//set template dir
Template::make()->setTemplateDir(TPL_DIR);

//check if we need to install the script
if(!defined('IN_INSTALLER') && file_exists(ROOT_DIR . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'index.php')) {

	if(!checkIfInstalled()) {

		Utils::redirect(
			Router::url(
				'/',
				true,
				true
			).
			'install/index.php'
		);
	}
}

//continue only if we are not in installation page
if(!defined('IN_INSTALLER')) {

	//set website language
	setLanguage();

	// include default routers
	require INC_DIR . DIRECTORY_SEPARATOR . 'routes.php';
}