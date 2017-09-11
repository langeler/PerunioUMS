<?php

class Pages extends Obj {

	private $defaultPages = array();

	public function __construct() {

		parent::__construct();

		$this->defaultPages = array(
				'index' => array(
					'private' => 0,
					'isAllowedToBePublic' => true
				),
				'login' => array(
					'private' => 0,
					'isAllowedToBePublic' => true,
					'isAllowedToBePrivate' => false
				),

				'forgot-password' => array(
					'private' => 0,
					'isAllowedToBePublic' => true
				),
				'recover-password' => array(
					'private' => 0,
					'isAllowedToBePublic' => true
				),
				'register' => array(
					'private' => 0,
					'isAllowedToBePublic' => true
				),
				'resend-activation' => array(
					'private' => 0,
					'isAllowedToBePublic' => true
				),
				'account' => array(
					'private' => 1,
					'isAllowedToBePublic' => false
				),
				'admin_configuration' => array(
					'private' => 1,
					'isAllowedToBePublic' => false
				),
				'admin_pages' => array(
					'private' => 1,
					'isAllowedToBePublic' => false
				),
				'admin_permissions' => array(
					'private' => 1,
					'isAllowedToBePublic' => false
				),
				'admin_users' => array(
					'private' => 1,
					'isAllowedToBePublic' => false
				)
		);
	}

	public function checkForNewPages() {

		$pages = $this->defaultPages;

		$savedPages = $this->getDb()->getList('pages',array('name','id'));

		$availablePages =	 Utils::getDirectoryFiles(PAGES_DIR);

		foreach ($availablePages as $p) {
			if(!isset($pages[$p])){
				$pages[$p] = array(
					'private' => 0
				);
			}
		}

		//save new pages if found
		foreach ($pages as $p => $v) {

			if(isset($savedPages[$p])) {
				continue;
			}

			$pid = $this->getDb()->insert('pages',array(
					'name' => $p,
					'private' => $v['private']
				));

			if($pid) {

				//add admin permission to new pages
				Permissions::make()->assignPermissionToPage(1,$pid);
			}
		}

		$savedPages = $this->getDb()->getList('pages',array('name','id'));

		foreach ($savedPages as $pn => $id) {

			if(!isset($pages[$pn])) {

				//delete unused page
				$res = $this->getDb()->delete('pages',array(
					'where' => array(
						'name' => $pn
					)
				));

				//delete all permissions associated with this page
				if($res) {

					$this->getDb()->query(

						sprintf('DELETE FROM %spages_permissions WHERE page_id = ? ',
							$this->getDb()->getPrefix()
						),
						array(
							$id
						)
					);
				}
			}
		}

		return $this->getAll();
	}

	public function getAll($type = null) {

		if($type === 'list') {
			return $this->getDb()->getList('pages',array('id','name'));
		}

		return $this->getDb()->get('pages',
			array(
				'order' => array(
					'id',
					'asc'
				)
			)
		);
	}

	public function get($value, $field = 'id') {

		return $this->getDb()->getOne('pages',
			array(
				'where' => array(
					$field => $value
				)
			)
		);
	}

	public function isAllowedToBePrivate($id) {

		$page = $this->get($id);

		if(empty($page)){
			return null;
		}

		$pageName = $page['name'];

		if( isset($this->defaultPages[$pageName],$this->defaultPages[$pageName]['isAllowedToBePrivate']) ){
			return $this->defaultPages[$pageName]['isAllowedToBePrivate'];
		}

		return true;
	}

	public function isAllowedToBePublic($id) {

		$page = $this->get($id);

		if(empty($page)) {
			return null;
		}

		$pageName = $page['name'];

		if( isset($this->defaultPages[$pageName],$this->defaultPages[$pageName]['isAllowedToBePublic']) ){
			return $this->defaultPages[$pageName]['isAllowedToBePublic'];
		}

		return true;
	}
}