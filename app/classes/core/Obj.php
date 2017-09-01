<?php

class Obj {

	/**
	 * @var DB
	 */
	protected $db;

	/**
	 * @var Template
	 */
	protected $tpl;

	private static $instances = array();

	public static function make($cache=true){

		$className = get_called_class();

		if(!$cache){
			return new static();
		}

		if(!isset(self::$instances[$className])){
			self::$instances[$className] = new static();
		}

		return self::$instances[$className];
	}

	public function __construct() {

		$this->db = DB::make();
		$this->tpl = Template::make();

		//avoid undefined indexes warnings errors
		if(Utils::isPost()){
			$_POST['data'] = isset($_POST['data']) ? $_POST['data'] : array();
		}
	}

	/**
	 * @return array
	 */
	public static function getInstances() {
		return self::$instances;
	}

	/**
	 * @return Template
	 */
	public function getTpl() {
		return $this->tpl;
	}

	/**
	 * @param Template $tpl
	 */
	public function setTpl($tpl) {
		$this->tpl = $tpl;
	}

	/**
	 * @return DB
	 */
	public function getDb() {
		return $this->db;
	}

	/**
	 * @param DB $db
	 */
	public function setDb($db) {
		$this->db = $db;
	}
}