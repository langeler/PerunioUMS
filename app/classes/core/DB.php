<?php


class DB {

	/**
	 * @var \PDO
	 */
	private $pdo;

	private $connected = false;

	private $prefix = '';

	private $_debug = array();

	private static $instances = null;

	public static function make($cache = true) {

		if(!$cache) {
			return new static();
		}

		if(is_null(self::$instances)) {
			self::$instances = new static();
		}

		return self::$instances;
	}

	public function __construct($init = true) {

		if($init)
			$this->_init();
	}

	private function _init() {
		if(
			!defined('DB_HOST') ||
			!defined('DB_NAME') ||
			!defined('DB_USERNAME') ||
			!defined('DB_PASSWORD') ||
			!defined('DB_PREFIX')

		){
			throw new InternalException('Database configuration is missing.');
		}

		$this->prefix = DB_PREFIX;

		$dns = sprintf("mysql:host=%s;dbname=%s",DB_HOST,DB_NAME);

		try {
			$this->pdo = new \PDO($dns, DB_USERNAME, DB_PASSWORD,
				array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

			$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

			//mark the current DB instance as not connected
			$this->connected = true;
		}

		catch (\Exception $e) {
			$this->connected = false;
			self::$instances = new static(false);
			throw new InternalException('Cannot connect to database. <br><small>' .
				$e->getMessage() . '</small>');
		}
	}

	private function _buildWhere($where = array()) {

		$_where = array(
			'OR' => array(),
			'AND' => array()
		);

		$_params = array();

		$q = '';

		//reorganize
		foreach ($where as $k => $v) {

			if($k == 'OR') {
				$_where['OR'] = $v + $_where['OR'];

			}

			elseif($k == 'AND') {
				$_where['AND'] = $v + $_where['AND'];

			}

			else{
				$_where['AND'] = array($k => $v) + $_where['AND'];
			}
		}

		//AND portion
		$_and = '';

		foreach ($_where['AND'] as $k => $v) {

			$v = (!is_scalar($v) ? null : $v);

			if(is_scalar($k)){

				$_and .= ($_and == '' ? '' : 'AND ') . $k . ' = ? ';
				array_push($_params,$v);
			}
		}

		$_and = trim($_and);

		//OR portion
		$_or = '';

		foreach ($_where['OR'] as $k => $v) {

			$v = (!is_scalar($v) ? null : $v);

			if(is_scalar($k)){
				$_or .= ($_or == '' ? '' : 'OR ') . $k . ' = ? ';
				array_push($_params,$v);
			}
		}

		$_or = trim($_or);

		if($_and != '') {
			$q .= sprintf('( %s )', $_and);
		}

		if($_or != ''){

			if($q != ''){
				$q .= ' AND ';
			}

			$q .= sprintf('( %s )', $_or);
		}

		if($q == '') {
			return array();
		}

		return array(
			'q' => $q,
			'params' => $_params
		);
	}

	/**
	 * @param array|number $limit
	 * @return string
	 */

	private function _buildLimit($limit = array()) {

		if(is_array($limit)) {

			if(count($limit) >= 2) {
				return sprintf('LIMIT %s,%s ' , abs((int)$limit[0]) , abs((int)$limit[1]));
			}
		}

		if(is_scalar($limit)) {
			return sprintf('LIMIT %s ' , (int)$limit);
		}

		return '';
	}

	public function getList($table,$fields = array(),$options = array()) {

		if(empty($fields) || count($fields) < 2) {
			$fields = array('name','value');
		}

		$output = array();

		$res = $this->get($table,$options);

		foreach ($res as $k => $v) {

			if(isset($v[$fields[0]] , $v[$fields[1]])) {
				$output[$v[$fields[0]]] = $v[$fields[1]];
			}
		}

		return $output;
	}

	public function get($table,$options = array()) {

		$params = array();
		$q = 'SELECT ';

		if(!isset($options['fields'])) {
			$options['fields'] = '*';
		}

		if(is_array($options['fields']) && !empty($options['fields'])) {
			$q .= implode(' ,',$options['fields']);
		}

		elseif(is_string($options['fields'])) {
			$q .= $options['fields'];
		}

		else{
			$q .= '*';
		}

		$q .= ' FROM ' . $this->prefix . $table . ' ';

		if(isset($options['where'])) {

			$_where = $this->_buildWhere($options['where']);

			if(!empty($_where)) {

				$q .= 'WHERE ' . $_where['q'];
				$params = $_where['params'];
			}
		}

		if( isset($options['order']) && !empty($options['order']) && count($options['order']) > 1) {

			$q .= ' ORDER BY ' . addslashes($options['order'][0]) . ' ' . addslashes($options['order'][1]);

		}

		if(isset($options['limit'])) {
			$q .= ' ' . $this->_buildLimit($options['limit']);
		}

		return $this->query($q,$params);
	}

	public function getOne($table,$options = array()) {

		$options = array('limit' => 1) + $options;

		$res = $this->get($table,$options);

		if($res) {
			return $res[0];
		}

		return array();
	}

	public function query($q = '', $params = array()) {

		// Check if we have a working database connection
		if(!$this->connected) {
			return false;
		}

		$q = trim($q);

		$this->_debug[] = $q . ' [' . implode(', ',$params) . ']';

		if(!empty($params)) {

			$sth = $this->pdo->prepare($q);

			if($sth->execute($params)) {

				if(preg_match('/^SELECT.*/i',$q) == 1) {
					return $sth->fetchAll();
				}

				elseif(preg_match('/^INSERT.*/i',$q) == 1) {
					return $this->pdo->lastInsertId();
				}

				return true;
			}

			return false;
		}

		if(preg_match('/^(SELECT|SHOW).*/i',$q) == 1) {
			return $this->pdo->query($q)->fetchAll();
		}

		$sth = $this->pdo->prepare($q);
		return $sth->execute();
	}

	public function insert($table,$params) {

		$q = 'INSERT INTO ' . $this->prefix . $table;

		$fields = array_keys($params);
		$values = array_values($params);

		$q .= ' (`' . implode('`,`',$fields) . '`) VALUES ';

		$q .= '(' . implode(',',array_fill(0,count($fields),'?')) . ')';

		return $this->query($q,$values);
	}

	public function update($table,$params = array(),$options = array()) {

		if(!isset($options['limit'])) {
			$options['limit'] = 1;
		}

		$q = 'UPDATE ' . $this->prefix . $table . ' SET ';

		$fields = array_keys($params);
		$values = array_values($params);

		$q .=  implode(' = ? ,',$fields) . ' = ? ';

		if(isset($options['where'])) {

			$_where = $this->_buildWhere($options['where']);

			if(!empty($_where)){
				$q .= 'WHERE ' . $_where['q'];
				$values = array_merge($values,$_where['params']);
			}
		}

		if(isset($options['limit'])) {
			$q .= ' ' . $this->_buildLimit($options['limit']);
		}

		return $this->query($q,$values);
	}

	public function delete($table,$options = array()) {

		$q = 'DELETE FROM '.$this->prefix . $table;
		$params = array();

		if(isset($options['where'])) {

			$_where = $this->_buildWhere($options['where']);

			if(!empty($_where)) {
				$q .= ' WHERE ' . $_where['q'];
				$params = $_where['params'];
			}
		}

		$q .= ' ' . $this->_buildLimit(1);

		//prevent deletion without condition
		if(empty($params)) {
			return false;
		}

		return $this->query($q,$params);
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
	}


	public function debug($force = false) {

		if(!IS_DEBUG && !$force) {
			return false;
		}

		return $this->_debug;
	}
}