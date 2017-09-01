<?php

class Container {

	private static $_objects = array();

	public static function set($name,$value=null){
		$name = ucfirst(strtolower($name));

		self::$_objects[$name] = $value;
	}

	public static function get($name, $default = null) {
		$name = ucfirst(strtolower($name));

		if(isset(self::$_objects[$name])){
			return self::$_objects[$name];
		}

		return $default;
	}

	/**
	 * @return array
	 */
	public static function getObjects() {
		return self::$_objects;
	}

	/**
	 * @param array $objects
	 */
	public static function setObjects($objects) {
		self::$_objects = $objects;
	}
}