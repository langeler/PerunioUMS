<?php

function lang($key = null){

	$lang = Container::get('Language',false);

	if($lang === false){
		$lang = array();
	}

	if(isset($lang[$key])){
		return $lang[$key];
	}

	return $key;
}

function getOption($key = null, $default = null) {

	$key = strtolower($key);
	$key = trim($key);

	$_key = 'option_' . $key;
	$cached = Container::get($_key,false);

	if($cached === false) {

		$cached = Options::make()->get($key,false);

		if($cached === false) {
			$cached = $default;
		}

		Container::set($_key,$cached);
	}

	return $cached;
}

function setLanguage($default = null) {

	if(is_null($default)) {
		$default = getOption('website_language',$default);
	}

	$siteLanguage = $default;
	$langFile = LANGUAGE_DIR . DIRECTORY_SEPARATOR . $siteLanguage . '.php';

	if(file_exists($langFile)) {
		Container::set('Language',(require $langFile));
	}
}

function checkIfInstalled() {

	$dbTables = DB::make()->query('SHOW tables');
	$tablesPrefix = DB::make()->getPrefix();

	if (empty($dbTables)) {
		return false;
	}

	else {

		$defaultTables = array(
			'options',
			'pages',
			'pages_permissions',
			'permissions',
			'permissions_users',
			'users',
		);

		foreach ($defaultTables as $dTable) {

			$found = false;

			foreach ($dbTables as $dbTable) {

				$dbTableName = array_values($dbTable);
				$dbTableName = $dbTableName[0];

				if($dbTableName == $tablesPrefix.$dTable){
					$found = true;
					break;
				}
			}

			if(!$found) {
				return false;
			}
		}
	}

	return true;
}