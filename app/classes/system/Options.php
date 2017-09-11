<?php

class Options extends Obj {

	public function get($name = null, $default = null) {

		$name = strtolower($name);
		$name = trim($name);

		$opt = $this->getDb()->getOne('options',
			array(
				'where' => array(
					'name' => $name
				)
			)
		);

		if(empty($opt)) {
			return $default;
		}

		return $opt['value'];
	}

	public function getAll($type = null) {

		if($type === 'list') {
			return $this->getDb()->getList('options',array('name','value'));
		}

		return $this->getDb()->get('options');
	}

	public function set($name = null, $value = null) {

		$name = strtolower($name);
		$name = trim($name);

		$opt = $this->get($name,false);

		if($opt === false) {

			$this->getDb()->insert('options',array(
				'name' => $name,
				'value' => $value
			));
		}

		else {

			$this->getDb()->update('options',
				array(
					'value' => $value
				),
				array(
					'where' => array(
						'name' => $name
					)
				)
			);
		}
	}
}