<?php


class Installer extends Obj {


	public function checkIfUserAllowedToAccess() {

		$page = trim(Router::currentRoute(false),'/');
		$page = trim($page);

		if($page == ''){
			$page = 'index';
		}

		$pageDetails = Pages::make()->get($page,'name');

		if($pageDetails['private'] <= 0) {
			return true;
		}

		if(!Utils::isUserLoggedIn()) {
			throw new unauthorizedException;
		}

		$current_user_id = Utils::currentUserInfo('id');

		if($this->isAdmin($current_user_id)) {
			return true;
		}

		if(!$this->isUserAuthorisedToAccessPage($current_user_id,$pageDetails['id'])) {
			throw new unauthorizedException;
		}
	}

	public function isUserAuthorisedToAccessPage($user_id, $page_id) {
		$q =
'SELECT pu.id FROM %table_prefix%permissions_users AS pu INNER JOIN %table_prefix%permissions AS p
ON (pu.permission_id = p.id ) INNER JOIN %table_prefix%pages_permissions AS pp
ON (pp.permission_id = p.id ) WHERE pu.user_id = ? AND pp.page_id = ?';

		$q = str_replace('%table_prefix%',$this->getDb()->getPrefix(),$q);

		$res = $this->getDb()->query($q,array(
			$user_id,
			$page_id
		));

		return !empty($res);
	}

	public function userHasPermission($permission_id, $user_id) {

		$per = $this->getDb()->getOne('permissions_users',array(
			'where' => array(
				'permission_id' => $permission_id,
				'user_id' => $user_id
			)
		));

		return !empty($per);
	}


	public function assignPermissionToUser($permission_id, $user_id) {

		if(!$this->userHasPermission($permission_id, $user_id)) {
			$pid = $this->getDb()->insert('permissions_users',array(
					'permission_id' => $permission_id,
					'user_id' => $user_id,
			));

			return $pid;
		}

		return true;
	}

	public function removeUserPermissions($user_id) {

		$permissions = $this->getUserPermissions($user_id);

		foreach ($permissions as $per) {

			//don't remove admin permission from the first user (aka from admin)
			if($user_id == 1 && $per['id'] == 1) {
				continue;
			}

			if($per['active'] == 1) {
				$this->getDb()->delete('permissions_users',array(
					'where' => array(
						'user_id' => $user_id,
						'permission_id' => $per['id'],
					)
				));
			}
		}
	}

	/* ======= pages permissions ===== */
	public function pageHasPermission($permission_id, $page_id) {

		$per = $this->getDb()->getOne('pages_permissions',array(
			'where' => array(
				'permission_id' => $permission_id,
				'page_id' => $page_id
			)
		));

		return !empty($per);
	}

	public function assignPermissionToPage($permission_id, $page_id) {

		if(!$this->pageHasPermission($permission_id, $page_id)) {

			$pid = $this->getDb()->insert('pages_permissions',array(
					'permission_id' => $permission_id,
					'page_id' => $page_id,
			));

			return $pid;
		}

		return true;
	}

	public function removePagePermissions($page_id) {

		$permissions = $this->getPagePermissions($page_id);

		foreach ($permissions as $per) {

			//don't remove admin permission
			if($per['id'] == 1){
				continue;
			}

			if($per['active'] == 1) {

				$this->getDb()->delete('pages_permissions',array(
					'where' => array(
						'page_id' => $page_id,
						'permission_id' => $per['id'],
					)
				));
			}
		}
	}

	public function isAdmin($user_id) {

		// first user is admin
		if($user_id == 1) {
			return true;
		}

		$p = $this->getDb()->getOne('permissions_users',array(
			'where' => array(
				'user_id' => $user_id,
				'permission_id' => 1
			)
		));

		return !empty($p);
	}

	public function getAll($type = null) {

		if($type === 'list') {
			return $this->getDb()->getList('permissions',array('id','name'));

		}

		return $this->getDb()->get('permissions');
	}

	public function update($name,$id) {

		 return $this->getDb()->update('permissions',array(
					'name' => $name
				),array(
					'where' => array(
						'id' => $id
					)
				)
			);
	}

	public function add($name = null) {

		return $this->getDb()->insert('permissions',array(
				'name' => $name
			));
	}

	public function get($value, $field = 'id') {

		return $this->getDb()->getOne('permissions',array(
				'where' => array(
					$field => $value
				)
			));
	}

	public function getUserPermissions($user_id = null) {

		$permissions = $this->getAll('list');
		$permissionsIds = array_keys($permissions);
		$output = $permissionsUserList =  array();

		$permissionsUser = $this->getDb()->query(
			sprintf('SELECT * FROM %s WHERE user_id = %s AND permission_id IN (%s)',
				$this->getDb()->getPrefix() . 'permissions_users',
				(int)$user_id,
				implode(',',$permissionsIds)
			)
		);

		foreach ($permissionsUser as $k => $pu) {
			$permissionsUserList[$pu['permission_id']] = 1;
		}

		foreach ($permissions as $k => $p) {
			$i = array(
				'id' => $k,
				'name' => $p,
				'active' => 0
			);

			if(isset($permissionsUserList[$k])) {
				$i['active'] = 1;
			}

			$output[] = $i;
		}

		return $output;
	}

	public function getPagePermissions($page_id = null) {

		$permissions = $this->getAll('list');
		$permissionsIds = array_keys($permissions);
		$output = $permissionsPageist =	 array();

		$permissionsUser = $this->getDb()->query(
			sprintf('SELECT * FROM %s WHERE page_id = %s AND permission_id IN (%s)',
				$this->getDb()->getPrefix() . 'pages_permissions',
				(int)$page_id,
				implode(',',$permissionsIds)
			)
		);

		foreach ($permissionsUser as $k => $pp) {
			$permissionsPageList[$pp['permission_id']] = 1;
		}


		foreach ($permissions as $k => $p) {
			$i = array(
				'id' => $k,
				'name' => $p,
				'active' => 0
			);

			if(isset($permissionsPageList[$k])){
				$i['active'] = 1;
			}

			$output[] = $i;
		}

		return $output;
	}
}