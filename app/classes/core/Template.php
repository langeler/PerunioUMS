<?php

class Template {

	public $vars = array();
	public $layout = 'default';
	private $_blocks = array();
	private $templateDir = null;
	private $_ext = 'php';
	private $_activeBlocks = null;
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

	public function __construct($viewsDir = null) {

		if(!is_null($viewsDir)) {
			$this->templateDir = $viewsDir;
		}
	}

	public function fetch($name = '',$default = '') {

		if(isset($this->_blocks[$name])) {
			return $this->_blocks[$name];
		}

		return $default;
	}

	public function getVar($name, $default = null) {

		if(isset($this->vars[$name])) {
			return $this->vars[$name];
		}

		return $default;
	}

	public function setVar($name, $value = null) {

		if(is_array($name)) {
			$this->vars = $name + $this->vars;
		}

		else {
			$this->vars[$name] = $value;
		}
	}

	public function setBlock($name = '',$content='') {

		if(!isset($this->_blocks[$name])) {
			$this->_blocks[$name] = '';
		}

		$this->_blocks[$name] .= $content;
	}

	public function element($name = '',$default ='') {

		$elementPath = $this->templateDir . '/elements/' . $name . '.' . $this->_ext;

		if(file_exists($elementPath)) {

			ob_start();

			include $elementPath;

			return ob_get_clean();
		}

		return $default;
	}

	public function startBlock($blockName = '') {

		$this->_activeBlocks[] = $blockName;

		ob_start();
	}

	public function endBlock() {

		if(!empty($this->_activeBlocks)){

			$activeBlock = end($this->_activeBlocks);

			array_pop($this->_activeBlocks);

			$this->setBlock($activeBlock,ob_get_clean());
		}
	}

	public function _includeFile($viewPath, $vars = array()) {

		extract($vars);

		if(file_exists($viewPath)) {
			include $viewPath;
		}

		else {

			throw new Exception(
				'View not found ('. str_replace(
					substr(
						$this->templateDir,
						0,
						strrpos($this->templateDir,'/')
					),
					'',
					$viewPath
				) .')'
			);
		}
	}

	public function getTemplatePath($tplName) {
		return $this->templateDir . '/' . $tplName . '.' . $this->_ext;
	}

	public function templateExists($tplName = null) {
		return file_exists($this->getTemplatePath($tplName));
	}

	public function render($view = false, array $vars = array(), $layout = null,$echo = true) {

		$this->vars = $vars + $this->vars;

		if(is_null($layout)){
			$layout = $this->layout;
		}

		if($view !== false){

			$this->startBlock('content');

			$view = rtrim($view,'/');
			$view = trim($view);
			$_viewPath = $this->templateDir . '/' . $view . '.' . $this->_ext;

			$this->_includeFile($_viewPath,$this->vars);

			$this->endBlock();
		}

		ob_start();

		if($layout !== false) {

			$_layoutPath = $this->templateDir . '/layouts/' . $layout . '.' . $this->_ext;

			$this->_includeFile($_layoutPath,$this->vars);
		}

		else {
			echo $this->fetch('content');
		}

		if($echo) {
			echo ob_get_clean();

		}

		else {
			return ob_get_clean();
		}
	}

	/**
	 * @return null|string
	 */
	public function getTemplateDir() {
		return $this->templateDir;
	}

	/**
	 * @param null|string $templateDir
	 */
	public function setTemplateDir($templateDir) {
		$this->templateDir = $templateDir;
	}

	/**
	 * @return string
	 */
	public function getLayout() {
		return $this->layout;
	}

	/**
	 * @param string $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}

	/**
	 * @return string
	 */
	public function getExt() {
		return $this->_ext;
	}

	/**
	 * @param string $ext
	 */
	public function setExt($ext) {
		$this->_ext = $ext;
	}
}