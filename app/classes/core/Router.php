<?php

class Router {

	public static $routes = array();

	public static function url($path='/',$fullBase=false,$rewrite=null){

		if(empty($path)){
			$path = '/';
		}

		if(is_null($rewrite)){
			$rewrite = isset($_GET['REWRITE_ENABLED']);
		}

		if($rewrite){
			return Utils::baseUrl($fullBase) .'/'. trim($path,'/');
		}

		elseif($path == '/'){
			$path = '';
		}

		return (Utils::baseUrl($fullBase) . '/index.php' . $path);
	}

	/**
	 * @param bool $base
	 * @param bool $include_query_string
	 * @return string
	 */
	public static function currentRoute($base=true,$include_query_string = false) {

		$scriptName = $_SERVER['SCRIPT_NAME'];
		$requestUri =  urldecode($_SERVER['REQUEST_URI']);

		$queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

		$queryString = preg_replace('#\&?REWRITE\_ENABLED\=1\&?#i','',$queryString);

		if (strpos($requestUri, $scriptName) !== false) {
			$physicalPath = $scriptName;
		}

		else {
			$physicalPath = str_replace('\\', '', dirname($scriptName));
		}

		$pathInfo = $requestUri;

		if (substr($requestUri, 0, strlen($physicalPath)) == $physicalPath) {
			$pathInfo = substr($requestUri, strlen($physicalPath));
		}

		$pathInfo = str_replace('?' . $queryString, '', $pathInfo);
		$pathInfo = '/' . ltrim($pathInfo, '/');

		return (($base ? Utils::baseUrl() : '') . $pathInfo);
	}

	public static function connect($path,$callback,$options=array()) {

		if($path == ''){
			return;
		}

		$path = self::fixPath($path);
		$pathKey = md5($path);

		if(array_key_exists($pathKey,self::$routes)){
			return;
		}

		if(substr($path,-1) !== '/'){
			$path .= '/';
		}

		if(substr($path,-1) !== '?'){
			$path .= '?';
		}

		if(isset($options['path'])){
			unset($options['path']);
		}

		if(isset($options['callback'])){
			unset($options['callback']);
		}

		self::$routes[$pathKey] = (
			array(
				'path'=>$path,
				'callback'=>$callback
			)+
			array(
				'options'=>$options
			)
		);
	}

	public static function group($prefix, $routes = array()) {

		foreach ($routes as $route => $callback) {
			self::connect('/'.$prefix.$route,$callback);
		}
	}

	public static function find() {

		$currentRote = self::currentRoute(false);

		foreach (self::$routes as $route) {

			$regx = '#^'. $route['path'] .'$#';

			if(preg_match($regx,$currentRote,$args) === 1){

				array_shift($args);

				return array(
					'route'=>$route,
					'args'=>$args,
					'options'
				);

				break;
			}
		}

		return null;
	}

	public static function fixPath($path = '') {

		$path = str_replace(':id','([0-9]+)',$path);
		$path = str_replace(':slug','((?:(?!/).)+)',$path);
		$path = str_replace('**','(.*)',$path);

		return $path;
	}

	public static function getRoutes() {
		return self::$routes;
	}
}